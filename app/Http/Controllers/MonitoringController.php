<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Monitoring';
        $q = $request->query('q', '');

        $periodemonitorings = PeriodeMonitoring::with(['tahunKerja', 'periodeMonev'])
            ->whereHas('periodeMonev', function ($query) use ($q) {
                if ($q) {
                    $query->where('th_id', 'like', '%' . $q . '%');
                }
            })
            ->select('periode_monitoring.*')
            ->distinct()
            ->orderBy('periode_monev.pm_nama', 'asc')
            ->paginate(10);

        $no = $periodemonitorings->firstItem();

        return view('pages.index-monitoring', [
            'title' => $title,
            'periodemonitorings' => $periodemonitorings,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'monitoring',
        ]);
    }

    public function show($pmo_id)
    {
        $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

        $rencanaKerja = RencanaKerja::with(['monitoring' => function ($query) use ($pmo_id) {
            $query->where('pmo_id', $pmo_id);
        }, 'unitKerja'])
            ->whereHas('periodes', function ($query) use ($periodeMonitoring) {
                $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->periodeMonev->pluck('pm_id')->toArray());
            })
            ->get();

        foreach ($rencanaKerja as $rencana) {
            $rencana->is_monitored = $rencana->monitoring->isNotEmpty();
        }

        return view('pages.monitoring-show', [
            'periodeMonitoring' => $periodeMonitoring,
            'rencanaKerja' => $rencanaKerja,
            'type_menu' => 'monitoring',
        ]);
    }

    public function fill($pmo_id)
    {
        $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

        $rencanaKerja = RencanaKerja::with(['periodes', 'monitoring' => function ($query) use ($pmo_id) {
            $query->where('pmo_id', $pmo_id);
        }, 'unitKerja', 'realisasi'])
            ->whereHas('periodes', function ($query) use ($periodeMonitoring) {
                $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->periodeMonev->pluck('pm_id')->toArray());
            })
            ->get();

        $rencanaKerja->each(function ($rencana) {
            $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
        });

        $selectedPeriods = $rencanaKerja->flatMap(function ($rencana) {
            return $rencana->monitoring
                ->where('mtg_status', 'p')
                ->flatMap(function ($monitoring) {
                    return $monitoring->periodes()->pluck('periode_monev.pm_id');
                });
        })->unique()->toArray();

        $realisasi = RealisasiRenja::whereIn('rk_id', $rencanaKerja->pluck('rk_id'))->get();

        return view('pages.monitoring-fill', [
            'periodeMonitoring' => $periodeMonitoring,
            'rencanaKerja' => $rencanaKerja,
            'realisasi' => $realisasi,
            'selectedPeriods' => $selectedPeriods,
            'type_menu' => 'monitoring',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'mtg_capaian' => 'required|numeric',
            'mtg_kondisi' => 'required|string',
            'mtg_kendala' => 'nullable|string',
            'mtg_tindak_lanjut' => 'required|string',
            'mtg_tindak_lanjut_tanggal' => 'required|date',
            'mtg_status' => 'required|in:y,n,t,p',
            'mtg_bukti' => 'nullable|url',
            'mtg_flag' => 'required|boolean',
            'pmo_id' => 'required|exists:periode_monitoring,pmo_id',
            'rk_id' => 'required|exists:rencana_kerja,rk_id',
        ]);

        try {
            $monitoring = Monitoring::updateOrCreate(
                [
                    'pmo_id' => $request->pmo_id,
                    'rk_id' => $request->rk_id,
                ],
                $validatedData
            );

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'monitoring' => $monitoring,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan monitoring data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam menyimpan data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getData($pmo_id, $rk_id)
    {
        $monitoring = Monitoring::where('pmo_id', $pmo_id)->where('rk_id', $rk_id)->first();
        $realisasi = RealisasiRenja::where('rk_id', $rk_id)->orderBy('rkr_capaian', 'asc')->get();

        return response()->json([
            'monitoring' => $monitoring,
            'realisasi' => $realisasi,
        ]);
    }
}

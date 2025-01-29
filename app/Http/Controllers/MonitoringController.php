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
            ->join('periode_monitoring_periode_monev', 'periode_monitoring.pmo_id', '=', 'periode_monitoring_periode_monev.pmo_id')
            ->join('periode_monev', 'periode_monitoring_periode_monev.pm_id', '=', 'periode_monev.pm_id')
            ->select('periode_monitoring.*')
            ->distinct()
            ->orderBy('periode_monev.pm_nama', 'asc')
            ->paginate(10);

        $no = $periodemonitorings->firstItem();

        foreach ($periodemonitorings as $item) {
            $tanggalInput = Carbon::now();
            $tanggalSelesai = Carbon::parse($item->pmo_tanggal_selesai);
            $item = $tanggalInput >= $tanggalSelesai;
        }

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
                $query->where('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->pm_id);
            })->get();

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
    // Ambil semua periode monev terurut berdasarkan nama
    $periodes = periode_monev::orderBy('pm_nama')->get();
    
    // Ambil data periode monitoring beserta relasi
    $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

    // Ambil data rencana kerja yang sesuai dengan periode monitoring
    $rencanaKerja = RencanaKerja::with(['periodes', 'monitoring' => function ($query) use ($pmo_id) {
        $query->where('pmo_id', $pmo_id);
    }, 'unitKerja', 'realisasi'])
        ->whereHas('periodes', function ($query) use ($periodeMonitoring) {
            $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->periodeMonev->pluck('pm_id')->toArray());
        })
        ->get();

    // Tandai apakah rencana kerja telah memiliki monitoring
    $rencanaKerja->each(function ($rencana) {
        $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
    });

    // ID periode di mana "Perlu Tindak Lanjut" harus dihapus dari dropdown
    $restrictedIds = [
        'PM6DA104A10B4F0DED85F92F877AF01684', // Q3
        'PM0A1C8847BC9316A6FC058F47C1EC7682', // Q4
    ];

    // Tentukan apakah perlu menghapus opsi "Perlu Tindak Lanjut"
    $hideTindakLanjut = in_array($periodeMonitoring->periodeMonev->first()->pm_id, $restrictedIds);

    return view('pages.monitoring-fill', [
        'periodes' => $periodes,
        'periodeMonitoring' => $periodeMonitoring,
        'rencanaKerja' => $rencanaKerja,
        'hideTindakLanjut' => $hideTindakLanjut,
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
            $monitoring = Monitoring::firstOrCreate(
                [
                    'pmo_id' => $request->pmo_id,
                    'rk_id' => $request->rk_id,
                ],
                $validatedData
            );

            $monitoring->fill($validatedData);
            $monitoring->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'monitoring' => $monitoring
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan monitoring data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam menyimpan data.',
                'error' => $e->getMessage()
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

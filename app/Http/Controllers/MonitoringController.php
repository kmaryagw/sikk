<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use App\Models\monitoring_periode_monev;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        ->select('periode_monitoring.pmo_id', 'periode_monitoring.th_id', 'periode_monitoring.pmo_tanggal_mulai', 'periode_monitoring.pmo_tanggal_selesai', 'periode_monev.pm_nama') // Pilih kolom spesifik
        ->groupBy(
            'periode_monitoring.pmo_id',
            'periode_monitoring.th_id',
            'periode_monitoring.pmo_tanggal_mulai',
            'periode_monitoring.pmo_tanggal_selesai',
            'periode_monitoring.created_at',
            'periode_monitoring.updated_at',
            'periode_monev.pm_nama'
        )
        
        ->orderBy('periode_monev.pm_nama', 'asc')
        ->paginate(10);
    

        $no = $periodemonitorings->firstItem();

        // Tandai apakah periode monitoring sudah berakhir
        $periodemonitorings->each(function ($item) {
            $item->isExpired = Carbon::now()->greaterThanOrEqualTo(Carbon::parse($item->pmo_tanggal_selesai));
        });

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
        // Ambil Periode Monitoring beserta Tahun Kerja dan Periode Monev-nya
        $periodeMonitoring = PeriodeMonitoring::with(['tahunKerja', 'periodeMonev'])->findOrFail($pmo_id);

        // Ambil semua ID periode_monev terkait periode_monitoring
        $periodeMonevIds = $periodeMonitoring->periodeMonev->pluck('pm_id')->toArray();

        // Ambil semua Rencana Kerja yang terkait dengan periode_monev dari periode_monitoring
        $rencanaKerja = RencanaKerja::with([
                'unitKerja',
                'monitoring' => function ($query) use ($pmo_id) {
                    $query->where('pmo_id', $pmo_id);
                }
            ])
            ->whereHas('periodes', function ($query) use ($periodeMonevIds) {
                $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonevIds);
            })
            ->get();

        // Tandai apakah masing-masing rencana sudah dimonitoring
        foreach ($rencanaKerja as $rencana) {
            $rencana->is_monitored = $rencana->monitoring->isNotEmpty();
        }

        foreach ($rencanaKerja as $rencana) {
            \Log::info("Rencana Kerja: $rencana->rk_nama, Monitored: " . $rencana->monitoring->count());
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
        $periodeMonevIds = $periodeMonitoring->periodeMonev->pluck('pm_id');

        $rencanaKerjaQuery = RencanaKerja::with([
                'periodes',
                'monitoring' => fn ($query) => $query->where('pmo_id', $pmo_id),
                'unitKerja',
                'realisasi',
            ])
            ->whereHas('periodes', fn ($query) => $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonevIds));

        if (Auth::user()->role == 'unit kerja') {
            $rencanaKerjaQuery->whereHas('unitKerja', fn ($query) =>
                $query->where('unit_id', Auth::user()->unit_id)
            );
        }

        // Ambil data rencana kerja yang sudah difilter
        $rencanaKerja = $rencanaKerjaQuery->get();

        // Tambahkan flag
        $rencanaKerja->each(function ($rencana) {
            $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
        });

        // Buat mapping selected periode per rk_id
        $selectedPeriodes = $rencanaKerja->mapWithKeys(function ($rencana) {
            $monitoring = $rencana->monitoring->first();
            $selected = $monitoring ? $monitoring->periodes->pluck('pm_id')->toArray() : [];
            return [$rencana->rk_id => $selected];
        });

        // ID periode yang harus dihapus dari dropdown
        $restrictedIds = [
            'PM6DA104A10B4F0DED85F92F877AF01684', // Q3
            'PM0A1C8847BC9316A6FC058F47C1EC7682', // Q4
        ];

        $hideTindakLanjut = in_array(optional($periodeMonitoring->periodeMonev->first())->pm_id, $restrictedIds);

        return view('pages.monitoring-fill', [
            'periodes' => $periodes,
            'periodeMonitoring' => $periodeMonitoring,
            'rencanaKerja' => $rencanaKerja,
            'selectedPeriodes' => $selectedPeriodes,
            'hideTindakLanjut' => $hideTindakLanjut,
            'type_menu' => 'monitoring',
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Store monitoring request:', $request->all());
    
        // Validasi input
        $validatedData = $request->validate([
            'mtg_capaian'                => 'required|numeric',
            'mtg_kondisi'                => 'required|string',
            'mtg_kendala'                => 'nullable|string',
            'mtg_tindak_lanjut'          => 'required|string',
            'mtg_tindak_lanjut_tanggal'  => 'required|date',
            'mtg_status'                 => 'required|in:y,n,t,p',
            'mtg_bukti'                  => 'nullable|url',
            'mtg_flag'                   => 'required|boolean',
            'pmo_id'                     => 'required|exists:periode_monitoring,pmo_id',
            'rk_id'                      => 'required|exists:rencana_kerja,rk_id',
            'pm_id'                      => $request->mtg_status === 'p' ? 'required|array' : 'nullable|array',
            'pm_id.*'                    => 'exists:periode_monev,pm_id',
        ]);
    
        try {
            DB::beginTransaction(); // Mulai transaksi database
    
            // Cek apakah data monitoring sudah ada berdasarkan pmo_id & rk_id
            $monitoring = Monitoring::where('pmo_id', $validatedData['pmo_id'])
                ->where('rk_id', $validatedData['rk_id'])
                ->first();
    
            if ($monitoring) {
                // Jika data sudah ada, update
                $monitoring->update([
                    'mtg_capaian'             => $validatedData['mtg_capaian'],
                    'mtg_kondisi'             => $validatedData['mtg_kondisi'],
                    'mtg_kendala'             => $validatedData['mtg_kendala'] ?? null,
                    'mtg_tindak_lanjut'       => $validatedData['mtg_tindak_lanjut'],
                    'mtg_tindak_lanjut_tanggal' => $validatedData['mtg_tindak_lanjut_tanggal'],
                    'mtg_status'              => $validatedData['mtg_status'],
                    'mtg_bukti'               => $validatedData['mtg_bukti'] ?? null,
                    'mtg_flag'                => $validatedData['mtg_flag'],
                ]);
            } else {
                // Jika data belum ada, buat baru dengan UUID
                $monitoring = Monitoring::create([
                    'mtg_id'                  => (string) Str::uuid(),
                    'mtg_capaian'             => $validatedData['mtg_capaian'],
                    'mtg_kondisi'             => $validatedData['mtg_kondisi'],
                    'mtg_kendala'             => $validatedData['mtg_kendala'] ?? null,
                    'mtg_tindak_lanjut'       => $validatedData['mtg_tindak_lanjut'],
                    'mtg_tindak_lanjut_tanggal' => $validatedData['mtg_tindak_lanjut_tanggal'],
                    'mtg_status'              => $validatedData['mtg_status'],
                    'mtg_bukti'               => $validatedData['mtg_bukti'] ?? null,
                    'mtg_flag'                => $validatedData['mtg_flag'],
                    'pmo_id'                  => $validatedData['pmo_id'],
                    'rk_id'                   => $validatedData['rk_id'],
                ]);
            }
    
            // Setelah memastikan `mtg_id` ada, update tabel monitoring_periode_monev
            if ($validatedData['mtg_status'] === 'p' && !empty($validatedData['pm_id'])) {
                $monitoring->periodes()->sync($validatedData['pm_id']); // Update tanpa duplikasi
            } else {
                $monitoring->periodes()->detach(); // Hapus semua relasi periode jika status bukan 'p'
            }

            \Log::info('Saved Monitoring Data:', $monitoring->toArray());

       
            DB::commit(); // Simpan transaksi
    
            return response()->json([
                'success'    => true,
                'message'    => 'Data berhasil disimpan',
                'monitoring' => $monitoring->load('periodes')
            ], 200);
    
        } catch (\Throwable $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            \Log::error('Error saat menyimpan monitoring data:', ['error' => $e->getMessage()]);
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam menyimpan data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // public function lihatMonitoring()
    // {
    //     $rencanaKerja = RencanaKerja::with([
    //         'monitoring',
    //         'unitKerja',
    //         'periodes',
    //         'tahunKerja',
    //     ])->get();

    //     return view('monitoring.lihat', compact('rencanaKerja'));
    // }



public function getData($pmo_id, $rk_id)
{
    // Eager load relasi periodes dan realisasi
    $monitoring = Monitoring::with(['periodes', 'realisasi'])
        ->where('pmo_id', $pmo_id)
        ->where('rk_id', $rk_id)
        ->first();

    $realisasi = RealisasiRenja::where('rk_id', $rk_id)
        ->orderBy('rkr_capaian', 'asc')
        ->get();

    return response()->json([
        'monitoring' => $monitoring,
        'realisasi' => $realisasi,
    ]);
}
}

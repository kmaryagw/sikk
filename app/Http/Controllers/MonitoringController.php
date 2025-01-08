<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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

    // Periksa apakah data sudah dimuat
    $no = $periodemonitorings->firstItem();

    // Tambahkan logika pengecekan durasi periode
    foreach ($periodemonitorings as $item) {
        $tanggalMulai = Carbon::parse($item->pmo_tanggal_mulai);
        $tanggalSelesai = Carbon::parse($item->pmo_tanggal_selesai);

        $item->is_within_three_months = $tanggalMulai->diffInMonths($tanggalSelesai) <= 3;
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
    $periodes = periode_monev::orderBy('pm_nama')->get();
    $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

    $rencanaKerja = RencanaKerja::with(['periodes', 'monitoring' => function ($query) use ($pmo_id) {
        $query->where('pmo_id', $pmo_id);
    }, 'unitKerja', 'realisasi'])
    ->whereHas('periodes', function ($query) use ($periodeMonitoring) {
        $query->whereIn('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->periodeMonev->pluck('pm_id')->toArray());
    })
    ->get();

    // Tandai apakah rencana kerja sudah diisi monitoring-nya
    $rencanaKerja->each(function ($rencana) {
        $rencana->is_submitted = $rencana->monitoring->isNotEmpty();
    });

    // Ambil data realisasi
    $realisasi = RealisasiRenja::whereIn('rk_id', $rencanaKerja->pluck('rk_id'))->get();

    return view('pages.monitoring-fill', [
        'periodes' => $periodes,
        'periodeMonitoring' => $periodeMonitoring,
        'rencanaKerja' => $rencanaKerja,
        'realisasi' => $realisasi,
        'type_menu' => 'monitoring',
    ]);
}




public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'mtg_capaian' => 'required|numeric',
        'mtg_kondisi' => 'required|string',
        'mtg_kendala' => 'nullable|string',
        'mtg_tindak_lanjut' => 'required|string',
        'mtg_tindak_lanjut_tanggal' => 'required|date',
        'mtg_status' => 'required|in:y,n,t,p',
        'mtg_bukti' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        'mtg_flag' => 'required|boolean', 
        'pmo_id' => 'required|exists:periode_monitoring,pmo_id',
        'rk_id' => 'required|exists:rencana_kerja,rk_id',
    ]);

    try {
        // Menggunakan firstOrCreate untuk mencari atau membuat data baru
        $monitoring = Monitoring::firstOrCreate(
            [
                'pmo_id' => $request->pmo_id,
                'rk_id' => $request->rk_id,
            ],
            $validatedData // Jika tidak ditemukan, data akan diisi dengan nilai yang divalidasi
        );

        // Mengelola file bukti jika ada
        if ($request->hasFile('mtg_bukti')) {
            // Jika ada file bukti sebelumnya, hapus file yang lama
            if ($monitoring->mtg_bukti) {
                Storage::disk('public')->delete($monitoring->mtg_bukti);
            }
            // Menyimpan file baru
            $monitoring->mtg_bukti = $request->file('mtg_bukti')->store('monitoring_bukti', 'public');
        }

        // Simpan perubahan jika ada file yang diubah atau data lain yang perlu diperbarui
        $monitoring->fill($validatedData);
        $monitoring->save();

        // Mengembalikan respons dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'monitoring' => $monitoring
        ], 200); // Status code 200 untuk sukses

    } catch (\Exception $e) {
        // Log error untuk debugging
        Log::error('Error saat menyimpan monitoring data: ' . $e->getMessage());

        // Mengembalikan respons JSON jika terjadi error
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan dalam menyimpan data.',
            'error' => $e->getMessage()
        ], 500); // Status code 500 untuk kesalahan server
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

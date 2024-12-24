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
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Monitoring';
    $q = $request->query('q');

    // Query hanya menggunakan relasi Eloquent
    $periodemonitorings = PeriodeMonitoring::with(['tahunKerja', 'periodeMonev']) // Memuat relasi
        ->when($q, function ($query, $q) {
            $query->whereHas('periodeMonev', function ($subQuery) use ($q) {
                $subQuery->where('pm_nama', 'like', '%' . $q . '%');
            });
        })
        ->paginate(10);

    $no = $periodemonitorings->firstItem();

    // Menambahkan atribut untuk selisih bulan
    foreach ($periodemonitorings as $item) {
        $tanggalMulai = Carbon::parse($item->pmo_tanggal_mulai);
        $tanggalSelesai = Carbon::parse($item->pmo_tanggal_selesai);

        $selisihBulan = $tanggalMulai->diffInMonths($tanggalSelesai);

        $item->is_within_three_months = $selisihBulan <= 3;
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
        $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')
            ->findOrFail($pmo_id);

        $rencanaKerja = RencanaKerja::with(['monitoring' => function ($query) use ($pmo_id) {
            $query->where('pmo_id', $pmo_id);
        }, 'unitKerja'])
            ->whereHas('periodes', function ($query) use ($periodeMonitoring) {
                $query->where('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->pm_id);
            })->get();

        // Menandai apakah data monitoring sudah diisi atau belum untuk setiap rencana kerja
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
    // Ambil data periode monitoring berdasarkan ID
    $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')->findOrFail($pmo_id);

    // Cek selisih waktu antara tanggal mulai dan tanggal selesai
    $tanggalMulai = Carbon::parse($periodeMonitoring->pmo_tanggal_mulai);
    $tanggalSelesai = Carbon::parse($periodeMonitoring->pmo_tanggal_selesai);
    $selisihBulan = $tanggalMulai->diffInMonths($tanggalSelesai);

    // Jika lebih dari 3 bulan, redirect ke halaman 'monitoring-show'
    if ($selisihBulan > 3) {
        return redirect()->route('monitoring.show', ['pmo_id' => $pmo_id]);
    }

    // Query untuk mendapatkan Rencana Kerja yang terkait dengan periode monitoring ini
    $rencanaKerja = RencanaKerja::with([
        'monitoring' => function ($query) use ($pmo_id) {
            $query->where('pmo_id', $pmo_id);
        },
        'unitKerja'
    ])->whereHas('periodes', function ($query) use ($periodeMonitoring) {
        // Gunakan tabel pivot untuk mencocokkan periode monitoring dengan periode monev
        $query->join('periode_monitoring_periode_monev', 'periode_monitoring_periode_monev.pm_id', '=', 'rencana_kerja_pelaksanaan.pm_id')
              ->where('periode_monitoring_periode_monev.pmo_id', $periodeMonitoring->pmo_id);
    })->get();

    // Tandai apakah data monitoring sudah diisi atau belum
    foreach ($rencanaKerja as $rencana) {
        $monitoring = $rencana->monitoring->first();

        $rencana->is_submitted = $monitoring ? true : false; // Tandai jika monitoring diisi
        $rencana->mtg_status = $monitoring->mtg_status ?? 'n'; // Default ke 'n'
        $rencana->periode = $monitoring->periode ?? ''; // Default kosong jika belum ada
    }

    // Kembalikan view 'monitoring-fill' dengan data yang telah diproses
    return view('pages.monitoring-fill', [
        'periodeMonitoring' => $periodeMonitoring,
        'rencanaKerja' => $rencanaKerja,
        'type_menu' => 'monitoring',
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'pmo_id' => 'required|exists:periode_monitoring,pmo_id',
        'rk_id' => 'required|exists:rencana_kerja,rk_id',
        'mtg_status' => 'required|in:y,n', // Validasi status
        'periode' => 'nullable|string|required_if:mtg_status,y', // Periode wajib jika status 'y'
        'mtg_capaian' => 'required|numeric',
        'mtg_kondisi' => 'required|string',
        'mtg_kendala' => 'nullable|string',
        'mtg_tindak_lanjut' => 'nullable|string',
        'mtg_tindak_lanjut_tanggal' => 'nullable|date',
        'mtg_bukti' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
    ]);

    try {
        $monitoring = Monitoring::updateOrCreate(
            ['pmo_id' => $request->pmo_id, 'rk_id' => $request->rk_id],
            [
                'mtg_status' => $validated['mtg_status'],
                'periode' => $validated['periode'], // Simpan periode
                'mtg_capaian' => $validated['mtg_capaian'],
                'mtg_kondisi' => $validated['mtg_kondisi'],
                'mtg_kendala' => $validated['mtg_kendala'],
                'mtg_tindak_lanjut' => $validated['mtg_tindak_lanjut'],
                'mtg_tindak_lanjut_tanggal' => $validated['mtg_tindak_lanjut_tanggal'],
            ]
        );

        // Upload file jika ada
        if ($request->hasFile('mtg_bukti')) {
            if ($monitoring->mtg_bukti) {
                Storage::disk('public')->delete($monitoring->mtg_bukti);
            }
            $monitoring->mtg_bukti = $request->file('mtg_bukti')->store('monitoring_bukti', 'public');
        }

        $monitoring->save();

        Alert::success('Berhasil', 'Data monitoring berhasil disimpan.');
        return redirect()->route('monitoring.fill', ['pmo_id' => $request->pmo_id])
            ->with('success', 'Data monitoring berhasil disimpan.');
    } catch (\Exception $e) {
        Alert::error('Gagal', 'Terjadi kesalahan dalam menyimpan data.');
        return redirect()->route('monitoring.fill', ['pmo_id' => $request->pmo_id])
            ->with('error', 'Terjadi kesalahan dalam menyimpan data: ' . $e->getMessage());
    }
}



    public function getData($pmo_id, $rk_id)
    {
        $monitoring = Monitoring::where('pmo_id', $pmo_id)->where('rk_id', $rk_id)->first();
        $realisasi = RealisasiRenja::where('rk_id', $rk_id)
            ->orderBy('rkr_capaian', 'asc')
            ->get();

        $data = [$monitoring, $realisasi];

        return response()->json($data);
    }
}

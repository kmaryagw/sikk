<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Monitoring';
    $q = $request->query('q');

    // Ambil semua periode monitoring
    $periode_monitoring = PeriodeMonitoring::with(['tahunKerja', 'rencanaKerja' => function ($query) use ($q) {
        if ($q) {
            $query->where('rk_nama', 'like', '%' . $q . '%');
        }
    }, 'rencanaKerja.periodeMonev'])
    ->orderBy('pmo_tanggal_mulai', 'asc')
    ->get();

    // Loop untuk cek dan membuat data monitoring jika belum ada
    foreach ($periode_monitoring as $periode) {
        foreach ($periode->rencanaKerja as $rencanaKerja) {
            // Periksa apakah data monitoring sudah ada
            $existingMonitoring = Monitoring::where('pmo_id', $periode->pmo_id)
                ->where('rk_id', $rencanaKerja->rk_id)
                ->first();

            if (!$existingMonitoring) {
                // Buat data monitoring baru
                Monitoring::create([
                    'mtg_id' => uniqid(),
                    'pmo_id' => $periode->pmo_id,
                    'rk_id' => $rencanaKerja->rk_id,
                    'mtg_capaian' => null,
                    'mtg_kondisi' => null,
                ]);
            }
        }
    }

    // Ambil ulang data monitoring setelah sinkronisasi
    $groupedMonitoring = [];
    foreach ($periode_monitoring as $periode) {
        foreach ($periode->rencanaKerja as $rencanaKerja) {
            if ($rencanaKerja->periodeMonev) {
                foreach ($rencanaKerja->periodeMonev as $periodeMonev) {
                    $key = $periode->pmo_id;
                    $groupedMonitoring[$key] = [
                        'tahun' => $periode->tahunKerja ? $periode->tahunKerja->th_tahun : 'N/A',
                        'periode' => 'Q' . ceil($periode->pmo_tanggal_mulai->month / 3),
                        'tanggal_mulai' => $periode->pmo_tanggal_mulai,
                        'tanggal_selesai' => $periode->pmo_tanggal_selesai,
                        'rencana_kerja' => $rencanaKerja->rk_nama,
                        'is_within_period' => now()->between($periode->pmo_tanggal_mulai, $periode->pmo_tanggal_selesai),
                        'months_difference' => $periode->pmo_tanggal_mulai->diffInMonths($periode->pmo_tanggal_selesai)
                    ];
                }
            }
        }
    }

    return view('pages.index-monitoring', [
        'title' => $title,
        'groupedMonitoring' => $groupedMonitoring,
        'q' => $q,
        'type_menu' => 'monitoring',
    ]);
}
}

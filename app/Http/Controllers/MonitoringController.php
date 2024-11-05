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

    // Ambil data periode monitoring beserta tahun kerja dan rencana kerja
    $periode_monitoring = PeriodeMonitoring::with(['tahunKerja', 'rencanaKerja' => function ($query) use ($q) {
        if ($q) {
            $query->where('rk_nama', 'like', '%' . $q . '%');
        }
    }])->orderBy('pmo_tanggal_mulai', 'asc')->get();

    $groupedMonitoring = [];
    foreach ($periode_monitoring as $periode) {
        foreach ($periode->rencanaKerja as $rencanaKerja) {
            // Gunakan collect() untuk memastikan relasi periodes berupa Collection
            $periodes = collect($rencanaKerja->periodes);

            if ($periodes->isNotEmpty()) { // Sekarang menggunakan Collection
                foreach ($periodes as $periodeMonev) {
                    $key = $periode->pmo_id; // Gunakan pmo_id sebagai kunci
                    $groupedMonitoring[$key] = [
                        'tahun' => optional($periode->tahunKerja)->th_tahun ?? 'N/A',
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

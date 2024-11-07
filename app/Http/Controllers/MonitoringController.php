<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{

    public function index(Request $request)
{
    $title = 'Data Monitoring ';
    $q = $request->query('q');

    $periodemonitorings = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')
        ->join('periode_monev', 'periode_monitoring.pm_id', '=', 'periode_monev.pm_id')
        ->orderBy('periode_monev.pm_nama', 'asc')
        ->where('th_id', 'like', '%' . $q . '%')
        ->paginate(10);

    $no = $periodemonitorings->firstItem();

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

public function fill($pmo_id)
{
    // Ambil data periode monitoring beserta relasi tahunKerja dan periodeMonev
    $periodeMonitoring = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')
        ->findOrFail($pmo_id);

    // Dapatkan rencana kerja yang terkait dengan periode monev dari periode monitoring
    $rencanaKerja = RencanaKerja::whereHas('periodes', function ($query) use ($periodeMonitoring) {
        $query->where('rencana_kerja_pelaksanaan.pm_id', $periodeMonitoring->pm_id); // Pastikan menggunakan alias tabel
    })->get();

    return view('pages.monitoring-fill', [
        'periodeMonitoring' => $periodeMonitoring,
        'rencanaKerja' => $rencanaKerja,
        'type_menu' => 'monitoring',
    ]);
}



public function store(Request $request)
{
    $monitoring = new Monitoring();
    $monitoring->rk_id = $request->input('rk_id');
    $monitoring->pmo_id = $request->input('pmo_id');
    $monitoring->save();

    Alert::success('Berhasil', 'Data monitoring berhasil diisi');
    return redirect()->route('monitoring.index');
}

}

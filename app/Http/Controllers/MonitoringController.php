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

}

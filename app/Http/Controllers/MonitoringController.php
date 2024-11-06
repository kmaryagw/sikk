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

    // Ambil data periode monitoring beserta relasinya
    $periodemonitorings = PeriodeMonitoring::with('tahunKerja', 'periodeMonev')
        ->where('th_id', 'like', '%' . $q . '%')
        ->orderBy('th_id', 'asc')
        ->paginate(10);

    $no = $periodemonitorings->firstItem();

    // Hitung selisih bulan dan tambahkan property ke setiap item
    foreach ($periodemonitorings as $item) {
        $tanggalMulai = Carbon::parse($item->pmo_tanggal_mulai);
        $tanggalSelesai = Carbon::parse($item->pmo_tanggal_selesai);

        // Hitung selisih antara tanggal mulai dan tanggal selesai
        $selisihBulan = $tanggalMulai->diffInMonths($tanggalSelesai);

        // Jika selisih bulan lebih besar dari 3 bulan, set aksi ke 'Lihat Monitoring'
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

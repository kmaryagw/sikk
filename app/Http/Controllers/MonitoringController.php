<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MonitoringController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Monitoring';
    $q = $request->query('q');

    $monitorings = RencanaKerja::with('tahunKerja', 'unitKerja')
        ->where('rk_nama', 'like', '%' . $q . '%')
        ->orderBy('rk_nama', 'asc')
        ->paginate(10);

        
    $no = $monitorings->firstItem();
    $periode_monitoring = PeriodeMonitoring::paginate(10);

    // Misalnya Anda ingin memeriksa rentang tanggal untuk setiap monitoring
    foreach ($monitorings as $monitoring) {
        $startDate = $monitoring->pmo_tanggal_mulai;
        $endDate = $monitoring->pmo_tanggal_selesai;

        if ($startDate && $endDate) {
            // Lakukan logika yang Anda butuhkan, misalnya
            // Cek apakah tanggal tertentu berada dalam rentang ini
            if ($someDate->between($startDate, $endDate)) {
                // Lakukan sesuatu jika $someDate berada dalam rentang
            }
        } else {
            // Tangani situasi jika salah satu tanggal adalah null
            // Misalnya, simpan pesan ke dalam array atau log
        }
    }

    return view('pages.index-monitoring', [
        'title' => $title,
        'monitorings' => $monitorings,
        'q' => $q,
        'no' => $no,
        
        'periode_monitoring' => $periode_monitoring,
        'type_menu' => 'realisasirenja',
    ]);
}

    // public function showRealisasi($rk_id)
    // {
    //     $rencanaKerja = RencanaKerja::findOrFail($rk_id);
    
    //     // Mengambil realisasi dan mengurutkannya berdasarkan tanggal dibuat
    //     $monitoring = Monitoring::where('rk_id', $rk_id)->orderBy('created_at', 'asc')->get();
    
    //     return view('pages.index-detail-realisasi', [
    //         'rencanaKerja' => $rencanaKerja,
    //         'monitoring' => $monitoring,
    //         'type_menu' => 'monitoring',
    //     ]);
    // }

    public function edit($id)
    {
        $periode = PeriodeMonitoring::findOrFail($id);
        return view('pages.edit-periode-monitoring', compact('periode'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after_or_equal:pmo_tanggal_mulai',
        ]);

        $periode = PeriodeMonitoring::findOrFail($id);
        $periode->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periode->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periode->save();

        Alert::success('Berhasil', 'Periode Monitoring berhasil diperbarui!');
        return redirect()->route('periode-monitoring.index');
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class PeriodeMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');
        $tahunId = $request->query('th_tahun'); // pastikan ini sudah didefinisikan
        $query = RencanaKerja::with('tahunKerja', 'UnitKerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->orderBy('rk_nama', 'asc');
    
        if ($q) {
            $query->whereHas('periode_monev', function ($subQuery) use ($q) {
                $subQuery->where('pm_nama', 'like', '%' . $q . '%');
            });
        }
    
        if ($tahunId) {
            $query->where('th_id', $tahunId); 
        }
    
        $perides = $query->paginate(10);
        $no = $perides->firstItem();
    
        $th_tahun = tahun_kerja::orderBy('th_tahun')->get(); // definisikan $th_tahun di sini
        $periodes = periode_monev::orderBy('pm_nama')->get();
    
        return view('pages.index-periode-monitoring', [
            'title' => $title,
            'perides' => $perides,
            'th_tahun' => $th_tahun,
            'periodes' => $periodes,
            'tahunKerja' => tahun_kerja::where('ren_is_aktif', 'y')->get(), 
            'type_menu' => 'periode-monitoring',
            'tahunId' => $tahunId, 
            'q' => $q,
            'no' => $no, 
        ]);
    }



    public function create()
    {
        $title = 'Tambah Periode Monitoring';
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $th_tahun = tahun_kerja::all();
        // $pm_nama = periode_monev::orderBy('pm_nama')->get();
        $periodes = periode_monev::all();
        $RencanaKerja = RencanaKerja::all();

        return view('pages.create-periodemonitoring', [
            'title' => $title,
            'tahuns' => $tahuns, 
            'periodes' => $periodes,
            'th_tahun' => $th_tahun,
            'type_menu' => 'periode-monitoring', 
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'required|exists:periode_monev,pm_id',
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after:pmo_tanggal_mulai',
        ]);

        $periodeMonitoring = new PeriodeMonitoring();
        $periodeMonitoring->pmo_id = 'PMO' . strtoupper(md5(time())); 
        $periodeMonitoring->th_id = $request->th_id;
        $periodeMonitoring->pm_id = $request->pm_id;
        $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periodeMonitoring->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('periode-monitoring.index');
    }

    public function edit($periode_monitoring)
{
    $title = 'Edit Periode Monitoring';
    $periodeMonitoring = PeriodeMonitoring::findOrFail($periode_monitoring);
    $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
    $th_tahun = tahun_kerja::orderBy('th_tahun')->get();
    $periodes = periode_monev::orderBy('pm_nama')->get();

    return view('pages.edit-periodemonitoring', [
        'title' => $title,
        'periodeMonitoring' => $periodeMonitoring,
        'tahuns' => $tahuns,
        'th_tahun' => $th_tahun,
        'periodes' => $periodes,
        'type_menu' => 'periode-monitoring',
    ]);
}

public function update(Request $request, $periode_monitoring)
{
    $request->validate([
        'th_id' => 'required|exists:tahun_kerja,th_id',
        'pm_id' => 'required|exists:periode_monev,pm_id',
        'pmo_tanggal_mulai' => 'required|date',
        'pmo_tanggal_selesai' => 'required|date|after:pmo_tanggal_mulai',
    ]);

    $periodeMonitoring = PeriodeMonitoring::findOrFail($periode_monitoring);
    $periodeMonitoring->th_id = $request->th_id;
    $periodeMonitoring->pm_id = $request->pm_id;
    $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
    $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
    $periodeMonitoring->save();

    Alert::success('Sukses', 'Data Berhasil Diubah');
    return redirect()->route('periode-monitoring.index');
}



    public function destroy(PeriodeMonitoring $periodeMonitoring)
    {
        $periodeMonitoring->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('periode-monitoring.index');
    }
}

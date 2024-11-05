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

        $rencanaKerjas = RencanaKerja::with('tahunKerja', 'UnitKerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->orderBy('rk_nama', 'asc')
            ->paginate(10);
        $no = $rencanaKerjas->firstItem();

        return view('pages.index-periode-monitoring', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'periode-monitoring',
        ]);
    
    }

    public function create()
    {
        $title = 'Tambah Periode Monitoring';
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $th_tahun = tahun_kerja::all();
        $periodes = periode_monev::all();
        $RencanaKerja = RencanaKerja::all();

        return view('pages.create-periodemonitoring', [
            'title' => $title,
            'tahuns' => $tahuns, 
            'periodes' => $periodes,
            'th_tahun' => $th_tahun,
            'RencanaKerja' => $RencanaKerja,
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
            'rk_id' => 'required|exists:rencana_kerja,rk_id',
        ]);

        $periodeMonitoring = new PeriodeMonitoring();
        $periodeMonitoring->pmo_id = 'PMO' . strtoupper(md5(time())); 
        $periodeMonitoring->th_id = $request->th_id;
        $periodeMonitoring->pm_id = $request->pm_id;
        $periodeMonitoring->rk_id = $request->rk_id;
        $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periodeMonitoring->save();

        Alert::success('Sukses', 'Data Periode Monitoring Berhasil Ditambah');

        return redirect()->route('periode-monitoring.index');
    }

    public function edit(PeriodeMonitoring $periodeMonitoring)
    {
        $title = 'Edit Periode Monitoring';
        $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();
        $periodes = periode_monev::all();

        return view('pages.edit-periodemonitoring', [
            'title' => $title,
            'periodeMonitoring' => $periodeMonitoring,
            'tahuns' => $tahuns, 
            'periodes' => $periodes,
            'type_menu' => 'periode-monitoring', 
        ]);
    }

    public function update(Request $request, PeriodeMonitoring $periodeMonitoring)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'required|exists:periode_monev,pm_id',
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after:pmo_tanggal_mulai',
        ]);

        $periodeMonitoring->th_id = $request->th_id;
        $periodeMonitoring->pm_id = $request->pm_id;
        $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periodeMonitoring->save();

        Alert::success('Sukses', 'Data Periode Monitoring Berhasil Diubah');

        return redirect()->route('periode-monitoring.index');
    }

    public function destroy(PeriodeMonitoring $periodeMonitoring)
    {
        $periodeMonitoring->delete();

        Alert::success('Sukses', 'Data Periode Monitoring Berhasil Dihapus');

        return redirect()->route('periode-monitoring.index');
    }
}

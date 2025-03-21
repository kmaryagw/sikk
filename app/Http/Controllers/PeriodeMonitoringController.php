<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use App\Models\PeriodeMonitoring;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PeriodeMonitoringController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Periode Monitoring';
        $q = $request->query('q');
        $tahunId = $request->query('th_tahun');
        $query = PeriodeMonitoring::with('tahunKerja', 'periodeMonev');
        
        if ($q) {
            $query->whereHas('periodeMonev', function ($subQuery) use ($q) {
                $subQuery->where('pm_nama', 'like', '%' . $q . '%');
            });
        }

        if ($tahunId) {
            $query->where('th_id', $tahunId); 
        }

        $perides = $query->orderBy('th_id', 'asc')->paginate(10);
        $no = $perides->firstItem();

        $th_tahun = tahun_kerja::orderBy('th_tahun')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();
        
        return view('pages.index-periode-monitoring', [
            'title' => $title,
            'perides' => $perides,
            'th_tahun' => $th_tahun,
            'periodes' => $periodes,
            'tahunKerja' => tahun_kerja::where('th_is_aktif', 'y')->get(), 
            'type_menu' => 'periode-monitoring',
            'tahunId' => $tahunId, 
            'q' => $q,
            'no' => $no,
        ]);
    }


    public function create()
    {
        $title = 'Tambah Periode Monitoring';
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $th_tahun = tahun_kerja::orderBy('th_tahun')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();
        

        return view('pages.create-periodemonitoring', [
            'title' => $title,
            'tahuns' => $tahuns, 
            'periodes' => $periodes,
            'th_tahun' => tahun_kerja::where('th_is_aktif', 'y')->get(),
            'type_menu' => 'periode-monitoring', 
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'array',
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after:pmo_tanggal_mulai',
        ]);

        $periodeMonitoring = new PeriodeMonitoring();
        $periodeMonitoring->pmo_id = 'PMO' . strtoupper(md5(time())); 
        $periodeMonitoring->th_id = $request->th_id;
        $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periodeMonitoring->save();

        if ($request->has('pm_id')) {
            $periodeMonitoring->periodeMonev()->sync($request->pm_id);
        }

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('periode-monitoring.index');
    }

    public function edit(PeriodeMonitoring $periodeMonitoring)
    {
        $title = 'Edit Periode Monitoring';
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $th_tahun = tahun_kerja::orderBy('th_tahun')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();

        $selectedPeriodes = $periodeMonitoring->periodeMonev()->pluck('periode_monev.pm_id')->toArray();

        return view('pages.edit-periodemonitoring', [
            'title' => $title,
            'periodeMonitoring' => $periodeMonitoring,
            'selectedPeriodes' => $selectedPeriodes,
            'tahuns' => $tahuns,
            'th_tahun' => $th_tahun,
            'periodes' => $periodes,
            'type_menu' => 'periode-monitoring', 
        ]);
    }

    public function update(Request $request, PeriodeMonitoring $periodeMonitoring)
    {
        $request->validate([
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'array',
            'pmo_tanggal_mulai' => 'required|date',
            'pmo_tanggal_selesai' => 'required|date|after:pmo_tanggal_mulai',
        ]);

        $periodeMonitoring->th_id = $request->th_id;
        $periodeMonitoring->pmo_tanggal_mulai = $request->pmo_tanggal_mulai;
        $periodeMonitoring->pmo_tanggal_selesai = $request->pmo_tanggal_selesai;
        $periodeMonitoring->save();

        if ($request->has('pm_id')) {
            $periodeMonitoring->periodeMonev()->sync($request->pm_id);
        }

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

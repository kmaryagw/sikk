<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ProgramKerjaController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Program Kerja';
    $q = $request->query('q');
    $unit_id = $request->query('unit_id');
    $tahunId = $request->query('tahun');

    
    $units = UnitKerja::where('unit_kerja', 'y')->get();

    
    $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();

    $query = RencanaKerja::where('rk_nama', 'like', '%' . $q . '%')
    ->leftJoin('unit_kerja', function($join) {
        $join->on('unit_kerja.unit_id', '=', 'rencana_kerja.unit_id') 
            ->where('unit_kerja.unit_kerja', 'y');
    })
    ->leftJoin('tahun_kerja', function($join) {
        $join->on('tahun_kerja.th_id', '=', 'rencana_kerja.th_id') 
            ->where('tahun_kerja.ren_is_aktif', 'y');
    });

    if (Auth::user()->role == 'unit kerja') {
        $query->where('rencana_kerja.unit_id', Auth::user()->unit_id);
    }
    
    if ($q) {
        $query->where('rk_nama', 'like', '%' . $q . '%');
    }

    
    if ($unit_id) {
        $query->where('rencana_kerja.unit_id', $unit_id);
    }

    
    if ($tahunId) {
        $query->where('rencana_kerja.th_id', $tahunId);
    }

    
    $programkerjas = $query->paginate(10)->withQueryString();
    $no = $programkerjas->firstItem();

    return view('pages.index-programkerja', [
        'title' => $title,
        'programkerjas' => $programkerjas,
        'units' => $units,
        'tahuns' => $tahuns,
        'q' => $q,
        'unit_id' => $unit_id,
        'tahun' => $tahunId,
        'no' => $no,
        'type_menu' => 'programkerja',
    ]);
}


public function create()
{
    $title = 'Tambah Program Kerja';
    
    
    $units = UnitKerja::where('unit_kerja', 'y')->get();
    
    
    $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();

    return view('pages.create-programkerja', [
        'title' => $title,
        'units' => $units,
        'tahuns' => $tahuns,
        'type_menu' => 'programkerja',
    ]);
}


public function store(Request $request)
{
    $request->validate([
        'rk_nama' => 'required|string|max:255',
        'unit_id' => 'required|exists:unit_kerja,unit_id',
        'th_id' => 'required|exists:tahun_kerja,th_id',
    ]);

    
    $unitAktif = UnitKerja::where('unit_id', $request->unit_id)->where('unit_kerja', 'y')->exists();
    $tahunAktif = tahun_kerja::where('th_id', $request->th_id)->where('ren_is_aktif', 'y')->exists();

    if (!$unitAktif || !$tahunAktif) {
        Alert::error('Gagal', 'Unit kerja atau tahun kerja tidak aktif.');
        return redirect()->back()->withInput();
    }

    $customPrefix = 'PR';
    $timestamp = time();
    $md5Hash = md5($timestamp);
    $rk_id = $customPrefix . strtoupper($md5Hash);

    $programkerja = new RencanaKerja();
    $programkerja->rk_id = $rk_id;
    $programkerja->rk_nama = $request->rk_nama;
    $programkerja->unit_id = $request->unit_id;
    $programkerja->th_id = $request->th_id;

    $programkerja->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');

    return redirect()->route('programkerja.index');
}


public function edit(RencanaKerja $programkerja)
{
    $title = 'Ubah Program Kerja';
    
    
    $units = UnitKerja::where('unit_kerja', 'y')->get();
    
    
    $tahuns = tahun_kerja::where('ren_is_aktif', 'y')->get();

    return view('pages.edit-programkerja', [
        'title' => $title,
        'programkerja' => $programkerja,
        'units' => $units,
        'tahuns' => $tahuns,
        'type_menu' => 'programkerja',
    ]);
}


public function update(RencanaKerja $programkerja, Request $request)
{
    $request->validate([
        'rk_nama' => 'required|string|max:255',
        'unit_id' => 'required|exists:unit_kerja,unit_id',
        'th_id' => 'required|exists:tahun_kerja,th_id',
    ]);

    
    $unitAktif = UnitKerja::where('unit_id', $request->unit_id)->where('unit_kerja', 'y')->exists();
    $tahunAktif = tahun_kerja::where('th_id', $request->th_id)->where('ren_is_aktif', 'y')->exists();

    if (!$unitAktif || !$tahunAktif) {
        Alert::error('Gagal', 'Unit kerja atau tahun kerja tidak aktif.');
        return redirect()->back()->withInput();
    }

    $programkerja->rk_nama = $request->rk_nama;
    $programkerja->unit_id = $request->unit_id;
    $programkerja->th_id = $request->th_id;

    $programkerja->save();

    Alert::success('Sukses', 'Data Berhasil Diubah');

    return redirect()->route('programkerja.index');
}


    public function destroy(RencanaKerja $programkerja)
    {
        $programkerja->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('programkerja.index');
    }
}


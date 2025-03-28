<?php

namespace App\Http\Controllers;
use App\Models\UnitKerja;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UnitkerjaController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Unit';
        $q = $request->query('q');
        $units = UnitKerja::where('unit_nama', 'like', '%'. $q. '%')
            ->orderBy('unit_nama', 'asc')
            ->paginate(10)
            ->withQueryString();
        $no = $units->firstItem();
        

        return view('pages.index-unit', [
            'title' => $title,
            'units' => $units,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'unit',
        ]);
    }
    
    public function create()
    {
        $title = 'Tambah User';
        $unit_kerjas = ['y', 'n'];

        return view('pages.create-unit', [
            'title' => $title,
            'unit_kerjas' => $unit_kerjas,  // Kirim array role ke view
            'type_menu' => 'masterdata',
            'sub_menu' => 'unit',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_nama' => 'required|string|max:255',
            'unit_kerja' => 'required|in:y,n',

        ]);
    
        $customPrefix = 'UK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $unit_id = $customPrefix . strtoupper($md5Hash);
    
        $unit = new UnitKerja($request->all());
        $unit->unit_id = $unit_id;
    
        $unit->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('unit.index');
    }

    public function edit(UnitKerja $unit)
    {   
        $title = 'Ubah Unit';
        $unit_kerjas = ['y', 'n'];
    
        return view('pages.edit-unit', [
            'title' => $title,
            'unit_kerjas' => $unit_kerjas,
            'unit' => $unit,
            'type_menu' => 'masterdata',
            'sub_menu' => 'unit',
        ]);
    }

    public function update(UnitKerja $unit, Request $request)
    {
        $request->validate([
            'unit_nama' => 'required',
            'unit_kerja' => 'required', 
            
        ]);
    
        $unit->unit_nama = $request->unit_nama;
        $unit->unit_kerja = $request->unit_kerja;
        
        
        $unit->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('unit.index');
    }

    public function destroy(UnitKerja $unit)
    {
        $unit->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('unit.index');
    }
}

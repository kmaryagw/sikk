<?php

namespace App\Http\Controllers;
use App\Models\UnitKerja;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UnitKerjaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Unit';
        $q = $request->query('q');
        $units = UnitKerja::where('unit_nama', 'like', '%'. $q. '%')
        
        ->paginate(10)
        ->withQueryString();
        $no = $units->firstItem();
        

        return view('pages.index-unit', [
            'title' => $title,
            'units' => $units,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
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
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_nama' => 'required|string|max:255',
            
            'unit_kerja' => 'required|in:y,n',
            
        ]);
    
        $customPrefix = 'US';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_unit_kerja = $customPrefix . strtoupper($md5Hash);
    
        $unit = new UnitKerja($request->all());
        $unit->id_unit_kerja = $id_unit_kerja;
    
        $unit->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('unit.index');
    }

    public function edit(UnitKerja $unit)
    {
        
        $title = 'Ubah Unit';
        $unit_kerjas = ['y','n'];
        return view('pages.edit-unit', [
            'title' => $title,
            'unit_kerjas' => $unit_kerjas,
            'unit' => $unit,
            'type_menu' => 'masterdata',
        ]);
    }

    public function update(UnitKerja $unit, Request $request)
    {
        $request->validate([
            'unit_nama' => 'required',
            'unit_kerja' => 'required', 
            
        ]);
    
        
    
        $unit->unit_nama = $request->unit_nama;
        
        
        $unit->level = $request->level;
        
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

<?php

namespace App\Http\Controllers;

use App\Models\Falkutasn;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class FalkutasnController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Falkutas';
        $q = $request->query('q');
        $falkutasns = Falkutasn::where('nama_falkutas', 'like', '%' . $q . '%')
        ->orderBy('nama_falkutas', 'asc')
        ->paginate(10)
        ->withQueryString();
        $no = $falkutasns->firstItem();
        
        return view('pages.index-falkutasn', [
            'title' => $title,
            'falkutasns' => $falkutasns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'falkutas',
        ]);

    }

        public function create()
    {
        $title = 'Tambah Falkutas';

        return view('pages.create-falkutasn', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'falkutas',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_falkutas' => 'required|string|max:255',
        ]);
    
        $customPrefix = 'FK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_falkutas = $customPrefix . strtoupper($md5Hash);
    
        $falkutasn = new Falkutasn();
        $falkutasn->id_falkutas = $id_falkutas;
        $falkutasn->nama_falkutas = $request->nama_falkutas;
    
        $falkutasn->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('falkutasn.index');
    }

    public function edit($id)
    {
        $title = 'Ubah Falkutas';
        $falkutasn = Falkutasn::findOrFail($id);
    
    
    return view('pages.edit-falkutasn', [
        'title' => $title,
        'falkutasn' => $falkutasn,
        'type_menu' => 'masterdata',
        'sub_menu' => 'falkutas',
    ]);
    }

    public function update(Falkutasn $falkutasn, Request $request)
    {
        $request->validate([
            'nama_falkutas' => 'required',
            
            
        ]);
    
        $falkutasn->nama_falkutas = $request->nama_falkutas;
        
        $falkutasn->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('falkutasn.index');
    }

    public function destroy(Falkutasn $falkutasn)
    {
        $falkutasn->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('falkutasn.index');
    }
}

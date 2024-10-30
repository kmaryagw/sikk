<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Prodi';
        $q = $request->query('q');
        $prodis = program_studi::where('nama_prodi', 'like', '%' . $q . '%')
        ->orderBy('nama_prodi', 'asc')
        ->paginate(10)
        ->withQueryString();
        $no = $prodis->firstItem();
        
        return view('pages.index-prodi', [
            'title' => $title,
            'prodis' => $prodis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'prodi',
        ]);

    }

        public function create()
    {
        $title = 'Tambah Prodi';

        return view('pages.create-prodi', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'prodi',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
        ]);
    
        $customPrefix = 'PR';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $prodi_id = $customPrefix . strtoupper($md5Hash);
    
        $program_studi = new program_studi();
        $program_studi->prodi_id = $prodi_id;
        $program_studi->nama_prodi = $request->nama_prodi;
    
        $program_studi->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('prodi.index');
    }

    public function edit(program_studi $prodi)
    {
        $title = 'Ubah Prodi';
    
    
    return view('pages.edit-prodi', [
        'title' => $title,
        'prodi' => $prodi,
        'type_menu' => 'masterdata',
        'sub_menu' => 'prodi',
    ]);
    }

    public function update(program_studi $prodi, Request $request)
    {
        $request->validate([
            'nama_prodi' => 'required',
            
            
        ]);
    
        $prodi->nama_prodi = $request->nama_prodi;
        
        $prodi->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('prodi.index');
    }

    public function destroy(program_studi $prodi)
    {
        $prodi->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('prodi.index');
    }
}
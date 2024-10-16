<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Prodi';
        $q = $request->query('q');
        $prodis = program_studi::where('nama_prodi', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $prodis->firstItem();
        
        return view('pages.index-prodi', [
            'title' => $title,
            'prodis' => $prodis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);

    }

        public function create()
    {
        $title = 'Tambah Prodi';

        return view('pages.create-prodi', [
            'title' => $title,
            'type_menu' => 'masterdata',
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
    
        return redirect()->route('program-studi.index');
    }

}
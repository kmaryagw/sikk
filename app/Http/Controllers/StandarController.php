<?php

namespace App\Http\Controllers;

use App\Models\standar;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class StandarController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Unit';
        $q = $request->query('q');
        $standars = standar::where('std_nama', 'like', '%'. $q. '%')
        
        ->paginate(10)
        ->withQueryString();
        $no = $standars->firstItem();
        

        return view('pages.index-standar', [
            'title' => $title,
            'standars' => $standars,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'standar',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Standar';

        return view('pages.create-standar', [
            'title' => $title,
            'type_menu' => 'standar',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'std_nama' => 'required|string|max:20',
            'std_deskripsi' => 'required',

        ]);
    
        $customPrefix = 'ST';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $std_id = $customPrefix . strtoupper($md5Hash);
    
        $standar = new standar();
        $standar->std_id = $std_id;
        $standar->std_nama = $request->std_nama;
        $standar->std_deskripsi = $request->std_deskripsi;
    
        $standar->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('standar.index');
    }

    public function edit(standar $standar)
    {
        $title = 'Ubah Standar';
    
        return view('pages.edit-standar', [
            'title' => $title,
            'standar' => $standar,
            'type_menu' => 'standar',
        ]);
    }

    public function update(standar $standar, Request $request)
    {
        $request->validate([
            'std_nama' => 'required|max:20',
            'std_deskripsi' => 'required', 
            
        ]);
    
    
        $standar->std_nama = $request->std_nama;
        $standar->std_deskripsi = $request->std_deskripsi;
        
        
        $standar->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('standar.index');
    }

    public function destroy(standar $standar)
    {
        $standar->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('standar.index');
    }
}

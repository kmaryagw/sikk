<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerjaUtama;
use App\Models\standar;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IndikatorKinerjaUtamaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Indikator Kinerja Utama';
        $q = $request->query('q');
        $indikatorkinerjautamas = IndikatorKinerjaUtama::where('ik_nama', 'like', '%'. $q. '%')
        ->leftjoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
        ->paginate(10)
        ->withQueryString();
        $no = $indikatorkinerjautamas->firstItem();
        

        return view('pages.index-indikatorkinerjautama', [
            'title' => $title,
            'indikatorkinerjautamas' => $indikatorkinerjautamas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'indikatorkinerjautama',
            
        ]);
    }
    
    public function create()
    {
        $title = 'Tambah Indikator Kinerja Utama';
        $standar = standar::orderBy('std_nama')->get();

        return view('pages.create-indikatorkinerjautama', [
            'title' => $title,
            'standar' => $standar,
            'type_menu' => 'indikatorkinerjautama',
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'ik_nama' => 'required|string|max:255',
        'std_id' => 'required|string', // Add validation for std_id
    ]);

    $customPrefix = 'IK';
    $timestamp = time();
    $md5Hash = md5($timestamp);
    $ik_id = $customPrefix . strtoupper($md5Hash);

    $indikatorkinerjautama = new IndikatorKinerjaUtama();
    $indikatorkinerjautama->ik_id = $ik_id;
    $indikatorkinerjautama->ik_nama = $request->ik_nama;
    $indikatorkinerjautama->std_id = $request->std_id; // Assign std_id from request

    $indikatorkinerjautama->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');

    return redirect()->route('indikatorkinerjautama.index');
}


    public function edit(IndikatorKinerjaUtama $indikatorkinerjautama)
{
    $title = 'Ubah Indikator Kinerja Utama';
    $standar = standar::orderBy('std_nama')->get();
    
    return view('pages.edit-indikatorkinerjautama', [
        'title' => $title,
        'type_menu' => 'indikatorkinerjautama',
        'indikatorkinerjautama' => $indikatorkinerjautama,
        'standar' => $standar,
        
    ]);
}

    public function update(IndikatorKinerjaUtama $indikatorkinerjautama, Request $request)
    {
        $request->validate([
            'ik_nama' => 'required',
            'std_id' => 'required',
        ]);
    
        
    
        $indikatorkinerjautama->ik_nama = $request->ik_nama;
        $indikatorkinerjautama->std_id = $request->std_id;
 
        $indikatorkinerjautama->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('indikatorkinerjautama.index');
    }

    public function destroy(IndikatorKinerjaUtama $indikatorkinerjautama)
    {
        $indikatorkinerjautama->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('indikatorkinerjautama.index');
    }
}

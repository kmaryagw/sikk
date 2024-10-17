<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerjaUtama;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IndikatorKinerjaUtamaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Indikator Kinerja Utama';
        $q = $request->query('q');
        $indikatorkinerjautamas = IndikatorKinerjaUtama::where('ik_nama', 'like', '%'. $q. '%')
        
        ->paginate(10)
        ->withQueryString();
        $no = $indikatorkinerjautamas->firstItem();
        

        return view('pages.index-indikatorkinerjautama', [
            'title' => $title,
            'indikatorkinerjautamas' => $indikatorkinerjautamas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            
        ]);
    }
    
    public function create()
    {
        $title = 'Tambah Indikator Kinerja Utama';
        

        return view('pages.create-indikatorkinerjautama', [
            'title' => $title,
            'type_menu' => 'masterdata',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ik_nama' => 'required|string|max:20',
        ]);
    
        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);
    
        $indikatorkinerjautama = new IndikatorKinerjaUtama($request->all());
        $indikatorkinerjautama->ik_id = $ik_id;
    
        $indikatorkinerjautama->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('indikatorkinerjautama.index');
    }

    public function edit(IndikatorKinerjaUtama $indikatorkinerjautama)
{
    $title = 'Ubah Indikator Kinerja Utama';
    
    
    return view('pages.edit-unit', [
        'title' => $title,
        
        'indikatorkinerjautama' => $indikatorkinerjautama,
        
    ]);
}

    public function update(IndikatorKinerjaUtama $indikatorkinerjautama, Request $request)
    {
        $request->validate([
            'ik_nama' => 'required',
        ]);
    
        
    
        $indikatorkinerjautama->ik_nama = $request->ik_nama;
 
        $indikatorkinerjautama->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('indikatorkinerjautama.index');
    }

    public function destroy(IndikatorKinerjaUtama $indikatorkinerjautama)
    {
        $indikatorkinerjautama->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('unit.indikatorkinerjautamas');
    }
}

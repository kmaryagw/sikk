<?php

namespace App\Http\Controllers;

use App\Models\tahun_kerja;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class TahunController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Tahun Kerja';
        $q = $request->query('q');
        $tahuns = tahun_kerja::where('th_tahun', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $tahuns->firstItem();
        
        return view('pages.index-tahun', [
            'title' => $title,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Tahun';
        
        $ren_is_aktifs = ['y', 'n'];

        return view('pages.create-tahun', [
            'title' => $title,
            'ren_is_aktifs' => $ren_is_aktifs,
            'type_menu' => 'masterdata',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_tahun' => 'required|integer|min:1900|max:2100',
            'ren_is_aktif' => 'required|in:y,n',
            
        ]);
    
        $customPrefix = 'TH';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $th_id = $customPrefix . strtoupper($md5Hash);
    
        $tahun = new tahun_kerja($request->all());
        $tahun->th_id = $th_id;
        $tahun->ren_id = $request->ren_id;
        $tahun->ren_id = 1;
        $tahun->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('tahun.index');
    }
}

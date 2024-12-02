<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\tahun_kerja;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TahunController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Tahun Kerja';
        $q = $request->query('q');
        $tahuns = tahun_kerja::where('th_tahun', 'like', '%' . $q . '%')
            ->orderBy('th_tahun', 'asc')
            ->leftjoin('renstra', 'renstra.ren_id', '=', 'tahun_kerja.ren_id')
            ->paginate(10)
            ->withQueryString();
        $no = $tahuns->firstItem();
        
        return view('pages.index-tahun', [
            'title' => $title,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Tahun';
        
        $th_is_aktifs = ['y', 'n'];
        $renstras = Renstra::orderBy('ren_nama')->get();
        $renstras = Renstra::all();

        return view('pages.create-tahun', [
            'title' => $title,
            'th_is_aktifs' => $th_is_aktifs,
            'renstras' => $renstras,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'th_tahun' => 'required|integer|min:1900|max:2100',
            'th_is_aktif' => 'required|in:y,n',
            'ren_id' => 'required',
            
        ]);
    
        $customPrefix = 'TH';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $th_id = $customPrefix . strtoupper($md5Hash);
    
        $tahun = new tahun_kerja();
        $tahun->th_id = $th_id;
        $tahun->th_tahun = $request->th_tahun;
        $tahun->ren_id = $request->ren_id;
        $tahun->th_is_aktif = $request->th_is_aktif;
        $tahun->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('tahun.index');
    }

    public function edit(tahun_kerja $tahun)
    {
        $title = 'Ubah tahun';
        $th_is_aktifs = ['y', 'n'];
        $renstras = Renstra::orderBy('ren_nama')->get();
        $renstras = Renstra::all();
    
        return view('pages.edit-tahun', [
            'title' => $title,
            'th_is_aktifs' => $th_is_aktifs,
            'renstras' => $renstras,
            'tahun' => $tahun,
            'type_menu' => 'masterdata',
            'sub_menu' => 'tahun',
        ]);
    }

    public function update(tahun_kerja $tahun, Request $request)
    {
        $request->validate([
            'th_tahun' => 'required',
        ]);
    
        $tahun->th_tahun = $request->th_tahun; 
        $tahun->ren_id = $request->ren_id;
        $tahun->th_is_aktif = $request->th_is_aktif;
        $tahun->save();

    Alert::success('Sukses', 'Data Berhasil Diubah');

    return redirect()->route('tahun.index');
}


    public function destroy(tahun_kerja $tahun)
    {
        $tahun->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('tahun.index');
    }

}

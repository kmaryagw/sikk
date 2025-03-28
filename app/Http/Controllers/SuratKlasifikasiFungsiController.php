<?php

namespace App\Http\Controllers;

use App\Models\SuratKlasifikasiFungsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SuratKlasifikasiFungsiController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Surat Klasifikasi Fungsi';
        $q = $request->query('q');
        $fungsis = SuratKlasifikasiFungsi::where('skf_nama', 'like', '%'. $q. '%')
            ->orderBy('skf_nama', 'asc')
            ->paginate(10)
            ->withQueryString();
    
        return view('pages.index-surat-klasifikasi-fungsi', [
            'title' => $title,
            'fungsis' => $fungsis,
            'q' => $q,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratfungsi',
        ]);
    }


    public function create()
    {
        $title = 'Tambah Surat Klasifikasi Fungsi';
        
        $skfaktifs = ['y', 'n'];

        return view('pages.create-surat-klasifikasi-fungsi', [
            'title' => $title,
            
            'skfaktifs' => $skfaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratfungsi',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'skf_nama' => 'required|string|max:100',
            
            'skf_aktif' => 'required|in:y,n',
            
        ]);

        $customPrefix = 'SKF';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $skf_id = $customPrefix . strtoupper($md5Hash);

        $suratfungsi = new SuratKlasifikasiFungsi();
        $suratfungsi->skf_id = $skf_id;
        $suratfungsi->skf_nama = $request->skf_nama;
        $suratfungsi->skf_aktif = $request->skf_aktif;
        

        $suratfungsi->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('suratfungsi.index');
    }

    public function edit(SuratKlasifikasiFungsi $suratfungsi)
    {   
        $title = 'Ubah Surat Klasifikasi Fungsi';
        $skfaktifs = ['y', 'n'];
        
        
        return view('pages.edit-surat-klasifikasi-fungsi', [
            'title' => $title,
            'suratfungsi' => $suratfungsi,
            
            'skfaktifs' => $skfaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratfungsi',
        ]);
    }

    public function update(SuratKlasifikasiFungsi $suratfungsi, Request $request)
    {
        $request->validate([
            'skf_nama' => 'required|string|max:100',
            
            'skf_aktif' => 'required|in:y,n',
            
        ]);

        $suratfungsi->skf_nama = $request->skf_nama;
        $suratfungsi->skf_aktif = $request->skf_aktif;
        
        
        $suratfungsi->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('suratfungsi.index');
    }

    public function destroy(SuratKlasifikasiFungsi $suratfungsi)
    {
        $suratfungsi->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('suratfungsi.index');
    }
}

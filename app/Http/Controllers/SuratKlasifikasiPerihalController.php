<?php

namespace App\Http\Controllers;

use App\Models\SuratKlasifikasiFungsi;
use App\Models\SuratKlasifikasiPerihal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SuratKlasifikasiPerihalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Surat Klasifikasi Perihal';
        $q = $request->query('q');
        $perihals = SuratKlasifikasiPerihal::with('fungsi')
            ->where('skp_nama', 'like', '%' . $q . '%')
            ->orderBy('skf_id', 'asc')
            ->get();
    
        return view('pages.index-surat-klasifikasi-perihal', [
            'title' => $title,
            'perihals' => $perihals,
            'q' => $q,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratperihal',
        ]);
    }


    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $title = 'Tambah Surat Klasifikasi Perihal';
        $suratfungsi = SuratKlasifikasiFungsi::where('skf_aktif', 'y')->get();
        $skpaktifs = ['y', 'n'];

        return view('pages.create-surat-klasifikasi-perihal', [
            'title' => $title,
            'suratfungsi' => $suratfungsi,
            'skpaktifs' => $skpaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratperihal',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'skp_nama' => 'required|string|max:100',
            'skp_kode' => 'nullable|string|max:50',
            'skp_aktif' => 'required|in:y,n',
            'skf_id' => 'required|string|max:50',
        ]);

        $customPrefix = 'SKP';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $skp_id = $customPrefix . strtoupper($md5Hash);

        $suratperihal = new SuratKlasifikasiPerihal();
        $suratperihal->skp_id = $skp_id;
        $suratperihal->skp_nama = $request->skp_nama;
        $suratperihal->skp_kode = $request->skp_kode;
        $suratperihal->skp_aktif = $request->skp_aktif;
        $suratperihal->skf_id = $request->skf_id;

        $suratperihal->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('suratperihal.index');
    }

    public function edit(SuratKlasifikasiPerihal $suratperihal)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah Surat Klasifikasi Perihal';
        $skpaktifs = ['y', 'n'];
        $suratfungsi = SuratKlasifikasiFungsi::where('skf_aktif', 'y')->get();
        
        return view('pages.edit-surat-klasifikasi-perihal', [
            'title' => $title,
            'suratperihal' => $suratperihal,
            'suratfungsi' => $suratfungsi,
            'skpaktifs' => $skpaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratperihal',
        ]);
    }

    public function update(SuratKlasifikasiPerihal $suratperihal, Request $request)
    {
        $request->validate([
            'skp_nama' => 'required|string|max:100',
            'skp_kode' => 'nullable|string|max:50',
            'skp_aktif' => 'required|in:y,n',
            'skf_id' => 'required|string|max:50',
        ]);

        $suratperihal->skp_nama = $request->skp_nama;
        $suratperihal->skp_kode = $request->skp_kode;
        $suratperihal->skp_aktif = $request->skp_aktif;
        $suratperihal->skf_id = $request->skf_id;
        
        $suratperihal->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('suratperihal.index');
    }

    public function destroy(SuratKlasifikasiPerihal $suratperihal)
    {
        $suratperihal->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('suratperihal.index');
    }
}

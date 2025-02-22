<?php

namespace App\Http\Controllers;

use App\Models\SuratKlasifikasiLingkup;
use App\Models\SuratKlasifikasiPerihal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SuratKlasifikasiLingkupController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Surat Klasifikasi Perihal';
        $q = $request->query('q');
        $lingkups = SuratKlasifikasiLingkup::with('perihal')
            ->where('skl_nama', 'like', '%' . $q . '%')
            ->orderBy('skp_id', 'asc')
            ->get();
    
        return view('pages.index-surat-klasifikasi-lingkup', [
            'title' => $title,
            'lingkups' => $lingkups,
            'q' => $q,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratlingkup',
        ]);
    }


    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $title = 'Tambah Surat Klasifikasi Lingkup';
        $suratperihal = SuratKlasifikasiPerihal::where('skp_aktif', 'y')->get();
        $sklaktifs = ['y', 'n'];

        return view('pages.create-surat-klasifikasi-lingkup', [
            'title' => $title,
            'suratperihal' => $suratperihal,
            'sklaktifs' => $sklaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratlingkup',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'skl_nama' => 'required|string|max:100',
            'skl_kode' => 'nullable|string|max:50',
            'skl_aktif' => 'required|in:y,n',
            'skp_id' => 'required|string|max:50',
        ]);

        $customPrefix = 'SKL';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $skl_id = $customPrefix . strtoupper($md5Hash);

        $suratlingkup = new SuratKlasifikasiLingkup();
        $suratlingkup->skl_id = $skl_id;
        $suratlingkup->skl_nama = $request->skl_nama;
        $suratlingkup->skl_kode = $request->skl_kode;
        $suratlingkup->skl_aktif = $request->skl_aktif;
        $suratlingkup->skp_id = $request->skp_id;

        $suratlingkup->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('suratlingkup.index');
    }

    public function edit(SuratKlasifikasiLingkup $suratlingkup)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah Surat Klasifikasi Lingkup';
        $sklaktifs = ['y', 'n'];
        $suratperihal = SuratKlasifikasiPerihal::where('skp_aktif', 'y')->get();
        
        return view('pages.edit-surat-klasifikasi-lingkup', [
            'title' => $title,
            'suratlingkup' => $suratlingkup,
            'suratperihal' => $suratperihal,
            'sklaktifs' => $sklaktifs,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'suratlingkup',
        ]);
    }

    public function update(SuratKlasifikasiLingkup $suratlingkup, Request $request)
    {
        $request->validate([
            'skl_nama' => 'required|string|max:100',
            'skl_kode' => 'nullable|string|max:50',
            'skl_aktif' => 'required|in:y,n',
            'skp_id' => 'required|string|max:50',
        ]);

        $suratlingkup->skl_nama = $request->skl_nama;
        $suratlingkup->skl_kode = $request->skl_kode;
        $suratlingkup->skl_aktif = $request->skl_aktif;
        $suratlingkup->skp_id = $request->skp_id;
        
        $suratlingkup->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('suratlingkup.index');
    }

    public function destroy(SuratKlasifikasiLingkup $suratlingkup)
    {
        $suratlingkup->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('suratlingkup.index');
    }
}

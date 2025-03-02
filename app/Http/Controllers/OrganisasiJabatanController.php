<?php

namespace App\Http\Controllers;

use App\Models\OrganisasiJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class OrganisasiJabatanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Data Organisasi Jabatan';
        $q = $request->query('q');

        $organisasis = OrganisasiJabatan::with('children')
            ->whereNull('oj_induk')
            ->where('oj_nama', 'like', '%' . $q . '%')
            ->orderBy('created_at', 'asc')
            ->get();

            
        
        return view('pages.index-organisasi-jabatan', [
            'title' => $title,
            'organisasis' => $organisasis,
            'q' => $q,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'organisasijabatan',
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $organisasis = OrganisasiJabatan::all();
        $nomors = ['y', 'n'];
        $statuses = ['y', 'n'];
        $title = 'Tambah Organisasi Jabatan';

        return view('pages.create-organisasi-jabatan', [
            'title' => $title,
            'organisasis' => $organisasis,
            'nomors' => $nomors,
            'statuses' => $statuses,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'organisasijabatan',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'oj_nama' => 'required|max:100',
            'oj_mengeluarkan_nomor' => 'required|in:y,n',
            'oj_kode' => 'nullable|max:50',
            'oj_induk' => 'nullable|exists:organisasi_jabatan,oj_id',
            'oj_status' => 'required|in:y,n',
        ]);
    
        $customPrefix = 'OJ';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $id_organisasi = $customPrefix . strtoupper($md5Hash);
    
        $organisasis = new OrganisasiJabatan();
        $organisasis->oj_id = $id_organisasi;
        $organisasis->oj_nama = $request->oj_nama;
        $organisasis->oj_mengeluarkan_nomor = $request->oj_mengeluarkan_nomor;
        $organisasis->oj_kode = $request->oj_kode;
        $organisasis->oj_induk = $request->oj_induk;
        $organisasis->oj_status = $request->oj_status;
    
        $organisasis->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('organisasijabatan.index');
    }

    public function edit(OrganisasiJabatan $organisasijabatan)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah Organisasi';
        $organisasies = OrganisasiJabatan::all();
        $nomors = ['y', 'n'];
        $statuses = ['y', 'n'];

        return view('pages.edit-organisasi-jabatan', [
            'title' => $title,
            'organisasijabatan' => $organisasijabatan,
            'organisasies' => $organisasies,
            'nomors' => $nomors,
            'statuses' => $statuses,
            'type_menu' => 'mastersurat',
            'sub_menu' => 'organisasijabatan',
        ]);
    }

    public function update(OrganisasiJabatan $organisasijabatan, Request $request)
    {
        $request->validate([
            'oj_nama' => 'required|max:100',
            'oj_mengeluarkan_nomor' => 'required|in:y,n',
            'oj_kode' => 'nullable|max:50',
            'oj_induk' => 'nullable|exists:organisasi_jabatan,oj_id',
            'oj_status' => 'required|in:y,n',
        ]);

        $organisasijabatan->oj_nama = $request->oj_nama;
        $organisasijabatan->oj_mengeluarkan_nomor = $request->oj_mengeluarkan_nomor;
        $organisasijabatan->oj_kode = $request->oj_kode;
        $organisasijabatan->oj_induk = $request->oj_induk;
        $organisasijabatan->oj_status = $request->oj_status;
        $organisasijabatan->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('organisasijabatan.index');
    }

    public function destroy(OrganisasiJabatan $organisasijabatan)
    {
        $organisasijabatan->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('organisasijabatan.index');
    }
}

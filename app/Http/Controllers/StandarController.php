<?php

namespace App\Http\Controllers;

use App\Models\standar;
use App\Models\standar_dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'stdd_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
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

        // Menyimpan file dokumen
        if ($request->hasFile('stdd_file')) {
            $file = $request->file('stdd_file');
            // Simpan file ke storage/app/public/dokumen
            $filePath = $file->store('public/dokumen'); 

            // Simpan ke tabel standar_dokumen
            $standardokumen = new standar_dokumen();
            $standardokumen->stdd_id = uniqid(); // ID dokumen, sesuaikan jika perlu
            $standardokumen->std_id = $standar->std_id; // ID standar yang baru dibuat
            $standardokumen->stdd_file = $filePath; // Simpan path file
            $standardokumen->save();
        }

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
        $standarDokumen = standar_dokumen::where('std_id', $standar->std_id)->first();
        if ($standarDokumen) {
            Storage::delete($standarDokumen->stdd_file); // Hapus file dari storage
        }
        $standar->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('standar.index');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Prodi';
        $q = $request->query('q');
        $prodis = program_studi::where('nama_prodi', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $prodis->firstItem();
        
        return view('pages.index-prodi', [
            'title' => $title,
            'prodis' => $prodis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);

    }

        public function create()
    {
        $title = 'Tambah Prodi';

        return view('pages.create-prodi', [
            'title' => $title,
            'type_menu' => 'masterdata',
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_prodi' => 'required|string|max:150',
    ]);
    
    // Membuat prodi_id dengan format yang lebih mudah
    $customPrefix = 'PR';
    
    // Ambil ID terakhir dari database untuk urutan baru
    $lastProdi = program_studi::orderBy('prodi_id', 'desc')->first();
    
    // Jika ada data, ambil nomor terakhir dari ID
    if ($lastProdi) {
        // Ambil nomor setelah prefix dan convert ke integer
        $lastId = intval(substr($lastProdi->prodi_id, 2)); // Menghilangkan prefix 'PR'
        $newIdNumber = $lastId + 1; // Menambah 1 untuk ID baru
    } else {
        $newIdNumber = 1; // Jika tidak ada data, mulai dari 1
    }
    
    // Buat ID baru dengan format yang lebih sederhana
    $prodi_id = $customPrefix . str_pad($newIdNumber, 5, '0', STR_PAD_LEFT); // Menambahkan nol di depan hingga panjang 5 digit
    
    $prodi = new program_studi($request->all());
    $prodi->prodi_id = $prodi_id;

    $prodi->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');

    return redirect()->route('program-studi.index');
}

}
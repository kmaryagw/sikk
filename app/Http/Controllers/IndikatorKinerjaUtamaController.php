<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\IndikatorKinerjaUtama;
use App\Models\Standar;
use App\Models\tahun_kerja;
use App\Models\TahunKerja; // Model untuk tahun kerja
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use RealRashid\SweetAlert\Facades\Alert;

class IndikatorKinerjaUtamaController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Indikator Kinerja Utama';
    $q = $request->query('q');
    $tahunId = $request->query('tahun'); // Ambil input tahun dari query string

    // Ambil data tahun aktif untuk dropdown filter
    $tahun = tahun_kerja::where('ren_is_aktif', 'y')->get();

    // Query untuk filter nama dan tahun
    $query = IndikatorKinerjaUtama::where('ik_nama', 'like', '%'. $q. '%') // Tambahkan kolom tahun
        ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
        ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'indikator_kinerja.th_id'); // Lakukan join dengan tabel tahun
    
    // Filter berdasarkan nama indikator
    if ($q) {
        $query->where('ik_nama', 'like', '%' . $q . '%');
    }

    // Filter berdasarkan tahun jika ada
    if ($tahunId) {
        $query->where('tahun_kerja.th_id', $tahunId); // Filter berdasarkan th_id dari tahun_kerja
    }

    $indikatorkinerjautamas = $query->paginate(10)->withQueryString();
    $no = $indikatorkinerjautamas->firstItem();
    
    return view('pages.index-indikatorkinerjautama', [
        'title' => $title,
        'indikatorkinerjautamas' => $indikatorkinerjautamas,
        'q' => $q,
        'tahun' => $tahun, // Kirim data tahun ke view
        'tahunId' => $tahunId, // Kirim tahun yang dipilih ke view untuk pengisian kembali
        'no' => $no,
        'type_menu' => 'indikatorkinerjautama',
    ]);
}


    public function create()
{
    $title = 'Tambah Indikator Kinerja Utama';
    $standar = Standar::orderBy('std_nama')->get();
    $tahunKerja = DB::table('tahun_kerja')->where('ren_is_aktif', 'y')->get(); // Mengambil tahun aktif

    return view('pages.create-indikatorkinerjautama', [
        'title' => $title,
        'standar' => $standar,
        'tahunKerja' => $tahunKerja, // Menambahkan tahun kerja ke view
        'type_menu' => 'indikatorkinerjautama',
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string', // Add validation for std_id
            'th_id' => 'required', // Tambahkan validasi untuk tahun
        ]);

        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);

        $indikatorkinerjautama = new IndikatorKinerjaUtama();
        $indikatorkinerjautama->ik_id = $ik_id;
        $indikatorkinerjautama->ik_nama = $request->ik_nama;
        $indikatorkinerjautama->std_id = $request->std_id;
        $indikatorkinerjautama->th_id = $request->th_id; // Simpan tahun yang dipilih

        $indikatorkinerjautama->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('indikatorkinerjautama.index');
    }

    public function edit(IndikatorKinerjaUtama $indikatorkinerjautama)
    {
        $title = 'Ubah Indikator Kinerja Utama';
        $standar = Standar::orderBy('std_nama')->get();
        $tahunKerja = DB::table('tahun_kerja')->where('ren_is_aktif', 'y')->get();
        
        return view('pages.edit-indikatorkinerjautama', [
            'title' => $title,
            'type_menu' => 'indikatorkinerjautama',
            'indikatorkinerjautama' => $indikatorkinerjautama,
            'tahunKerja' => $tahunKerja,
            'standar' => $standar,
        ]);
    }

    public function update(IndikatorKinerjaUtama $indikatorkinerjautama, Request $request)
    {
        $request->validate([
            'ik_nama' => 'required',
            'std_id' => 'required',
            'th_id' => 'required', // Tambahkan validasi tahun
        ]);

        $indikatorkinerjautama->ik_nama = $request->ik_nama;
        $indikatorkinerjautama->std_id = $request->std_id;
        $indikatorkinerjautama->th_id = $request->th_id; // Simpan tahun yang dipilih

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

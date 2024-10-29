<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use Illuminate\Routing\Controller;
use App\Models\RealisasiRenja;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class RealisasiRenjaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Realisasi Renja';
        $q = $request->query('q');

        $rencanaKerjas = RencanaKerja::with('tahun_kerja', 'UnitKerja')
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->paginate(10)
            ->withQueryString();
        $no = $rencanaKerjas->firstItem();

        return view('pages.index-realisasirenja', [
            'title' => $title,
            'rencanaKerjas' => $rencanaKerjas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'realisasirenja',
        ]);
    }

    public function showRealisasi($rk_id)
    {
        $rencanaKerja = RencanaKerja::findOrFail($rk_id);

        $realisasi = RealisasiRenja::where('rk_id', $rk_id)->get();

        return view('pages.index-detail-realisasi', [
            'rencanaKerja' => $rencanaKerja,
            'realisasi' => $realisasi,
            'type_menu' => 'realisasirenja',
        ]);
    }


    public function create(Request $request)
{
    $title = 'Tambah Realisasi Renja';

    // Mengambil data RencanaKerja berdasarkan rk_id dari request
    $rencanaKerja = RencanaKerja::with('periodes')->findOrFail($request->rk_id); 
    $periodes = periode_monev::all();

    // Ambil id periode yang sudah terpilih
    $selectedPeriodes = $rencanaKerja->periodes->pluck('pm_id')->toArray();

    return view('pages.create-realisasirenja', [
        'title' => $title,
        'rencanaKerja' => $rencanaKerja,
        'rk_nama' => $rencanaKerja->rk_nama,
        'pm_nama' => $rencanaKerja->pm_nama, // Pastikan ini sesuai dengan relasi
        'periodes' => $periodes,
        'selectedPeriodes' => $selectedPeriodes,
        'type_menu' => 'realisasirenja',
    ]);
}



public function store(Request $request)
{
    $request->validate([
        'rkr_url' => 'nullable|url',
        'rkr_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'rkr_deskripsi' => 'nullable|string',
        'rkr_capaian' => 'required|integer',
        'rkr_tanggal' => 'required|date',
    ]);

    if (empty($request->rkr_url) && empty($request->rkr_deskripsi)) {
        return back()->withErrors([
            'rkr_url' => 'URL atau Deskripsi harus diisi.',
            'rkr_deskripsi' => 'URL atau Deskripsi harus diisi.',
        ])->withInput();
    }

    $customPrefix = 'RKR';
    $timestamp = time();
    $md5Hash = md5($timestamp);
    $rkr_id = $customPrefix . strtoupper($md5Hash);

    $realisasi = new RealisasiRenja();
    $realisasi->rkr_id = $rkr_id;
    $realisasi->rk_id = $request->rk_id; // Diperoleh dari tampilan yang otomatis terisi
    $realisasi->pm_id = $request->pm_id;
    $realisasi->rkr_url = $request->rkr_url;
    $realisasi->rkr_file = $request->hasFile('rkr_file') ? $request->file('rkr_file')->store('realisasi_files') : null;
    $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
    $realisasi->rkr_capaian = $request->rkr_capaian;
    $realisasi->rkr_tanggal = $request->rkr_tanggal;
    $realisasi->save();

    Alert::success('Sukses', 'Data Berhasil Ditambah');

    return redirect()->route('realisasirenja.index');
}

}
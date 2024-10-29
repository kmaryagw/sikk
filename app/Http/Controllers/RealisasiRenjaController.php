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


    public function create()
    {
        $title = 'Tambah Realisasi Renja';
        $rencanakerjas = RencanaKerja::orderBy('rk_nama')->get();
        $periodes = periode_monev::all();

        return view('pages.create-realisasirenja', [
            'title' => $title,
            'rencanakerjas' => $rencanakerjas,
            'periodes' => $periodes,
            'type_menu' => 'realisasirenja',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rk_id' => 'required',
            'pm_id' => 'required',
            'rkr_url' => 'nullable',
            'rkr_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'rkr_deskripsi' => 'required',
            'rkr_capaian' => 'required|integer',
            'rkr_tanggal' => 'required',
        ]);

        $customPrefix = 'RKR';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $rkr_id = $customPrefix . strtoupper($md5Hash);

        $realisasi = new RealisasiRenja();
        $realisasi->rkr_id = $rkr_id;
        $realisasi->rk_id = $request->rk_id;
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
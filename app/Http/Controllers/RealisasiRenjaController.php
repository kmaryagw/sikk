<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
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
        $realisasis = RealisasiRenja::select('rencana_kerja_realisasi.*', 'rencana_kerja.ren_nama')
            ->leftJoin('rencana_kerja', 'rencana_kerja.rk_id', '=', 'rencana_kerja_realisasi.rk_id')
            ->leftJoin('periode_monev', 'periode_monev.pm_id', '=', 'rencana_kerja_realisasi.pm_id')
            ->where('rencana_kerja_realisasi.rk_id', 'like', '%' . $q . '%')
            ->orderBy('unit_id', 'asc')
            ->paginate(10)
            ->withQueryString();
        $no = $realisasis->firstItem();
        
        return view('pages.index-realisasirenja', [
            'title' => $title,
            'realisasis' => $realisasis,
            'q' => $q,
            'no' => $no,
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
            // 'pm_id' => 'required',
            // 'rkr_url' => 'required',
            // 'rkr_file' => 'required',
            // 'rkr_deskripsi' => 'required',
            // 'rkr_capaian' => 'required|integer',
            'rkr_tanggal' => 'required',
        ]);
    
        $customPrefix = 'RKR';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $rkr_id = $customPrefix . strtoupper($md5Hash);
    
        $realisasi = new RealisasiRenja();
        $realisasi->rkr_id = $rkr_id;
        $realisasi->rk_id = $request->rk_id;
        // $realisasi->pm_id = $request->pm_id;
        // $realisasi->rkr_url = $request->rkr_url;
        // $realisasi->rkr_file = $request->rkr_file;
        // $realisasi->rkr_deskripsi = $request->rkr_deskripsi;
        // $realisasi->rkr_capaian = $request->rkr_capaian;
        $realisasi->rkr_tanggal = $request->rkr_tanggal;
        $realisasi->save();
    
        Alert::success('Sukses', 'Data Berhasil Ditambah');
    
        return redirect()->route('realisasirenja.index');
    }
}

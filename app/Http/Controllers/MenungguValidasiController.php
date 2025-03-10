<?php

namespace App\Http\Controllers;

use App\Models\SuratNomor;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenungguValidasiController extends NomorSuratController
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    // public function index(Request $request)
    // {
    //     $title = 'Data Nomor Surat yang perlu ditinjau';
    //     $q = $request->query('q');
    //     $ajukans = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent'])
    //         ->where('sn_perihal', 'like', '%' . $q . '%')
    //         ->where('sn_status', 'ajukan')
    //         ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
    //         ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
    //         ->paginate(10)
    //         ->withQueryString();
    //         // ->get();,
    
    //     return view('pages.index-menunggu-validasi', [
    //         'title' => $title,
    //         'ajukans' => $ajukans,
    //         'q' => $q,
    //         'type_menu' => 'surat',
    //         'sub_menu' => 'menungguvalidasi',
    //     ]);
    // }

    public function index(Request $request)
{
    $title = 'Data Nomor Surat yang perlu ditinjau';
    $q = $request->query('q');
    $tanggal = $request->query('tanggal');
    $unit = $request->query('unit');

    $ajukans = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent'])
        ->where('sn_perihal', 'like', '%' . $q . '%')
        ->where('sn_status', 'ajukan');

    if ($tanggal) {
        $ajukans->whereDate('sn_tanggal', $tanggal);
    }

    if ($unit) {
        $ajukans->where('unit_id', $unit);
    }

    $ajukans = $ajukans
        ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
        ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
        ->paginate(10)
        ->withQueryString();

    $units = UnitKerja::orderBy('unit_nama')->get();


    return view('pages.index-menunggu-validasi', [
        'title' => $title,
        'ajukans' => $ajukans,
        'q' => $q,
        'tanggal' => $tanggal,
        'unit' => $unit,
        'units' => $units,
        'type_menu' => 'surat',
        'sub_menu' => 'menungguvalidasi',
    ]);
}


    public function validateSuratAdmin($id)
    {
        $suratNomor = SuratNomor::findOrFail($id);

        if ($suratNomor->sn_status == 'ajukan') {
            $nomorSurat = $this->generateNomorSurat($suratNomor);
            $suratNomor->update([
                'sn_nomor' => $nomorSurat,
                'sn_status' => 'validasi',
                'sn_revisi' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil divalidasi.'
        ]);
    }

    public function revisiSurat(Request $request, $id)
    {
        $request->validate([
            'sn_revisi' => 'required',
        ]);
        
        $suratNomor = SuratNomor::findOrFail($id);

        if ($suratNomor->sn_status == 'ajukan') {
            $suratNomor->update([
                'sn_status' => 'revisi',
                'sn_revisi' => $request->sn_revisi,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Revisi surat berhasil.'
        ]);
    }
}

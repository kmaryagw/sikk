<?php

namespace App\Http\Controllers;

use App\Models\SuratNomor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenungguValidasiController extends NomorSuratController
{
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Nomor Surat yang perlu ditinjau';
        $q = $request->query('q');
        $ajukans = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent'])
            ->where('sn_perihal', 'like', '%' . $q . '%')
            ->where('sn_status', 'ajukan')
            ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
            ->paginate(10)
            ->withQueryString();
            // ->get();,
    
        return view('pages.index-menunggu-validasi', [
            'title' => $title,
            'ajukans' => $ajukans,
            'q' => $q,
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
                'sn_status' => 'validasi'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil divalidasi.'
        ]);
    }
}

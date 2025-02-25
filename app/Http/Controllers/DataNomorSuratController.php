<?php

namespace App\Http\Controllers;

use App\Models\SuratNomor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataNomorSuratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

            if ($user->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
    
        $title = 'Data Nomor Surat';
        $q = $request->query('q');
        $dataSurats = SuratNomor::with(['unitKerja', 'lingkup', 'organisasiJabatan', 'OrganisasiJabatan.parent.parent', 'lingkup.perihal', 'lingkup.perihal.fungsi'])
            ->where('sn_perihal', 'like', '%' . $q . '%')
            ->orderBy('sn_status', 'desc')
            ->orderByRaw("STR_TO_DATE(sn_tanggal, '%Y-%m-%d') ASC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(sn_nomor, '/', 1) AS UNSIGNED) ASC")
            ->paginate(10)
            ->withQueryString();
            // ->get();
    
        return view('pages.index-data-nomor-surat', [
            'title' => $title,
            'dataSurats' => $dataSurats,
            'q' => $q,
            'type_menu' => 'surat',
            'sub_menu' => 'datanomorsurat',
        ]);
    }
}

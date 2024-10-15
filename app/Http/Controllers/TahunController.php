<?php

namespace App\Http\Controllers;

use App\Models\tahun_kerja;
use Illuminate\Http\Request;

class TahunController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Tahun Kerja';
        $q = $request->query('q');
        $tahuns = tahun_kerja::where('th_tahun', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $tahuns->firstItem();
        
        return view('pages.index-tahun', [
            'title' => $title,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }
}

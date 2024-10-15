<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use Illuminate\Http\Request;

class PeriodeMonevController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Rencana Strategis';
        $q = $request->query('q');
        $periodems = periode_monev::where('pm_nama', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $periodems->firstItem();
        
        return view('pages.index-periode-monev', [
            'title' => $title,
            'periodems' => $periodems,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }
}

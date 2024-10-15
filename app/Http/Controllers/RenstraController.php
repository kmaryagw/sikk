<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use Illuminate\Http\Request;

class RenstraController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Rencana Strategis';
        $q = $request->query('q');
        $renstras = Renstra::where('ren_nama', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $renstras->firstItem();
        
        return view('pages.index-renstra', [
            'title' => $title,
            'renstras' => $renstras,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }
}

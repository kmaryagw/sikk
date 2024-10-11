<?php

namespace App\Http\Controllers;

use App\Models\program_studi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Prodi';
        $q = $request->query('q');
        $prodis = program_studi::where('nama_prodi', 'like', '%' . $q . '%')
        ->paginate(10)
        ->withQueryString();
        $no = $prodis->firstItem();
        
        return view('pages.index-prodi', [
            'title' => $title,
            'prodis' => $prodis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
        ]);
    }

}

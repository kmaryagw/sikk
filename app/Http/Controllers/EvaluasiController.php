<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use Illuminate\Http\Request;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Evaluasi';
        $q = $request->query('q');

        $evaluasis = Evaluasi::with(['prodi', 'tahun_kerja'])
            ->whereHas('prodi', function($query) use ($q) {
                $query->where('nama_prodi', 'like', '%' . $q . '%');
            })
            ->orderBy('nama_prodi', 'asc')
            ->paginate(10)
            ->withQueryString();

        $no = $evaluasis->firstItem();
        
        return view('pages.index-evaluasi', [
            'title' => $title,
            'evaluasis' => $evaluasis,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'evaluasi',
        ]);
    }
}

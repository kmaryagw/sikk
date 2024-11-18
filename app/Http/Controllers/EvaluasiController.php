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

    // EvaluasiController.php
public function store(Request $request)
{
    $request->validate([
        'th_id' => 'required',
        'prodi_id' => 'required',
    ]);

    Evaluasi::create([
        'eval_id' => Str::uuid(),
        'th_id' => $request->th_id,
        'prodi_id' => $request->prodi_id,
    ]);

    return redirect()->route('evaluasi.index')->with('success', 'Evaluasi berhasil ditambahkan');
}

}

<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\program_studi;
use App\Models\tahun_kerja;
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
            ->join('program_studi', 'evaluasi.prodi_id', '=', 'program_studi.prodi_id')
            ->orderBy('program_studi.nama_prodi', 'asc')
            ->paginate(10)
            ->withQueryString();

        $prodis = program_studi::all();
        $tahuns = tahun_kerja::all();

        $no = $evaluasis->firstItem();
        
        return view('pages.index-evaluasi', [
            'title' => $title,
            'evaluasis' => $evaluasis,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'evaluasi',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:program_studi,prodi_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
        ]);

        try {
            $existingEvaluasi = Evaluasi::where('prodi_id', $request->prodi_id)
                ->where('th_id', $request->th_id)
                ->first();

            if ($existingEvaluasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prodi ini sudah ada untuk tahun yang sama.',
                ]);
            }

            $evaluasi = Evaluasi::create([
                'eval_id' => 'EV' . md5(uniqid(rand(), true)),
                'prodi_id' => $request->prodi_id,
                'th_id' => $request->th_id,
                'status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Evaluasi berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ]);
        }
    }

    public function fill($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);

        if ($evaluasi->status == 1) {
            return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
        }

        return view('evaluasi.isi-evaluasi', compact('evaluasi'));
    }

    public function final($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);

        if ($evaluasi->status == 1) {
            return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
        }

        if (!$evaluasi->isFilled()) {
            return redirect()->route('evaluasi.index')->with('error', 'Data harus diisi terlebih dahulu sebelum final.');
        }

        $evaluasi->status = 1;
        $evaluasi->save();

        return redirect()->route('evaluasi.index')->with('success', 'Evaluasi berhasil diselesaikan.');
    }

    public function show($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);
    
        return view('evaluasi.show', compact('evaluasi'));
    }
}
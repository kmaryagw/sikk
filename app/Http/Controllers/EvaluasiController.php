<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Evaluasi_Detail;
use App\Models\program_studi;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EvaluasiController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Evaluasi';
    $q = $request->query('q');

    // Ubah relasi untuk hanya menggunakan targetIndikator
    $evaluasis = Evaluasi::with(['targetIndikator'])
        ->whereHas('targetIndikator', function ($query) use ($q) {
            // Filter berdasarkan prodi yang ada di targetIndikator
            $query->whereHas('prodi', function ($query) use ($q) {
                $query->where('nama_prodi', 'like', '%' . $q . '%');
            });
        })
        ->paginate(10);

    $no = $evaluasis->firstItem();

    $prodis = program_studi::all();
    $tahuns = tahun_kerja::all();

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
            $targetIndikator = target_indikator::where('prodi_id', $request->prodi_id)->first();

            if (!$targetIndikator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prodi ini tidak memiliki target indikator.',
                ]);
            }

            $tahun = target_indikator::where('th_id', $request->th_id)->first();

            if (!$tahun) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun ini tidak memiliki target indikator.',
                ]);
            }

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

    public function indexDetail($eval_id)
    {
        $Evaluasi = Evaluasi::with('targetIndikator.prodi', 'targetIndikator.tahunKerja')->findOrFail($eval_id);

        $Evaluasis = Evaluasi_Detail::where('eval_id', $eval_id)->get();

        return view('pages.index-detail-evaluasi', [
            'Evaluasi' => $Evaluasi,
            'Evaluasis' => $Evaluasis,
            'type_menu' => 'evaluasi',
        ]);
    }

    public function createDetail($eval_id)
{
    $evaluasi = Evaluasi::with('targetIndikator.prodi', 'targetIndikator.tahunKerja',  'targetIndikator.indikatorKinerja')->findOrFail($eval_id);
    

    if ($evaluasi->status == 1) {
        return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
    }

    $targetIndikators = target_indikator::all();

    return view('pages.create-detail-evaluasi', [
        'evaluasi' => $evaluasi,
        'targetIndikators' => $targetIndikators,
        'type_menu' => 'evaluasi',
    ]);
}

public function storeDetail(Request $request, $eval_id)
{
    $validated = $request->validate([
        'ti_id' => 'required|exists:target_indikator,ti_id',
        'evald_target' => 'required|string',
        'evald_keterangan' => 'nullable|string',
    ]);

    try {
        $evaluasi = Evaluasi::findOrFail($eval_id);

        if ($evaluasi->status == 1) {
            return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final.');
        }

        Evaluasi_Detail::create([
            'evald_id' => 'ED' . md5(uniqid(rand(), true)),
            'eval_id' => $eval_id,
            'ti_id' => $request->ti_id,
            'evald_target' => $request->evald_target,
            'evald_keterangan' => $request->evald_keterangan,
        ]);

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('evaluasi.index-detail', $eval_id);
    } catch (\Exception $e) {
        return redirect()->route('evaluasi.index-detail', $eval_id)->with('error', 'Terjadi kesalahan saat menyimpan data.');
    }
}

public function editDetail($evald_id)
{
    $evaluasiDetail = Evaluasi_Detail::with([
        'targetIndikator.prodi', 
        'targetIndikator.tahunKerja',
        'unitKerja' // Relasi unit kerja
    ])->findOrFail($evald_id);

    $evaluasi = Evaluasi::findOrFail($evaluasiDetail->eval_id);

    if ($evaluasi->status == 1) {
        return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
    }

    // Tambahkan relasi atau data target indikator
    $targetIndikators = $evaluasiDetail->targetIndikator()->get(); // Mengambil data terkait

    $unitKerja = $evaluasiDetail->unitKerja; // Ambil unit kerja terkait
    $rencanaKerja = $unitKerja ? $unitKerja->rencanaKerja()->with('monitoring')->get() : [];

    return view('pages.edit-detail-evaluasi', [
        'evaluasi' => $evaluasi,
        'evaluasiDetail' => $evaluasiDetail,
        'targetIndikators' => $targetIndikators, // Pastikan ini ada
        'rencanaKerja' => $rencanaKerja, // Data monitoring terkait
        'type_menu' => 'evaluasi',
    ]);
}



public function updateDetail(Request $request, $evald_id)
{
    // Validasi input
    $validated = $request->validate([
        'ti_id' => 'required|exists:target_indikator,ti_id',
        'evald_target' => 'required|string',
        'evald_keterangan' => 'nullable|string',
    ]);

    try {
        // Cari Evaluasi Detail dan Evaluasi terkait
        $evaluasiDetail = Evaluasi_Detail::findOrFail($evald_id);
        $evaluasi = Evaluasi::findOrFail($evaluasiDetail->eval_id);

        // Pastikan status evaluasi belum final
        if ($evaluasi->status == 1) {
            return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
        }

        // Update detail evaluasi
        $evaluasiDetail->update([
            'ti_id' => $request->ti_id,
            'evald_target' => $request->evald_target,
            'evald_keterangan' => $request->evald_keterangan,
        ]);

        // Redirect ke halaman index detail evaluasi
        Alert::success('Sukses', 'Data Berhasil Diperbarui');
        return redirect()->route('evaluasi.index-detail', $evaluasiDetail->eval_id);
    } catch (\Exception $e) {
        // Jika terjadi kesalahan, redirect dengan pesan error
        return redirect()->route('evaluasi.index-detail', $evaluasiDetail->eval_id)
                         ->with('error', 'Terjadi kesalahan saat memperbarui data.');
    }
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

    public function destroyDetail($eval_id, $evald_id)
{
    try {
        $evaluasiDetail = Evaluasi_Detail::findOrFail($evald_id);

        // Pastikan status evaluasi belum final
        $evaluasi = Evaluasi::findOrFail($eval_id);
        if ($evaluasi->status == 1) {
            return redirect()->route('evaluasi.index-detail', $eval_id)->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
        }

        // Hapus data
        $evaluasiDetail->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('evaluasi.index-detail', $eval_id)->with('success', 'Detail Evaluasi berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->route('evaluasi.index-detail', $eval_id)->with('error', 'Terjadi kesalahan saat menghapus data.');
    }
}

}

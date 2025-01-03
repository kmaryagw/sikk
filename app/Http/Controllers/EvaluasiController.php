<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Evaluasi_Detail;
use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Evaluasi';
        $q = $request->query('q');

        $evaluasis = Evaluasi::with(['targetIndikator'])
            ->whereHas('targetIndikator', function ($query) use ($q) {
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
        $Evaluasis = Evaluasi_Detail::where('eval_id', $eval_id)->get();

        $Evaluasi = Evaluasi::find($eval_id);
        $prodi_id = $Evaluasi->prodi_id;
        $th_id = $Evaluasi->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();
        
        $evaluasiDetail = Evaluasi_Detail::where('eval_id', $eval_id)->first() ?? new Evaluasi_Detail();

        return view('pages.index-detail-evaluasi', [
            'Evaluasi' => $Evaluasi,
            'Evaluasis' => $Evaluasis,
            'targetIndikators' => $targetIndikators,
            'evaluasiDetail' => $evaluasiDetail,
            'type_menu' => 'evaluasi',
        ]);
    }

    public function editDetail($eval_id)
    {
        $evaluasi = Evaluasi::with(['prodi', 'tahunKerja'])->findOrFail($eval_id);
        $status = ['tercapai','tidak tercapai','tidak terlaksana'];
        $targetIndikator = target_indikator::where('prodi_id', $evaluasi->prodi_id)
            ->where('th_id', $evaluasi->th_id)
            ->with('indikatorKinerja')
            ->first();

        $evaluasiDetail = Evaluasi_Detail::where('eval_id', $eval_id)->first() ?? new Evaluasi_Detail();

        $indikatorKinerja = $targetIndikator->indikatorKinerja;
            if (!$indikatorKinerja) {
                return redirect()->route('evaluasi.index')->with('error', 'Indikator Kinerja tidak ditemukan.');
            }
        
        $programKerja = RencanaKerja::with(['periodes', 'tahunKerja', 'monitoring', 'realisasi'])
            ->join('rencana_kerja_target_indikator', 'rencana_kerja.rk_id', '=', 'rencana_kerja_target_indikator.rk_id')
            ->join('target_indikator', 'rencana_kerja_target_indikator.ti_id', '=', 'target_indikator.ti_id')
            ->where('target_indikator.ik_id', optional($indikatorKinerja)->ik_id)
            ->select('rencana_kerja.*')
            ->orderBy('rk_nama', 'asc')
            ->get();

        return view('pages.edit-detail-evaluasi', [
            'evaluasi' => $evaluasi,
            'targetIndikator' => $targetIndikator,
            'status' => $status,
            'programKerja' => $programKerja,
            'evaluasiDetail' => $evaluasiDetail,
            'type_menu' => 'evaluasi',
        ]);
    }

    public function updateDetail(Request $request, $eval_id)
    {
        $indikatorKinerja = IndikatorKinerja::find($request->ik_id);
        
        $validated = $request->validate([
            'evald_capaian' => 'required',
            'evald_keterangan' => 'nullable|string',
            'evald_status' => 'required|in:tercapai,tidak tercapai,tidak terlaksana',
        ]);

        if ($indikatorKinerja) {
            if ($indikatorKinerja->ik_ketercapaian == 'nilai') {
                $validationRules['evald_capaian'] = 'required|numeric|min:0';
            } elseif ($indikatorKinerja->ik_ketercapaian == 'persentase') {
                $validationRules['evald_capaian'] = 'required|numeric|min:0|max:100';
            } elseif ($indikatorKinerja->ik_ketercapaian == 'ketersediaan') {
                $validationRules['evald_capaian'] = 'required|string';
            }
        }

        try {
            $evaluasi = Evaluasi::findOrFail($eval_id);
            $targetIndikator = target_indikator::where('prodi_id', $evaluasi->prodi_id)
                ->where('th_id', $evaluasi->th_id)
                ->first();

            if ($evaluasi->status == 1) {
                return redirect()->route('evaluasi.index')->with('error', 'Evaluasi ini sudah final dan tidak dapat diubah.');
            }

            Evaluasi_Detail::create([
                'evald_id' => Str::uuid(),
                'eval_id' => $eval_id,
                'ti_id' => $targetIndikator->ti_id,
                'evald_target' => $targetIndikator->ti_target,
                'evald_capaian' => $validated['evald_capaian'],
                'evald_keterangan' => $validated['evald_keterangan'],
                'evald_status' => $validated['evald_status'],
            ]);

            Alert::success('Sukses', 'Data Berhasil Ditambah');

            return redirect()->route('evaluasi.index-detail', $eval_id);
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan!');
            return redirect()->route('evaluasi.index-detail', $eval_id);
        }
    }

    public function final($id)
    {
        $evaluasi = Evaluasi::findOrFail($id);

        if ($evaluasi->status == 1) {
            return response()->json(['success' => false, 'message' => 'Evaluasi ini sudah final dan tidak dapat diubah.']);
        }

        if (!$evaluasi->isFilled()) {
            return response()->json(['success' => false, 'message' => 'Data harus diisi terlebih dahulu sebelum final.']);
        }

        $evaluasi->status = 1;
        $evaluasi->save();

        return response()->json(['success' => true, 'message' => 'Evaluasi berhasil diselesaikan.']);
    }

    public function show($eval_id)
    {
        $Evaluasis = Evaluasi_Detail::where('eval_id', $eval_id)->get();

        $Evaluasi = Evaluasi::find($eval_id);
        $prodi_id = $Evaluasi->prodi_id;
        $th_id = $Evaluasi->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();
        
        $evaluasiDetail = Evaluasi_Detail::where('eval_id', $eval_id)->first() ?? new Evaluasi_Detail();

        return view('pages.index-show-evaluasi', [
            'Evaluasi' => $Evaluasi,
            'Evaluasis' => $Evaluasis,
            'targetIndikators' => $targetIndikators,
            'evaluasiDetail' => $evaluasiDetail,
            'type_menu' => 'evaluasi',
        ]);
    }

    public function destroyDetail($eval_id)
    {
        $evaluasi = Evaluasi::find($eval_id);

        if ($evaluasi) {
            $evaluasi->evaluasiDetails()->delete();
            $evaluasi->status = 0;
            $evaluasi->save();

            Alert::success('Sukses', 'Data Berhasil Dihapus');
            return redirect()->route('evaluasi.index-detail', $eval_id);
        }
        Alert::error('Error', 'Terjadi Kesalahan!');
        return redirect()->route('evaluasi.index-detail', $eval_id);
    }

}

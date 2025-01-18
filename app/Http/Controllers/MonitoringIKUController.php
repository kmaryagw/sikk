<?php

namespace App\Http\Controllers;

use App\Models\MonitoringIKU;
use App\Models\MonitoringIKU_Detail;
use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Symfony\Contracts\Service\Attribute\Required;

class MonitoringIKUController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Monitoring IKU';
        $q = $request->query('q');

        $monitoringikus = MonitoringIKU::with(['targetIndikator'])
            ->whereHas('targetIndikator', function ($query) use ($q) {
                $query->whereHas('prodi', function ($query) use ($q) {
                    $query->where('nama_prodi', 'like', '%' . $q . '%');
                });
            })
            ->paginate(10);

        $no = $monitoringikus->firstItem();

        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->orderBy('th_tahun', 'asc')->get();


        return view('pages.index-monitoringiku', [
            'title' => $title,
            'monitoringikus' => $monitoringikus,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'monitoringiku',
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

            $existisMonitoringIKU = MonitoringIKU::where('prodi_id', $request->prodi_id)
                ->where('th_id', $request->th_id)
                ->first();

            if ($existisMonitoringIKU) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prodi ini sudah ada untuk tahun yang sama.',
                ]);
            }

            $monitoringiku = MonitoringIKU::create([
                'mti_id' => 'EV' . md5(uniqid(rand(), true)),
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

    public function indexDetail($mti_id)
    {
        // $Monitoringikus = MonitoringIKU_Detail::where('mti_id', $mti_id)->get(); //Evaluasis

        $Monitoringiku = MonitoringIKU::find($mti_id); //Evaluasi
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();
        
        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)->first() ?? new MonitoringIKU_Detail();

        return view('pages.index-detail-monitoringiku', [
            'Monitoringiku' => $Monitoringiku,
            // 'Monitoringikus' => $Monitoringikus,
            'targetIndikators' => $targetIndikators,
            'monitoringikuDetail' => $monitoringikuDetail,
            'type_menu' => 'monitoringiku',
        ]);
    }

    public function editDetail($mti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai','tidak tercapai','tidak terlaksana'];
        $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->with('indikatorKinerja')
            ->first();

        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)->first() ?? new MonitoringIKU_Detail();

        $indikatorKinerja = $targetIndikator->indikatorKinerja;
            if (!$indikatorKinerja) {
                return redirect()->route('monitoringiku.index')->with('error', 'Indikator Kinerja tidak ditemukan.');
            }
        
        $programKerja = RencanaKerja::with(['periodes', 'tahunKerja', 'monitoring', 'realisasi'])
            ->join('rencana_kerja_target_indikator', 'rencana_kerja.rk_id', '=', 'rencana_kerja_target_indikator.rk_id')
            ->join('target_indikator', 'rencana_kerja_target_indikator.ti_id', '=', 'target_indikator.ti_id')
            ->where('target_indikator.ik_id', optional($indikatorKinerja)->ik_id)
            ->select('rencana_kerja.*')
            ->orderBy('rk_nama', 'asc')
            ->get();

        return view('pages.edit-detail-monitoringiku', [
            'monitoringiku' => $monitoringiku,
            'targetIndikator' => $targetIndikator,
            'status' => $status,
            'programKerja' => $programKerja,
            'monitoringikuDetail' => $monitoringikuDetail,
            'type_menu' => 'monitoringiku',
        ]);
    }

    public function updateDetail(Request $request, $mti_id)
    {
        $indikatorKinerja = IndikatorKinerja::find($request->ik_id);
        
        $validated = $request->validate([
            'mtid_capaian' => 'required',
            'mtid_keterangan' => 'nullable|string',
            'mtid_status' => 'required|in:tercapai,tidak tercapai,tidak terlaksana',
            'mtid_url' => 'required|url',
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
            $monitoringiku = MonitoringIKU::findOrFail($mti_id);
            $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
                ->where('th_id', $monitoringiku->th_id)
                ->first();

            if ($monitoringiku->status == 1) {
                return redirect()->route('monitoringiku.index')->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
            }

            MonitoringIKU_Detail::create([
                'mtid_id' => Str::uuid(),
                'mti_id' => $mti_id,
                'ti_id' => $targetIndikator->ti_id,
                'mtid_target' => $targetIndikator->ti_target,
                'mtid_capaian' => $validated['mtid_capaian'],
                'mtid_keterangan' => $validated['mtid_keterangan'],
                'mtid_status' => $validated['mtid_status'],
                'mtid_url' => $validated['mtid_url'],
            ]);

            Alert::success('Sukses', 'Data Berhasil Ditambah');

            return redirect()->route('monitoringiku.index-detail', $mti_id);
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan!');
            return redirect()->route('monitoringiku.index-detail', $mti_id);
        }
    }

    public function final($id)
    {
        $monitoringiku = MonitoringIKU::findOrFail($id);

        if ($monitoringiku->status == 1) {
            return response()->json(['success' => false, 'message' => 'Monitoring IKU ini sudah final dan tidak dapat diubah.']);
        }

        if (!$monitoringiku->isFilled()) {
            return response()->json(['success' => false, 'message' => 'Data harus diisi terlebih dahulu sebelum final.']);
        }

        $monitoringiku->status = 1;
        $monitoringiku->save();

        return response()->json(['success' => true, 'message' => 'Monitoring IKU berhasil diselesaikan.']);
    }

    public function show($mti_id)
    {
        $Evaluasis = MonitoringIKU_Detail::where('mti_id', $mti_id)->get();

        $Monitoringiku = MonitoringIKU::find($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();
        
        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)->first() ?? new MonitoringIKU_Detail();

        return view('pages.index-show-monitoringiku', [
            'Monitoringiku' => $Monitoringiku,
            'Evaluasis' => $Evaluasis,
            'targetIndikators' => $targetIndikators,
            'monitoringikuDetail' => $monitoringikuDetail,
            'type_menu' => 'evaluasi',
        ]);
    }
}

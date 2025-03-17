<?php

namespace App\Http\Controllers;

use App\Models\HistoryMonitoringIKU;
use App\Models\MonitoringIKU;
use App\Models\MonitoringIKU_Detail;
use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Symfony\Contracts\Service\Attribute\Required;

class MonitoringIKUController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'fakultas') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();

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

        if ($user->role == 'fakultas') {
            $prodis = program_studi::where('id_fakultas', $user->id_fakultas)
                ->orderBy('nama_prodi', 'asc')
                ->get();
        } else {
            $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        }

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
                'message' => 'Data berhasil disimpan!',
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
        $Monitoringiku = MonitoringIKU::find($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja', 'monitoringDetail')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();

        return view('pages.index-detail-monitoringiku', [
            'Monitoringiku' => $Monitoringiku,
            'targetIndikators' => $targetIndikators,
            'type_menu' => 'monitoringiku',
        ]);
    }

    public function createDetail($mti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];

        $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->with('indikatorKinerja')
            ->get();

        if ($targetIndikator->isEmpty()) {
            return redirect()->route('monitoringiku.index')->with('error', 'Indikator Kinerja tidak ditemukan.');
        }

        $indikatorKinerja = $targetIndikator->pluck('indikatorKinerja');

        $monitoringikuDetail = MonitoringIKU_Detail::whereIn('ti_id', $targetIndikator->pluck('ti_id'))->get();

        return view('pages.create-detail-monitoringiku', [
            'monitoringiku' => $monitoringiku,
            'targetIndikator' => $targetIndikator,
            'status' => $status,
            'monitoringikuDetail' => $monitoringikuDetail,
            'type_menu' => 'monitoringiku',
        ]);
    }

    public function storeDetail(Request $request, $mti_id) 
    {
        $validated = $request->validate([
            'ti_id' => 'required|array',
            'ti_id.*' => 'required|string',
            'mtid_capaian' => 'nullable|array',
            'mtid_capaian.*' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $type = $request->input("items.$index.type");
            
                    if ($type === 'number' && !is_numeric($value)) {
                        $fail("The $attribute must be a number.");
                    }
                }
            ],
            'mtid_keterangan' => 'nullable|array',
            'mtid_keterangan.*' => 'nullable|string',
            'mtid_status' => 'nullable|array',
            'mtid_status.*' => 'nullable|in:tercapai,tidak tercapai,tidak terlaksana',
            'mtid_url' => 'nullable|array',
            'mtid_url.*' => 'nullable|url',
        ]);

        try {
            $monitoringiku = MonitoringIKU::findOrFail($mti_id);
    
            foreach ($request->ti_id as $index => $ti_id) {
                $targetIndikator = target_indikator::findOrFail($ti_id);
        
                if ($monitoringiku->status == 1) {
                    return redirect()->route('monitoringiku.index')->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
                }
        
                $customPrefix = 'MTID';
                $timestamp = time();
                $uuid = strtoupper(md5($timestamp . $index));
                $mtid_id = $customPrefix . $uuid;
        
                $mtid_capaian = $request->mtid_capaian[$index] ?? null;
                $mtid_keterangan = $request->mtid_keterangan[$index] ?? null;
                $mtid_status = $request->mtid_status[$index] ?? null;
                $mtid_url = $request->mtid_url[$index] ?? null;
        
                $existingDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
                    ->where('ti_id', $ti_id)
                    ->first();
        
                $detail = MonitoringIKU_Detail::updateOrCreate(
                    [
                        'mti_id' => $mti_id,
                        'ti_id' => $ti_id
                    ],
                    [
                        'mtid_id' => $mtid_id,
                        'mtid_target' => $targetIndikator->ti_target,
                        'mtid_capaian' => $mtid_capaian,
                        'mtid_keterangan' => $mtid_keterangan,
                        'mtid_status' => $mtid_status,
                        'mtid_url' => $mtid_url,
                    ]
                );
        
                if (!$existingDetail || 
                    ($existingDetail->mtid_capaian != $mtid_capaian ||
                    $existingDetail->mtid_keterangan != $mtid_keterangan ||
                    $existingDetail->mtid_status != $mtid_status ||
                    $existingDetail->mtid_url != $mtid_url)) {

                    if (!is_null($mtid_capaian) || !is_null($mtid_keterangan) || !is_null($mtid_status) || !is_null($mtid_url)) {
                        HistoryMonitoringIKU::create([
                            'hmi_id' => 'HMI' . strtoupper(md5(time() . $mtid_id)),
                            'mtid_id' => $detail->mtid_id,
                            'ti_id' => $ti_id,
                            'hmi_target' => $targetIndikator->ti_target,
                            'hmi_capaian' => $mtid_capaian,
                            'hmi_keterangan' => $mtid_keterangan,
                            'hmi_status' => $mtid_status,
                            'hmi_url' => $mtid_url,
                        ]);
                    }
                }
            }        
    
            Alert::success('Sukses', 'Data Berhasil Ditambah');
            return redirect()->route('monitoringiku.index-detail', $mti_id);
    
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan: ' . $e->getMessage());
            return redirect()->route('monitoringiku.index-detail', $mti_id);
        }
    }


    public function editDetail($mti_id, $ti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai','tidak tercapai','tidak terlaksana'];
        $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->where('ti_id', $ti_id)
            ->with('indikatorKinerja')
            ->first();

        $monitoringikuDetail = MonitoringIKU_Detail::where('ti_id', $ti_id)->first() ?? new MonitoringIKU_Detail();

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

    public function updateDetail(Request $request, $mti_id, $ti_id)
    {
        $validated = $request->validate([
            'mtid_capaian' => 'required',
            'mtid_keterangan' => 'nullable|string',
            'mtid_status' => 'required|in:tercapai,tidak tercapai,tidak terlaksana',
            'mtid_url' => 'required|url',
        ]);

        try {
            $monitoringiku = MonitoringIKU::findOrFail($mti_id);
            $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
                ->where('th_id', $monitoringiku->th_id)
                ->where('ti_id', $ti_id)
                ->first();

            if (!$targetIndikator) {
                return redirect()->route('monitoringiku.index')->with('error', 'Target Indikator tidak ditemukan.');
            }

            if ($monitoringiku->status == 1) {
                return redirect()->route('monitoringiku.index')->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
            }

            $monitoringIKUDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)->where('ti_id', $ti_id)->first();

            if ($monitoringIKUDetail) {
                $monitoringIKUDetail->update([
                    'mtid_capaian' => $validated['mtid_capaian'],
                    'mtid_keterangan' => $validated['mtid_keterangan'],
                    'mtid_status' => $validated['mtid_status'],
                    'mtid_url' => $validated['mtid_url'],
                ]);
            } else {
                $monitoringIKUDetail = MonitoringIKU_Detail::create([
                    'mtid_id' => 'MTID' . Str::uuid(),
                    'mti_id' => $mti_id,
                    'ti_id' => $targetIndikator->ti_id,
                    'mtid_target' => $targetIndikator->ti_target,
                    'mtid_capaian' => $validated['mtid_capaian'],
                    'mtid_keterangan' => $validated['mtid_keterangan'],
                    'mtid_status' => $validated['mtid_status'],
                    'mtid_url' => $validated['mtid_url'],
                ]);
            }

            HistoryMonitoringIKU::create([
                'hmi_id' => 'HMI' . Str::uuid(),
                'mtid_id' => $monitoringIKUDetail->mtid_id,
                'ti_id' => $targetIndikator->ti_id,
                'hmi_target' => $targetIndikator->ti_target,
                'hmi_capaian' => $validated['mtid_capaian'],
                'hmi_keterangan' => $validated['mtid_keterangan'],
                'hmi_status' => $validated['mtid_status'],
                'hmi_url' => $validated['mtid_url'],
            ]);

            Alert::success('Sukses', 'Data Berhasil Diperbarui');
            return redirect()->route('monitoringiku.index-detail', $mti_id);
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan: ' . $e->getMessage());
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
        $Monitoringiku = MonitoringIKU::find($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        $targetIndikators = target_indikator::with('indikatorKinerja', 'monitoringDetail')
            ->where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->get();

        return view('pages.index-show-monitoringiku', [
            'Monitoringiku' => $Monitoringiku,
            'targetIndikators' => $targetIndikators,
            'type_menu' => 'monitoringiku',
        ]);
    }
}

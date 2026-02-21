<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\tahun_kerja;
use Illuminate\Support\Str;
use App\Models\RencanaKerja;
use Illuminate\Http\Request;
use App\Models\MonitoringIKU;
use App\Models\program_studi;
use App\Models\IndikatorKinerja;
use App\Models\target_indikator;
use Illuminate\Support\Facades\DB;
use App\Models\MonitoringFinalUnit;
use App\Models\HistoryMonitoringIKU;
use App\Models\MonitoringIKU_Detail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\MonitoringIKUDetailExport;
use Symfony\Contracts\Service\Attribute\Required;
use Barryvdh\DomPDF\Facade\Pdf; 

class MonitoringIKUController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'unit kerja' && Auth::user()->role !== 'fakultas' && Auth::user()->role !== 'prodi') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $title = 'Data Monitoring IKU';
        $q = $request->query('q');
        $th_id = $request->query('th_id');

        if (!$request->has('th_id')) {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            $th_id = $tahunAktif ? $tahunAktif->th_id : null;
        }

        $query = MonitoringIKU::with(['prodi', 'tahunKerja']);

        if ($th_id) {
            $query->where('th_id', $th_id);
        }

        // --- LOGIKA FILTER UNIT KERJA (DEKANAT VS UMUM) ---
        if ($user->role === 'unit kerja') {
            
            // Cek: Apakah unit kerja ini terdaftar sebagai pengelola di salah satu prodi?
            $isManager = program_studi::where('unit_id_pengelola', $user->unit_id)->exists();

            if ($isManager) {
                // JIKA DIA DEKANAT: Filter hanya prodi yang dia kelola
                $allowedProdiIds = program_studi::where('unit_id_pengelola', $user->unit_id)
                                    ->pluck('prodi_id')
                                    ->toArray();

                $query->whereIn('prodi_id', $allowedProdiIds);
                $prodis = program_studi::whereIn('prodi_id', $allowedProdiIds)->get();
            } else {
                // JIKA DIA UNIT UMUM (BAAK/LPM/DLL): Lihat seluruh prodi
                $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
            }

        } elseif ($user->role === 'prodi') {
            $query->where('prodi_id', $user->prodi_id);
            $prodis = program_studi::where('prodi_id', $user->prodi_id)->get();

        } elseif ($user->role === 'fakultas') {
            $query->whereHas('prodi', function($sq) use ($user) {
                $sq->where('id_fakultas', $user->id_fakultas);
            });
            $prodis = program_studi::where('id_fakultas', $user->id_fakultas)->get();

        } else {
            $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        }

        if ($q) {
            $query->whereHas('prodi', function ($subQuery) use ($q) {
                $subQuery->where('nama_prodi', 'like', '%' . $q . '%');
            });
        }

        $monitoringikus = $query->paginate(10)->withQueryString();
        $no = $monitoringikus->firstItem();
        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();

        return view('pages.index-monitoringiku', [
            'title' => $title,
            'monitoringikus' => $monitoringikus,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'q' => $q,
            'th_id' => $th_id,
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

    public function indexDetail($mti_id, Request $request)
    {
        $Monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;
        $user = Auth::user();

        $q = trim($request->input('q', ''));
        $unitKerjaFilter = $request->input('unit_kerja', '');

        $targetIndikatorsQuery = target_indikator::select('target_indikator.*') 
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->where('target_indikator.prodi_id', $prodi_id)
            ->where('target_indikator.th_id', $th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($prodi_id, $th_id) {
                    $sub->where('prodi_id', $prodi_id)
                        ->where('th_id', $th_id);
                },
                'monitoringDetail' => function ($q) use ($mti_id) {
                    $q->where('mti_id', $mti_id);
                },
                'historyMonitoring',
            ]);

        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        if ($unitKerjaFilter) {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
            });
        }

        if ($user->role !== 'admin' && $user->role !== 'fakultas' && $user->role !== 'prodi') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); 
        }

        $targetIndikators = $targetIndikatorsQuery->get()->sortBy(function ($item) {
            return optional($item->indikatorKinerja)->ik_kode ?? '';
        }, SORT_NATURAL | SORT_FLAG_CASE)->values();

        $unitKerjas = UnitKerja::all();

        return view('pages.index-detail-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'unitKerjas'        => $unitKerjas,
            'q'                 => $q,
            'unitKerjaFilter'   => $unitKerjaFilter,
            'type_menu'         => 'monitoringiku',
        ]);
    }

    public function createDetail($mti_id, Request $request)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $user = Auth::user();
        
        if ($user->role === 'prodi') {
            return redirect()->back()->with('error', 'Akun Prodi hanya memiliki akses Lihat (Read-Only).');
        }

        $q = trim($request->input('q', '')); 
        $unitKerjaFilter = $request->input('unit_kerja', ''); 

        $targetIndikatorQuery = target_indikator::select('target_indikator.*')
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->where('target_indikator.prodi_id', $monitoringiku->prodi_id)
            ->where('target_indikator.th_id', $monitoringiku->th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($monitoringiku) {
                    $sub->where('prodi_id', $monitoringiku->prodi_id)
                        ->where('th_id', $monitoringiku->th_id);
                },
                'monitoringDetail' => function ($query) use ($mti_id) {
                    $query->where('mti_id', $mti_id);
                },
            ]);

        if ($q !== '') {
            $targetIndikatorQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        $isAdminOrFakultas = ($user->role === 'admin' || $user->role === 'fakultas');

        if ($isAdminOrFakultas) {
            if ($unitKerjaFilter) {
                $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                    $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
                });
            }
        } else {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            });
        }

        /**
         * LOGIKA PENGURUTAN NATURAL (CAST)
         */
        // $targetIndikatorQuery->orderByRaw("LENGTH(SUBSTRING_INDEX(indikator_kinerja.ik_kode, '.', 1)) ASC, 
        // SUBSTRING_INDEX(indikator_kinerja.ik_kode, '.', 1) ASC, LENGTH(indikator_kinerja.ik_kode) ASC, indikator_kinerja.ik_kode ASC");

        $targetIndikator = $targetIndikatorQuery->get()->sortBy(function ($item) {
            return optional($item->indikatorKinerja)->ik_kode ?? '';
        }, SORT_NATURAL | SORT_FLAG_CASE)->values();

        if ($targetIndikator->isEmpty() && empty($q) && empty($unitKerjaFilter)) {
            return redirect()->route('monitoringiku.index')
                ->with('error', 'Tidak ada indikator yang tersedia atau Anda tidak memiliki akses.');
        }

        \DB::transaction(function () use ($targetIndikator, $mti_id) {
            foreach ($targetIndikator as $target) {
                MonitoringIKU_Detail::firstOrCreate(
                    ['mti_id' => $mti_id, 'ti_id' => $target->ti_id],
                    [
                        'mtid_id'     => 'MTID' . \Str::uuid(),
                        'mtid_target' => $target->ti_target,
                        'mtid_status' => 'Draft',
                    ]
                );
            }
        });

        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
            ->whereIn('ti_id', $targetIndikator->pluck('ti_id'))
            ->get()
            ->keyBy('ti_id'); 

        $unitKerjas = \App\Models\UnitKerja::orderBy('unit_nama', 'asc')->get();

        return view('pages.create-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'monitoringikuDetail' => $monitoringikuDetail,
            'unitKerjas'          => $unitKerjas,       
            'selectedUnit'        => $unitKerjaFilter, 
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $isAdminOrFakultas,
            'q'                   => $q,
        ]);
    }

    public function storeDetail(Request $request, $mti_id)
    {
        $request->validate([
            'ti_id' => 'required|array',
            'mtid_url.*' => 'nullable|url', 
        ]);

        try {
            $monitoringiku = MonitoringIKU::findOrFail($mti_id);
            $user = Auth::user();

            if ($monitoringiku->status == 1) {
                return redirect()->back()->with('error', 'Monitoring IKU ini sudah final.');
            }

            \DB::transaction(function () use ($request, $mti_id, $user) {
                
                foreach ($request->ti_id as $ti_id) {
                    $targetIndikator = target_indikator::with('indikatorKinerja')->where('ti_id', $ti_id)->first();
                    if (!$targetIndikator) continue;

                    $existing = MonitoringIKU_Detail::where('mti_id', $mti_id)->where('ti_id', $ti_id)->first();
                    
                    $mtid_id = $existing ? $existing->mtid_id : 'MTID' . \Str::uuid();
                    $jenis_ik = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'nilai');

                    // Ambil data dari request, jika kosong set NULL (agar bisa menghapus data lama)
                    $tipeInput      = $request->mtid_capaian[$ti_id] ?? ''; 
                    $nilaiInput     = $request->capaian_value[$ti_id] ?? '';
                    
                    // Gunakan ternary agar jika input kosong, di database tersimpan NULL
                    $val_keterangan = isset($request->mtid_keterangan[$ti_id]) ? ($request->mtid_keterangan[$ti_id] ?: null) : ($existing->mtid_keterangan ?? null);
                    $val_url        = isset($request->mtid_url[$ti_id]) ? ($request->mtid_url[$ti_id] ?: null) : ($existing->mtid_url ?? null);
                    
                    $val_evaluasi     = $existing->mtid_evaluasi ?? null;
                    $val_tindaklanjut = $existing->mtid_tindaklanjut ?? null;
                    $val_peningkatan  = $existing->mtid_peningkatan ?? null;
                    $val_capaian      = $existing->mtid_capaian ?? null;
                    $val_status       = $existing->mtid_status ?? 'Draft';
                    
                    if ($user->role !== 'admin' && $user->role !== 'fakultas') {
                        if ($tipeInput === 'rasio') {
                            // Gunakan regex agar konsisten dengan updateDetail
                            if (preg_match('/^\d+\s*:\s*\d+$/', $nilaiInput)) {
                                $cleaned = preg_replace('/\s+/', '', $nilaiInput);
                                [$left, $right] = explode(':', $cleaned);
                                $val_capaian = $left . ' : ' . $right;
                            } else {
                                $val_capaian = $nilaiInput; 
                            }
                        } elseif ($tipeInput === 'persentase') {
                            $val_capaian = is_numeric($nilaiInput) ? $nilaiInput . '%' : $nilaiInput;
                        } elseif ($tipeInput === 'ada' || $tipeInput === 'draft') {
                            $val_capaian = $tipeInput;
                        } else {
                            $val_capaian = $nilaiInput;
                        }

                        $val_status = $this->hitungStatus($val_capaian, $targetIndikator->ti_target, $jenis_ik);

                    } else {
                        // Logika Admin/Fakultas
                        $val_evaluasi     = isset($request->mtid_evaluasi[$ti_id]) ? ($request->mtid_evaluasi[$ti_id] ?: null) : $val_evaluasi;
                        $val_tindaklanjut = isset($request->mtid_tindaklanjut[$ti_id]) ? ($request->mtid_tindaklanjut[$ti_id] ?: null) : $val_tindaklanjut;
                        $val_peningkatan  = isset($request->mtid_peningkatan[$ti_id]) ? ($request->mtid_peningkatan[$ti_id] ?: null) : $val_peningkatan;
                    }

                    $detail = MonitoringIKU_Detail::updateOrCreate(
                        ['mti_id' => $mti_id, 'ti_id' => $ti_id],
                        [
                            'mtid_id'           => $mtid_id,
                            'mtid_target'       => $targetIndikator->ti_target,
                            'mtid_capaian'      => $val_capaian,
                            'mtid_keterangan'   => $val_keterangan,
                            'mtid_status'       => $val_status,
                            'mtid_url'          => $val_url,
                            'mtid_evaluasi'     => $val_evaluasi,
                            'mtid_tindaklanjut' => $val_tindaklanjut,
                            'mtid_peningkatan'  => $val_peningkatan,
                        ]
                    );

                    $hasChanged = false;
                    if (!$existing) {
                        $hasChanged = true;
                    } else {
                        if (
                            $existing->mtid_capaian != $val_capaian ||
                            $existing->mtid_status != $val_status ||
                            $existing->mtid_evaluasi != $val_evaluasi ||
                            $existing->mtid_url != $val_url ||
                            $existing->mtid_keterangan != $val_keterangan
                        ) {
                            $hasChanged = true;
                        }
                    }

                    if ($hasChanged) {
                        HistoryMonitoringIKU::create([
                            'hmi_id'           => 'HMI' . \Str::uuid(),
                            'mtid_id'          => $mtid_id,
                            'ti_id'            => $ti_id,
                            'hmi_target'       => $targetIndikator->ti_target,
                            'hmi_capaian'      => $val_capaian,
                            'hmi_keterangan'   => $val_keterangan,
                            'hmi_status'       => $val_status,
                            'hmi_url'          => $val_url,
                            'hmi_evaluasi'     => $val_evaluasi,
                            'hmi_tindaklanjut' => $val_tindaklanjut,
                            'hmi_peningkatan'  => $val_peningkatan,
                        ]);
                    }
                }
            });

            \Alert::success('Sukses', 'Data Berhasil Disimpan');
            return redirect()->route('monitoringiku.index-detail', $mti_id);

        } catch (\Exception $e) {
            \Log::error("Error Store Detail: " . $e->getMessage());
            \Alert::error('Error', 'Terjadi Kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function editDetail($mti_id, $ti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $user = Auth::user();
        if (Auth::user()->role === 'prodi') {
            return redirect()->back()->with('error', 'Akun Prodi hanya memiliki akses Lihat (Read-Only).');
        }
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];

        // Ambil target indikator
        $targetIndikatorQuery = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->where('ti_id', $ti_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($q) use ($monitoringiku) {
                    $q->where('prodi_id', $monitoringiku->prodi_id)
                    ->where('th_id', $monitoringiku->th_id);
                }
            ]);

        // Filter untuk user/unit kerja
        if ($user->role !== 'admin' && $user->role !== 'fakultas') {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                $q->where('unit_kerja.unit_id', $user->unit_id);
            });
        }

        $targetIndikator = $targetIndikatorQuery->first();

        if (!$targetIndikator) {
            return redirect()->route('monitoringiku.index-detail', $mti_id)
                ->with('error', 'Data target indikator tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Ambil atau buat monitoring detail
        $monitoringikuDetail = MonitoringIKU_Detail::firstOrCreate(
            ['mti_id' => $mti_id, 'ti_id' => $ti_id],
            [
                'mtid_id'     => 'MTID'.Str::uuid(),
                'mtid_target' => $targetIndikator->ti_target,
                'mtid_status' => 'Draft'
            ]
        );

        // Hitung status otomatis jika role user dan belum ada status
        if ($user->role !== 'admin' && !$monitoringikuDetail->mtid_status) {
            $jenis = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'nilai');
            $monitoringikuDetail->mtid_status = $this->hitungStatus(
                $monitoringikuDetail->mtid_capaian,
                $targetIndikator->ti_target,
                $jenis
            );
            $monitoringikuDetail->save();
        }

        // Ambil baseline
        $baseline = optional($targetIndikator->baselineTahun)->baseline;

        // Ambil unit kerja
        $unitKerja = UnitKerja::all();

        // Tentukan readonly card untuk admin
        $readonlyCard2 = $user->role === 'admin'; // Card 2 selalu read-only untuk admin
        $readonlyCard3 = $user->role === 'admin' && empty($monitoringikuDetail->mtid_capaian);

        return view('pages.edit-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'monitoringikuDetail' => $monitoringikuDetail,
            'baseline'            => $baseline,
            'unitKerja'           => $unitKerja,
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin' || $user->role === 'fakultas',
            'readonlyCard2'       => $readonlyCard2,
            'readonlyCard3'       => $readonlyCard3,
            'isEmptyMonitoring'   => empty($monitoringikuDetail->mtid_capaian),
        ]);
    }


    public function updateDetail(Request $request, $mti_id, $ti_id)
    {
        $user = Auth::user();
        if (Auth::user()->role === 'prodi') {
            return redirect()->back()->with('error', 'Akun Prodi hanya memiliki akses Lihat (Read-Only).');
        }
        $monitoringiku = MonitoringIKU::findOrFail($mti_id);

        if ($monitoringiku->status == 1) {
            return redirect()->back()->with('error', 'Monitoring sudah difinalisasi, tidak dapat diedit.');
        }

        $targetIndikator = target_indikator::with('indikatorKinerja')->findOrFail($ti_id);
        $isAdmin = ($user->role === 'admin' || $user->role === 'fakultas');

        $rules = [];
        if ($isAdmin) {
            $rules = [
                'mtid_evaluasi'     => 'required|string',
                'mtid_tindaklanjut' => 'required|string',
                'mtid_peningkatan'  => 'required|string', 
            ];
        } else {
            $rules = [
                'mtid_capaian'    => 'required|string', 
                'mtid_keterangan' => 'required|string',
                'mtid_url'        => 'nullable|url', 
                
               
                'capaian_value'   => [
                    'nullable', 
                    function ($attribute, $value, $fail) use ($request) {
                        $type = $request->mtid_capaian;
                        
                        
                        if (in_array($type, ['persentase', 'nilai', 'rasio'])) {
                            if (is_null($value) || trim($value) === '') {
                                $fail('Nilai capaian wajib diisi.');
                            }
                            
                            
                            if (in_array($type, ['persentase', 'nilai']) && !is_numeric($value)) {
                                $fail('Nilai capaian harus berupa angka.');
                            }
                            
                            if ($type === 'rasio' && !preg_match('/^\d+\s*:\s*\d+$/', $value)) {
                                $fail('Format rasio harus angka:angka (contoh: 1:20).');
                            }
                        }
                    }
                ],
            ];
        }

        $validated = $request->validate($rules);

        try {
          
            $monitoringikuDetail = MonitoringIKU_Detail::firstOrNew([
                'mti_id' => $mti_id,
                'ti_id'  => $ti_id
            ]);

            if (!$monitoringikuDetail->mtid_id) {
                $monitoringikuDetail->mtid_id = 'MTID'.Str::uuid();
                $monitoringikuDetail->mtid_target = $targetIndikator->ti_target;
            }

            
            if ($isAdmin) {
                $monitoringikuDetail->mtid_evaluasi     = $validated['mtid_evaluasi'];
                $monitoringikuDetail->mtid_tindaklanjut = $validated['mtid_tindaklanjut'];
                $monitoringikuDetail->mtid_peningkatan  = $validated['mtid_peningkatan'];
            } else {
                $jenisCapaian = $validated['mtid_capaian'];
                $nilaiInput   = $validated['capaian_value'] ?? '';

                $capaianFinal = '';

                if ($jenisCapaian === 'ada' || $jenisCapaian === 'draft') {
                    $capaianFinal = $jenisCapaian;
                } 
                elseif ($jenisCapaian === 'persentase') {
                    $capaianFinal = is_numeric($nilaiInput) ? $nilaiInput.'%' : '0%';
                } 
                elseif ($jenisCapaian === 'rasio') {
                    $cleaned = preg_replace('/\s+/', '', $nilaiInput);
                    [$left, $right] = explode(':', $cleaned);
                    $capaianFinal = $left.' : '.$right;
                } 
                else {
                    $capaianFinal = $nilaiInput;
                }

                $monitoringikuDetail->mtid_capaian    = $capaianFinal;
                $monitoringikuDetail->mtid_keterangan = $validated['mtid_keterangan'];
                $monitoringikuDetail->mtid_url        = $validated['mtid_url'];
            
                $monitoringikuDetail->mtid_status = $this->hitungStatus(
                    $capaianFinal,
                    $targetIndikator->ti_target,
                    $targetIndikator->indikatorKinerja->ik_ketercapaian
                );
            }

            $monitoringikuDetail->save();

            HistoryMonitoringIKU::create([
                'hmi_id'           => 'HMI'.Str::uuid(),
                'mtid_id'          => $monitoringikuDetail->mtid_id,
                'ti_id'            => $ti_id,
                'hmi_target'       => $targetIndikator->ti_target,
                'hmi_capaian'      => $monitoringikuDetail->mtid_capaian,
                'hmi_keterangan'   => $monitoringikuDetail->mtid_keterangan,
                'hmi_status'       => $monitoringikuDetail->mtid_status,
                'hmi_url'          => $monitoringikuDetail->mtid_url,
                'hmi_evaluasi'     => $monitoringikuDetail->mtid_evaluasi,
                'hmi_tindaklanjut' => $monitoringikuDetail->mtid_tindaklanjut,
                'hmi_peningkatan'  => $monitoringikuDetail->mtid_peningkatan,
            ]);

            Alert::success('Sukses', 'Data berhasil diperbarui');
            return redirect()->route('monitoringiku.index-detail', $mti_id);

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan: '.$e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function parseNumber($value)
    {
        if (is_null($value) || $value === '') return 0;

        $string = (string) $value;

        $clean = preg_replace('/[^0-9.,-]/', '', $string);

        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean); 
            $clean = str_replace(',', '.', $clean);
        } 
        elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }

    private function hitungStatus($capaian, $target, $jenis)
    {
        $jenis = strtolower(trim($jenis));
        
        if (is_null($capaian) || trim($capaian) === '') {
            return 'Tidak Terlaksana';
        }

        if (in_array($jenis, ['nilai', 'persentase'])) {
            $valCapaian = $this->parseNumber($capaian);
            $valTarget  = $this->parseNumber($target);

            $epsilon = 0.00001;

            if (abs($valCapaian - $valTarget) < $epsilon) {
                return 'Tercapai';
            } elseif ($valCapaian > $valTarget) {
                return 'Terlampaui';
            } else {
                return 'Tidak Tercapai';
            }
        }

        if ($jenis === 'rasio') {
            $capaianStr = preg_replace('/\s+/', '', $capaian);
            $targetStr  = preg_replace('/\s+/', '', $target);

            $partsCapaian = explode(':', $capaianStr);
            $partsTarget  = explode(':', $targetStr);

            if (count($partsCapaian) == 2 && count($partsTarget) == 2) {
                
                $rightCapaian = $this->parseNumber($partsCapaian[1]);
                $rightTarget  = $this->parseNumber($partsTarget[1]);
                
                $leftCapaian = $this->parseNumber($partsCapaian[0]);
                $leftTarget  = $this->parseNumber($partsTarget[0]);

                if ($rightCapaian == 0 || $rightTarget == 0) return 'Tidak Tercapai';

                $ratioCapaian = $leftCapaian / $rightCapaian;
                $ratioTarget  = $leftTarget / $rightTarget;
                
                if ($rightCapaian > $rightTarget) {
                    return 'Terlampaui';
                } elseif ($rightCapaian == $rightTarget) {
                    return 'Tercapai';
                } else {
                    return 'Tidak Tercapai';
                }
            }
            return 'Tidak Tercapai';
        }

        if ($jenis === 'ketersediaan') {
            $capaianLower = strtolower(trim($capaian));
            $targetLower  = strtolower(trim($target));

            if ($capaianLower === 'ada' || $capaianLower === $targetLower) {
                return 'Tercapai';
            } elseif ($capaianLower === 'draft') {
                return 'Tidak Tercapai';
            } else {
                return 'Tidak Tercapai';
            }
        }

        return 'Tidak Tercapai';
    }


    
    public function final($id)
    {
        $monitoringiku = MonitoringIKU::findOrFail($id);

        if ($monitoringiku->status == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Monitoring IKU ini sudah final dan tidak dapat diubah.',
            ]);
        }

        if (! $monitoringiku->isCompleteForCurrentUnit()) {
            return response()->json([
                'success' => false,
                'message' => 'Unit kerja Anda belum melengkapi seluruh indikator yang menjadi tanggung jawabnya.',
            ]);
        }

        $monitoringiku->status = 1;
        $monitoringiku->save();

        return response()->json([
            'success' => true,
            'message' => 'Monitoring IKU berhasil difinalisasi untuk unit kerja Anda.',
        ]);
    }

    public function show(Request $request, $mti_id)
    {
        $user = Auth::user();

        if ($user->role == 'prodi' && $Monitoringiku->prodi_id != $user->prodi_id) {
            abort(403);
        }

        $Monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        $q = trim($request->input('q', ''));
        $unitKerjaFilter = $request->input('unit_kerja'); 

        

        $targetIndikatorsQuery = target_indikator::where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($prodi_id, $th_id) {
                    $sub->where('prodi_id', $prodi_id)
                        ->where('th_id', $th_id);
                },
                'monitoringDetail', 
                // 'historyMonitoring', // Bisa di-comment jika tidak ditampilkan di tabel ini untuk optimasi
            ]);

        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        $unitKerjas = [];

        if ($user->role == 'admin' || $user->role == 'fakultas' || $user->role == 'prodi') {
            $unitKerjas = \App\Models\UnitKerja::all(); 

            if ($unitKerjaFilter) {
                $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                    $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
                });
            }
        } else {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); 
        }

        $targetIndikators = $targetIndikatorsQuery->get();

        return view('pages.index-show-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'type_menu'         => 'monitoringiku',
            'user'              => $user,
            'q'                 => $q,
            'unitKerjas'        => $unitKerjas,     
            'unitKerjaFilter'   => $unitKerjaFilter,
        ]);
    }

    public function finalizeUnit($mti_id)
    {
        $user = Auth::user();
        if (Auth::user()->role === 'prodi') {
            return redirect()->back()->with('error', 'Akun Prodi hanya memiliki akses Lihat (Read-Only).');
        }
        $unit_id = $user->unit_id;

        // Validasi lagi di server side untuk keamanan
        $monitoringIku = MonitoringIKU::findOrFail($mti_id);
        
        if (!$monitoringIku->isCompleteForCurrentUnit()) {
            return response()->json([
                'success' => false, 
                'message' => 'Data belum lengkap. Harap isi semua capaian indikator terlebih dahulu.'
            ]);
        }

        \App\Models\MonitoringFinalUnit::updateOrCreate(
            [
                'monitoring_iku_id' => $mti_id,
                'unit_id' => $unit_id,
            ],
            [
                'status' => true,
                'finalized_by' => $user->id_user, // Sesuaikan dengan PK tabel users Anda (id atau id_user)
                'finalized_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Unit berhasil difinalisasi.']);
    }

    public function batalFinalUnitDashboard($unit_id)
    {
        // 1. Cek Tahun Aktif
        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();

        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tahun aktif.']);
        }

        // 2. Ambil semua ID Monitoring (MTI) yang ada di tahun aktif
        // Karena Unit Kerja memfinalisasi per Monitoring, kita harus membuka kunci
        // untuk semua monitoring di tahun ini yang terkait unit tersebut.
        $mti_ids = MonitoringIKU::where('th_id', $tahunAktif->th_id)->pluck('mti_id');

        // 3. Hapus data finalisasi unit tersebut di tahun aktif ini
        $deleted = MonitoringFinalUnit::whereIn('monitoring_iku_id', $mti_ids)
            ->where('unit_id', $unit_id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Validasi unit berhasil dibatalkan. Data bisa diedit kembali.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Unit belum melakukan finalisasi atau data tidak ditemukan.']);
        }
    }

    public function batalFinalSpesifik(Request $request)
    {
        $request->validate([
            'unit_id' => 'required',
            'mti_id'  => 'required'
        ]);

        $deleted = MonitoringFinalUnit::where('monitoring_iku_id', $request->mti_id)
            ->where('unit_id', $request->unit_id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Validasi untuk Prodi tersebut berhasil dibatalkan.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau sudah dibatalkan.']);
        }
    }

    public function history($mti_id, $ti_id)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'fakultas') {
            abort(403, 'Anda tidak memiliki akses untuk melihat riwayat ini.');
        }

        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        
        $targetIndikator = target_indikator::with(['indikatorKinerja'])
            ->where('ti_id', $ti_id)
            ->firstOrFail();

        $detail = MonitoringIKU_Detail::where('mti_id', $mti_id)
            ->where('ti_id', $ti_id)
            ->first();

        $histories = collect([]);
        if ($detail) {
            $histories = HistoryMonitoringIKU::where('mtid_id', $detail->mtid_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pages.log-history-monitoringiku', [
            'title'           => 'Riwayat Perubahan Data',
            'monitoringiku'   => $monitoringiku,
            'targetIndikator' => $targetIndikator,
            'detail'          => $detail,
            'histories'       => $histories,
            'type_menu'       => 'masterdata',
            'sub_menu'        => 'history',
        ]);
    }

    public function indexHistory(Request $request)
    {
        // Hanya Admin/Fakultas
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'fakultas') {
            abort(403);
        }

        $title = 'Log Aktivitas Monitoring';
        $q = $request->query('q');

        // Ambil data prodi & tahun (Logic sama seperti index biasa)
        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        $allowedProdis = $prodis->pluck('prodi_id')->toArray();
        
        // Filter khusus Fakultas jika login sebagai fakultas
        if (Auth::user()->role == 'fakultas') {
            $allowedProdis = program_studi::where('id_fakultas', Auth::user()->id_fakultas)
                ->pluck('prodi_id')->toArray();
        }

        $monitoringikus = MonitoringIKU::with(['targetIndikator.prodi', 'tahunKerja'])
            ->whereHas('targetIndikator.prodi', function ($query) use ($q, $allowedProdis) {
                if ($q) $query->where('nama_prodi', 'like', '%' . $q . '%');
                $query->whereIn('prodi_id', $allowedProdis);
            })
            ->paginate(10);

        return view('pages.index-history-monitoring', [
            'title' => $title,
            'monitoringikus' => $monitoringikus,
            'q' => $q,
            'type_menu' => 'masterdata',
            'sub_menu'  => 'history',
        ]);
    }

    public function listIndicatorsForHistory(Request $request, $mti_id)
    {
        // Cek Role (Sesuai logic Anda sebelumnya)
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'fakultas') {
            abort(403);
        }

        $Monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        
        // 1. Ambil Inputan Filter
        $q = $request->input('q');
        $unitKerjaFilter = $request->input('unit_kerja'); // <--- Tambahan Filter Unit

        // 2. Query Indikator
        $targetIndikatorsQuery = target_indikator::where('prodi_id', $Monitoringiku->prodi_id)
            ->where('th_id', $Monitoringiku->th_id)
            ->with(['indikatorKinerja.unitKerja']); // Eager load unit kerja

        // 3. Filter Pencarian Nama/Kode
        if ($q) {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // 4. Filter Unit Kerja (TAMBAHAN)
        if ($unitKerjaFilter) {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
            });
        }

        $targetIndikators = $targetIndikatorsQuery->get();

        // 5. Ambil Daftar Unit Kerja untuk Dropdown
        $unitKerjas = UnitKerja::orderBy('unit_nama', 'asc')->get();

        return view('pages.index-list-indicators', [
            'Monitoringiku'    => $Monitoringiku,
            'targetIndikators' => $targetIndikators,
            'q'                => $q,
            'unitKerjaFilter'  => $unitKerjaFilter, 
            'unitKerjas'       => $unitKerjas,      
            'type_menu'        => 'masterdata',
            'sub_menu'         => 'history',
        ]);
    }


    public function exportDetail(Request $request, $mti_id, $type)
    {
        $allowed = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];

        if (!in_array($type, $allowed)) {
            abort(404, 'Jenis export tidak valid');
        }

        $unit_kerja_id = $request->query('unit_kerja');

        $monitoring = MonitoringIKU::with(['prodi.Fakultasn', 'tahunKerja'])
            ->findOrFail($mti_id);

        $query = target_indikator::with([
                'indikatorKinerja.unitKerja', 
                'monitoringDetail'
            ])
            ->select('target_indikator.*')
            ->selectRaw("(
                SELECT baseline 
                FROM ik_baseline_tahun 
                WHERE ik_baseline_tahun.ik_id = target_indikator.ik_id 
                AND ik_baseline_tahun.th_id = target_indikator.th_id 
                AND ik_baseline_tahun.prodi_id = target_indikator.prodi_id 
                LIMIT 1
            ) as fetched_baseline") 
            ->where('prodi_id', $monitoring->prodi_id)
            ->where('th_id', $monitoring->th_id);

        if ($unit_kerja_id) {
            $query->whereHas('indikatorKinerja.unitKerja', function($q) use ($unit_kerja_id) {
                $q->where('indikatorkinerja_unitkerja.unit_id', $unit_kerja_id);
            });
        }

        $data = $query->orderBy('ti_id', 'asc')->get();

        $rawProdi = $monitoring->prodi->nama_prodi ?? 'Prodi_Umum';
        $rawTahun = $monitoring->tahunKerja->th_tahun ?? date('Y');

        $cleanType  = ucfirst($type);
        $cleanProdi = Str::slug($rawProdi, '_'); 
        $cleanTahun = Str::slug($rawTahun, '-'); 
        
        $unitSuffix = '';
        if ($unit_kerja_id) {
            $unit = UnitKerja::find($unit_kerja_id);
            if ($unit) {
                $cleanUnit = Str::slug($unit->unit_nama, '_');
                $unitSuffix = "_Unit_{$cleanUnit}";
            }
        }

        $fileName = "Monitoring_{$cleanType}_{$cleanProdi}_{$cleanTahun}{$unitSuffix}.xlsx";

        return Excel::download(new MonitoringIKUDetailExport($data, $type, $monitoring), $fileName);
    }

    public function exportPdfDetail(Request $request, $mti_id)
    {
        $type = $request->query('type', 'peningkatan');
        $unit_kerja_id = $request->query('unit_kerja');
        $q = $request->query('q'); 

        $monitoring = MonitoringIKU::with([
            'prodi.Fakultasn', 
            'tahunKerja'
        ])->findOrFail($mti_id);

        $query = target_indikator::with([
            'indikatorKinerja.unitKerja', 
            'monitoringDetail'
        ])
        ->select('target_indikator.*') 
        ->addSelect(['fetched_baseline' => DB::table('ik_baseline_tahun')
            ->select('baseline')
            ->whereColumn('ik_id', 'target_indikator.ik_id')
            ->whereColumn('th_id', 'target_indikator.th_id')
            ->whereColumn('prodi_id', 'target_indikator.prodi_id')
            ->limit(1)
        ])
        ->where('prodi_id', $monitoring->prodi_id)
        ->where('th_id', $monitoring->th_id);

    if (!empty($unit_kerja_id)) {
        $query->whereHas('indikatorKinerja.unitKerja', function($query) use ($unit_kerja_id) {
            $query->where('indikatorkinerja_unitkerja.unit_id', $unit_kerja_id);
        });
    }

    if (!empty($q)) {
        $query->whereHas('indikatorKinerja', function($query) use ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%')
                ->orWhere('ik_kode', 'like', '%' . $q . '%');
        });
    }

        $targetIndikators = $query->orderBy('ti_id', 'asc')->get();

        $judulLaporan = match ($type) {
            'penetapan'    => 'Laporan Penetapan Indikator Kinerja',
            'pelaksanaan'  => 'Laporan Pelaksanaan & Capaian Kinerja',
            'evaluasi'     => 'Laporan Evaluasi Kinerja',
            'pengendalian' => 'Laporan Pengendalian Kinerja',
            'peningkatan'  => 'Laporan Peningkatan Kinerja',
            default        => 'Laporan Monitoring Indikator Kinerja',
        };

        $pdf = Pdf::loadView('export.MonitoringDetail-pdf', [
            'data'       => $targetIndikators,
            'monitoring' => $monitoring,
            'type'       => $type,
            'judul'      => $judulLaporan
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Monitoring_' . ucfirst($type) . '.pdf');
    }

}

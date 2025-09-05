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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringIKUDetailExport;

class MonitoringIKUController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'unit kerja') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();

        $title = 'Data Monitoring IKU';
        $q = $request->query('q');

        $allowedProdis = [];

        // Tentukan prodi sesuai role/unit kerja
        if ($user->role == 'fakultas') {
            $prodis = program_studi::where('id_fakultas', $user->id_fakultas)
                ->orderBy('nama_prodi', 'asc')
                ->get();
            $allowedProdis = $prodis->pluck('prodi_id')->toArray();

        } elseif ($user->role == 'unit kerja' && $user->unitKerja && $user->unitKerja->unit_nama == 'Dekanat Fti') {
            $prodis = program_studi::whereIn('nama_prodi', [
                'Informatika',
                'Rekayasa Sistem Komputer'
            ])->get();
            $allowedProdis = $prodis->pluck('prodi_id')->toArray();

        } elseif ($user->role == 'unit kerja' && $user->unitKerja && $user->unitKerja->unit_nama == 'Dekanat Fbdk') {
            $prodis = program_studi::whereIn('nama_prodi', [
                'Bisnis Digital',
                'Desain Komunikasi Visual'
            ])->get();
            $allowedProdis = $prodis->pluck('prodi_id')->toArray();

        } else {
            $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
            $allowedProdis = $prodis->pluck('prodi_id')->toArray();
        }

        // Query MonitoringIKU difilter berdasarkan allowedProdis
        $monitoringikus = MonitoringIKU::with(['targetIndikator.prodi', 'tahunKerja'])
            ->whereHas('targetIndikator.prodi', function ($query) use ($q, $allowedProdis) {
                if ($q) {
                    $query->where('nama_prodi', 'like', '%' . $q . '%');
                }
                if (!empty($allowedProdis)) {
                    $query->whereIn('prodi_id', $allowedProdis);
                }
            })
            ->paginate(10);

        $no = $monitoringikus->firstItem();

        $tahuns = tahun_kerja::where('th_is_aktif', 'y')
            ->orderBy('th_tahun', 'asc')
            ->get();

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

    public function indexDetail($mti_id, Request $request)
    {
        $Monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;
        $user = Auth::user();

        // ambil keyword pencarian
        $q = trim($request->input('q', ''));

        $targetIndikatorsQuery = target_indikator::where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($prodi_id, $th_id) {
                    $sub->where('prodi_id', $prodi_id)
                        ->where('th_id', $th_id);
                },
                'monitoringDetail',
                'historyMonitoring',
            ]);

        // 🔎 filter pencarian
        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // 🔒 filter jika bukan admin
        if ($user->role !== 'admin') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); // hanya tampilkan yg sudah ada target
        }

        $targetIndikators = $targetIndikatorsQuery->get();

        return view('pages.index-detail-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'type_menu'         => 'monitoringiku',
            'user'              => $user,
            'q'                 => $q, // 🔑 kirim ke view biar tidak undefined
        ]);
}

    public function createDetail($mti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];
        $user = Auth::user();

        // Ambil target indikator sesuai role
        $targetIndikatorQuery = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($q) use ($monitoringiku) {
                    $q->where('prodi_id', $monitoringiku->prodi_id)
                    ->where('th_id', $monitoringiku->th_id);
                }
            ]);

        // Filter kalau bukan admin → hanya unit-nya sendiri dan yang sudah ada target
        if ($user->role !== 'admin') {
            $targetIndikatorQuery
                ->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                })
                ->whereNotNull('ti_target');
        }

        $targetIndikator = $targetIndikatorQuery->get();

        if ($targetIndikator->isEmpty()) {
            return redirect()
                ->route('monitoringiku.index')
                ->with('error', 'Tidak ada indikator yang bisa diukur.');
        }

        // Ambil detail monitoring yang sudah ada
        $monitoringikuDetail = MonitoringIKU_Detail::whereIn('ti_id', $targetIndikator->pluck('ti_id'))->get();

        return view('pages.create-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'monitoringikuDetail' => $monitoringikuDetail,
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin', // 🔑 dipakai di Blade
        ]);
    }

    public function storeDetail(Request $request, $mti_id) 
    {
        $validated = $request->validate([
            'ti_id' => 'required|array',
            'ti_id.*' => 'required|string',

            // field user
            'mtid_capaian' => 'nullable|array',
            'mtid_capaian.*' => 'nullable|string',
            'mtid_keterangan' => 'nullable|array',
            'mtid_keterangan.*' => 'nullable|string',
            'mtid_url' => 'nullable|array',
            'mtid_url.*' => 'nullable|url',

            // field admin
            'mtid_evaluasi' => 'nullable|array',
            'mtid_evaluasi.*' => 'nullable|string',
            'mtid_tindaklanjut' => 'nullable|array',
            'mtid_tindaklanjut.*' => 'nullable|string',
            'mtid_peningkatan' => 'nullable|array',
            'mtid_peningkatan.*' => 'nullable|string',
        ]);

        try {
            $monitoringiku = MonitoringIKU::findOrFail($mti_id);
            $user = Auth::user();

            foreach ($request->ti_id as $index => $ti_id) {
                $targetIndikator = target_indikator::with('indikatorKinerja')
                    ->where('ti_id', $ti_id)
                    ->where('prodi_id', $monitoringiku->prodi_id)
                    ->where('th_id', $monitoringiku->th_id)
                    ->when($user->role !== 'admin', function ($q) use ($user) {
                        $q->whereHas('indikatorKinerja', function ($sub) use ($user) {
                            $sub->where('unit_id', $user->unit_id);
                        })->whereNotNull('ti_target');
                    })
                    ->first();

                if (!$targetIndikator) {
                    return redirect()
                        ->route('monitoringiku.index-detail', $mti_id)
                        ->with('error', 'Indikator tidak ditemukan atau Anda tidak berhak mengisinya.');
                }

                if ($monitoringiku->status == 1) {
                    return redirect()
                        ->route('monitoringiku.index')
                        ->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
                }

                $jenis = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'nilai');

                // 🔒 Batasi input sesuai role
                if ($user->role === 'admin') {
                    $mtid_capaian     = null;
                    $mtid_keterangan  = null;
                    $mtid_url         = null;

                    $mtid_evaluasi     = $request->mtid_evaluasi[$index] ?? null;
                    $mtid_tindaklanjut = $request->mtid_tindaklanjut[$index] ?? null;
                    $mtid_peningkatan  = $request->mtid_peningkatan[$index] ?? null;
                } else {
                    $mtid_capaian     = $request->mtid_capaian[$index] ?? null;
                    $mtid_keterangan  = $request->mtid_keterangan[$index] ?? null;
                    $mtid_url         = $request->mtid_url[$index] ?? null;

                    $mtid_evaluasi     = null;
                    $mtid_tindaklanjut = null;
                    $mtid_peningkatan  = null;
                }

                // Hitung status otomatis (hanya relevan kalau ada capaian)
                $mtid_status = $this->hitungStatus($mtid_capaian, $targetIndikator->ti_target, $jenis);

                // Cari detail lama (jika ada)
                $existingDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
                    ->where('ti_id', $ti_id)
                    ->first();

                $mtid_id = $existingDetail?->mtid_id ?? 'MTID' . strtoupper(uniqid());

                // 🔒 Batasi input sesuai role
                if ($user->role === 'admin') {
                    // Ambil nilai lama biar tidak di-null-kan
                    $mtid_capaian    = $existingDetail?->mtid_capaian;
                    $mtid_keterangan = $existingDetail?->mtid_keterangan;
                    $mtid_url        = $existingDetail?->mtid_url;
                    $mtid_status     = $existingDetail?->mtid_status;

                    // Admin hanya boleh isi 3 ini
                    $mtid_evaluasi     = $request->mtid_evaluasi[$index] ?? null;
                    $mtid_tindaklanjut = $request->mtid_tindaklanjut[$index] ?? null;
                    $mtid_peningkatan  = $request->mtid_peningkatan[$index] ?? null;
                } else {
                    // User update capaian dkk
                    $mtid_capaian    = $request->mtid_capaian[$index] ?? null;
                    $mtid_keterangan = $request->mtid_keterangan[$index] ?? null;
                    $mtid_url        = $request->mtid_url[$index] ?? null;
                    $mtid_status     = $this->hitungStatus($mtid_capaian, $targetIndikator->ti_target, $jenis);

                    // Field admin biarkan tetap
                    $mtid_evaluasi     = $existingDetail?->mtid_evaluasi;
                    $mtid_tindaklanjut = $existingDetail?->mtid_tindaklanjut;
                    $mtid_peningkatan  = $existingDetail?->mtid_peningkatan;
                }

                $detail = MonitoringIKU_Detail::updateOrCreate(
                    [
                        'mti_id' => $mti_id,
                        'ti_id'  => $ti_id
                    ],
                    [
                        'mtid_id'           => $mtid_id,
                        'mtid_target'       => $targetIndikator->ti_target,
                        'mtid_capaian'      => $mtid_capaian,
                        'mtid_keterangan'   => $mtid_keterangan,
                        'mtid_status'       => $mtid_status,
                        'mtid_url'          => $mtid_url,
                        'mtid_evaluasi'     => $mtid_evaluasi,
                        'mtid_tindaklanjut' => $mtid_tindaklanjut,
                        'mtid_peningkatan'  => $mtid_peningkatan,
                    ]
                );

                // Simpan history jika ada perubahan
                if (
                    !$existingDetail ||
                    $existingDetail->mtid_capaian    != $mtid_capaian ||
                    $existingDetail->mtid_keterangan != $mtid_keterangan ||
                    $existingDetail->mtid_status     != $mtid_status ||
                    $existingDetail->mtid_url        != $mtid_url ||
                    $existingDetail->mtid_evaluasi   != $mtid_evaluasi ||
                    $existingDetail->mtid_tindaklanjut != $mtid_tindaklanjut ||
                    $existingDetail->mtid_peningkatan  != $mtid_peningkatan
                ) {
                    HistoryMonitoringIKU::create([
                        'hmi_id'          => 'HMI' . strtoupper(uniqid()),
                        'mtid_id'         => $detail->mtid_id,
                        'ti_id'           => $ti_id,
                        'hmi_target'      => $targetIndikator->ti_target,
                        'hmi_capaian'     => $mtid_capaian,
                        'hmi_keterangan'  => $mtid_keterangan,
                        'hmi_status'      => $mtid_status,
                        'hmi_url'         => $mtid_url,
                        'hmi_evaluasi'    => $mtid_evaluasi,
                        'hmi_tindaklanjut'=> $mtid_tindaklanjut,
                        'hmi_peningkatan' => $mtid_peningkatan,
                    ]);
                }
            }

            Alert::success('Sukses', 'Data Berhasil Disimpan');
            return redirect()->route('monitoringiku.index-detail', $mti_id);

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan: ' . $e->getMessage());
            return redirect()->route('monitoringiku.index-detail', $mti_id);
        }
    }


    public function editDetail($mti_id, $ti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];
        $user = Auth::user();

        // Query target indikator
        $targetIndikatorQuery = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->where('ti_id', $ti_id)
            ->with(['indikatorKinerja.unitKerja']);

        // Filter kalau bukan admin → hanya bisa edit indikator milik unitnya sendiri
        if ($user->role !== 'admin') {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            })->whereNotNull('ti_target');
        }

        $targetIndikator = $targetIndikatorQuery->first();

        // Jika target indikator tidak ditemukan → mungkin bukan jatahnya
        if (!$targetIndikator) {
            return redirect()->route('monitoringiku.index-detail', $mti_id)
                ->with('error', 'Data target indikator tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Ambil detail monitoring
        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
            ->where('ti_id', $ti_id)
            ->first();

        // Program kerja terkait indikator
        $programKerja = RencanaKerja::with(['periodes', 'tahunKerja', 'monitoring', 'realisasi'])
            ->join('rencana_kerja_target_indikator', 'rencana_kerja.rk_id', '=', 'rencana_kerja_target_indikator.rk_id')
            ->join('target_indikator', 'rencana_kerja_target_indikator.ti_id', '=', 'target_indikator.ti_id')
            ->where('target_indikator.ik_id', $targetIndikator->ik_id)
            ->select('rencana_kerja.*')
            ->orderBy('rk_nama', 'asc')
            ->get();

        return view('pages.edit-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'programKerja'        => $programKerja,
            'monitoringikuDetail' => $monitoringikuDetail ?? new MonitoringIKU_Detail(),
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin', // ✅ tambahan untuk blade
        ]);
    }



    public function updateDetail(Request $request, $mti_id, $ti_id)
    {
        $user = Auth::user();

        // Validasi input dinamis berdasarkan role
        $rules = [];
        if ($user->role === 'admin') {
            $rules = [
                'mtid_evaluasi'     => 'nullable|string',
                'mtid_tindaklanjut' => 'nullable|string',
                'mtid_peningkatan'  => 'nullable|string',
            ];
        } else {
            $rules = [
                'mtid_capaian'   => 'required|string',
                'capaian_value'  => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($request) {
                        if (in_array($request->mtid_capaian, ['persentase', 'nilai']) && !is_numeric($value)) {
                            $fail('Nilai harus berupa angka.');
                        }

                        if ($request->mtid_capaian === 'rasio') {
                            $cleaned = preg_replace('/\s*/', '', $value);
                            if (!preg_match('/^\d+:\d+$/', $cleaned)) {
                                $fail('Format rasio harus dalam bentuk angka:angka, misalnya 5:2.');
                            } else {
                                [$left, $right] = explode(':', $cleaned);
                                if ((int)$left === 0 && (int)$right === 0) {
                                    $fail('Rasio tidak boleh 0:0.');
                                }
                            }
                        }
                    }
                ],
                'mtid_keterangan' => 'nullable|string',
                'mtid_status'     => 'required|in:tercapai,tidak tercapai,tidak terlaksana',
                'mtid_url'        => 'required|url',
            ];
        }

        $validated = $request->validate($rules);

        try {
            $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
                ->where('ti_id', $ti_id)
                ->first();

            // Data default untuk update
            $updateData = [];

            if ($user->role === 'admin') {
                // Admin hanya boleh update 3 field ini
                $updateData['mtid_evaluasi']     = $validated['mtid_evaluasi'] ?? $monitoringikuDetail->mtid_evaluasi ?? null;
                $updateData['mtid_tindaklanjut'] = $validated['mtid_tindaklanjut'] ?? $monitoringikuDetail->mtid_tindaklanjut ?? null;
                $updateData['mtid_peningkatan']  = $validated['mtid_peningkatan'] ?? $monitoringikuDetail->mtid_peningkatan ?? null;
            } else {
                // User biasa update semua
                $jenisCapaian = $validated['mtid_capaian'];
                $inputCapaian = trim($validated['capaian_value'] ?? '');

                if ($jenisCapaian === 'persentase') {
                    $capaianFinal = is_numeric($inputCapaian) ? $inputCapaian.'%' : '0%';
                } elseif ($jenisCapaian === 'nilai') {
                    $capaianFinal = is_numeric($inputCapaian) ? (float)$inputCapaian : 0;
                } elseif ($jenisCapaian === 'rasio') {
                    $cleaned = preg_replace('/\s*/', '', $inputCapaian);
                    [$left, $right] = explode(':', $cleaned);
                    $capaianFinal = $left.' : '.$right;
                } else {
                    $capaianFinal = $inputCapaian;
                }

                $updateData = [
                    'mtid_capaian'    => $capaianFinal,
                    'mtid_keterangan' => $validated['mtid_keterangan'] ?? null,
                    'mtid_status'     => $validated['mtid_status'],
                    'mtid_url'        => $validated['mtid_url'],
                ];
            }

            // Update atau buat baru
            if ($monitoringikuDetail) {
                $monitoringikuDetail->update($updateData);
            } else {
                $monitoringikuDetail = MonitoringIKU_Detail::create(array_merge($updateData, [
                    'mtid_id'    => 'MTID'.Str::uuid(),
                    'mti_id'     => $mti_id,
                    'ti_id'      => $ti_id,
                    'mtid_target'=> 'ada',
                ]));
            }

            // Simpan ke history
            $targetIndikator = target_indikator::findOrFail($ti_id);
            HistoryMonitoringIKU::create([
                'hmi_id'        => 'HMI'.Str::uuid(),
                'mtid_id'       => $monitoringikuDetail->mtid_id,
                'ti_id'         => $targetIndikator->ti_id,
                'hmi_target'    => $targetIndikator->ti_target,
                'hmi_capaian'   => $updateData['mtid_capaian'] ?? $monitoringikuDetail->mtid_capaian,
                'hmi_keterangan'=> $updateData['mtid_keterangan'] ?? $monitoringikuDetail->mtid_keterangan,
                'hmi_status'    => $updateData['mtid_status'] ?? $monitoringikuDetail->mtid_status,
                'hmi_url'       => $updateData['mtid_url'] ?? $monitoringikuDetail->mtid_url,
                'hmi_evaluasi'  => $updateData['mtid_evaluasi'] ?? $monitoringikuDetail->mtid_evaluasi,
                'hmi_tindaklanjut'=> $updateData['mtid_tindaklanjut'] ?? $monitoringikuDetail->mtid_tindaklanjut,
                'hmi_peningkatan'=> $updateData['mtid_peningkatan'] ?? $monitoringikuDetail->mtid_peningkatan,
            ]);


            Alert::success('Sukses', 'Data berhasil diperbarui');
            return redirect()->route('monitoringiku.index-detail', $mti_id);

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi Kesalahan: '.$e->getMessage());
            return redirect()->route('monitoringiku.index-detail', $mti_id);
        }
}

    private function hitungStatus($capaian, $target, $jenis)
    {
        $jenis = strtolower($jenis);

        if (is_null($capaian) || $capaian === '') {
            return 'Tidak Terlaksana';
        }

        // Untuk jenis numerik
        if (in_array($jenis, ['nilai', 'persentase'])) {
            $capaian = floatval($capaian);
            $target = floatval($target);

            if ($capaian > $target) {
                return 'Terlampaui';
            } elseif ($capaian == $target) {
                return 'Tercapai';
            } else {
                return 'Tidak Tercapai';
            }
        }

        // Untuk jenis rasio
        if ($jenis === 'rasio') {
            // Format yang valid adalah "a : b"
            if (preg_match('/^\s*(\d+)\s*:\s*(\d+)\s*$/', $capaian, $matchCapaian) &&
                preg_match('/^\s*(\d+)\s*:\s*(\d+)\s*$/', $target, $matchTarget)) {
        
                // Ambil nilai kanan dari rasio, yaitu pembilang b
                $capaianRight = (int) $matchCapaian[2];
                $targetRight = (int) $matchTarget[2];
        
                if ($capaianRight > $targetRight) {
                    return 'Terlampaui';
                } elseif ($capaianRight == $targetRight) {
                    return 'Tercapai';
                } else {
                    return 'Tidak Tercapai';
                }
            }

            return 'Tidak Tercapai'; // jika format tidak valid
        }

        // Untuk jenis ketersediaan (string)
        if ($jenis === 'ketersediaan') {
            $capaian = strtolower($capaian);
            if ($capaian === 'ada') {
                return 'Tercapai';
            } elseif ($capaian === 'draft') {
                return 'Tidak Tercapai';
            }
        }

        return 'Tidak Tercapai';
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

    public function exportDetail($mti_id, $type)
    {
        $allowed = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];

        if (!in_array($type, $allowed)) {
            abort(404, 'Jenis export tidak valid');
        }

        // Ambil data MonitoringIKU supaya tahu prodi
        $monitoring = MonitoringIKU::with('targetIndikator.prodi')->findOrFail($mti_id);

        // Nama prodi (misalnya "Desain Komunikasi Visual" jadi dkv)
        $prodiName = strtolower(str_replace(' ', '_', $monitoring->targetIndikator->prodi->nama_prodi ?? 'prodi'));

        $fileName = "MonitoringIKU_{$type}_prodi_{$prodiName}.xlsx";

        return Excel::download(new MonitoringIKUDetailExport($mti_id, $type), $fileName);
    }

}

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

class MonitoringIKUController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'unit kerja' && Auth::user()->role !== 'fakultas') {
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

        } elseif ($user->role == 'unit kerja' && $user->unitKerja && $user->unitKerja->unit_nama == 'Dekanat FTI') {
            $prodis = program_studi::whereIn('nama_prodi', [
                'Informatika',
                'Rekayasa Sistem Komputer'
            ])->get();
            $allowedProdis = $prodis->pluck('prodi_id')->toArray();

        } elseif ($user->role == 'unit kerja' && $user->unitKerja && $user->unitKerja->unit_nama == 'Dekanat FBDK') {
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

        // Ambil keyword pencarian
        $q = trim($request->input('q', ''));

        // Ambil filter unit kerja
        $unitKerjaFilter = $request->input('unit_kerja', '');

        // Query untuk target indikator
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

        // Filter pencarian
        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // Filter berdasarkan unit kerja
        if ($unitKerjaFilter) {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                $sub->where('unit_kerja.unit_id', $unitKerjaFilter); // pastikan nama kolom yang digunakan adalah 'unit_kerja.id'
            });
        }

        // Filter jika bukan admin
        if ($user->role !== 'admin' && $user->role !== 'fakultas') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id); // Menyaring berdasarkan unit_id pengguna
            })
            ->whereNotNull('ti_target'); // hanya tampilkan yg sudah ada target
        }

        // Eksekusi query
        $targetIndikators = $targetIndikatorsQuery->get();

        // Ambil data unit kerja untuk dropdown filter
        $unitKerjas = UnitKerja::all();  // Mengambil semua unit kerja

        return view('pages.index-detail-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'unitKerjas'        => $unitKerjas, // Kirim unit kerja ke view
            'q'                 => $q,
            'unitKerjaFilter'   => $unitKerjaFilter,
            'type_menu'         => 'monitoringiku',   // Kirim unit kerja filter ke view
        ]);
    }

    public function createDetail($mti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];
        $user = Auth::user();

        // Ambil semua target indikator berdasarkan prodi + tahun
        $targetIndikator = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($q) use ($monitoringiku) {
                    $q->where('prodi_id', $monitoringiku->prodi_id)
                    ->where('th_id', $monitoringiku->th_id);
                }
            ])
            ->get();

        if ($targetIndikator->isEmpty()) {
            return redirect()->route('monitoringiku.index')
                ->with('error', 'Tidak ada indikator yang bisa diukur.');
        }

        // Ambil data unit kerja (jika diperlukan di blade)
        $unitKerja = UnitKerja::all();

        // Ambil monitoring detail berdasarkan target indikator yang ditemukan
        $monitoringikuDetail = MonitoringIKU_Detail::whereIn('ti_id', $targetIndikator->pluck('ti_id'))->get();

        // Return langsung ke halaman create detail (tidak ada AJAX)
        return view('pages.create-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'monitoringikuDetail' => $monitoringikuDetail,
            'unitKerja'           => $unitKerja,
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin',
        ]);
    }

    public function storeDetail(Request $request, $mti_id)
    {
        $validated = $request->validate([
            'ti_id' => 'required|array',
            'ti_id.*' => 'required|string',

            // field user
            'mtid_capaian'   => 'nullable|array',
            'mtid_capaian.*' => 'nullable|string',
            'mtid_keterangan'   => 'nullable|array',
            'mtid_keterangan.*' => 'nullable|string',
            'mtid_url'   => 'nullable|array',
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

            if ($monitoringiku->status == 1) {
                return redirect()
                    ->route('monitoringiku.index')
                    ->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
            }

            foreach ($request->ti_id as $index => $ti_id) {

                // --- Ambil target indikator ---
                $targetIndikator = target_indikator::with('indikatorKinerja')
                    ->where('ti_id', $ti_id)
                    ->where('prodi_id', $monitoringiku->prodi_id)
                    ->where('th_id', $monitoringiku->th_id)
                    ->when($user->role !== 'admin', function ($q) use ($user) {
                        $q->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                            $sub->where('unit_kerja.unit_id', $user->unit_id);
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

                // Ambil detail lama bila ada
                $existing = MonitoringIKU_Detail::where('mti_id', $mti_id)
                    ->where('ti_id', $ti_id)
                    ->first();

                $mtid_id = $existing->mtid_id ?? 'MTID' . strtoupper(uniqid());

                // =============== ROLE USER ===============
                if ($user->role === 'user') {
                    $mtid_capaian    = $request->mtid_capaian[$index] ?? $existing->mtid_capaian ?? null;
                    $mtid_keterangan = $request->mtid_keterangan[$index] ?? $existing->mtid_keterangan ?? null;
                    $mtid_url        = $request->mtid_url[$index] ?? $existing->mtid_url ?? null;

                    $mtid_evaluasi     = $existing->mtid_evaluasi ?? null;
                    $mtid_tindaklanjut = $existing->mtid_tindaklanjut ?? null;
                    $mtid_peningkatan  = $existing->mtid_peningkatan ?? null;

                    // Status otomatis
                    $mtid_status = $this->hitungStatus($mtid_capaian, $targetIndikator->ti_target, $jenis);
                }

                // =============== ROLE ADMIN ===============
                else {
                    // Admin tidak boleh ubah capaian
                    $mtid_capaian    = $existing->mtid_capaian ?? null;
                    $mtid_keterangan = $existing->mtid_keterangan ?? null;
                    $mtid_url        = $existing->mtid_url ?? null;

                    // Admin hanya isi 3 field
                    $mtid_evaluasi     = $request->mtid_evaluasi[$index] ?? $existing->mtid_evaluasi ?? null;
                    $mtid_tindaklanjut = $request->mtid_tindaklanjut[$index] ?? $existing->mtid_tindaklanjut ?? null;
                    $mtid_peningkatan  = $request->mtid_peningkatan[$index] ?? $existing->mtid_peningkatan ?? null;

                    // Status tidak dihitung ulang oleh admin
                    $mtid_status = $existing->mtid_status ?? null;
                }

                // mtid_status tidak boleh null
                if ($mtid_status === null) {
                    $mtid_status = 'Draft';
                }

                // Simpan
                $detail = MonitoringIKU_Detail::updateOrCreate(
                    [
                        'mti_id' => $mti_id,
                        'ti_id'  => $ti_id
                    ],
                    [
                        'mtid_id'         => $mtid_id,
                        'mtid_target'     => $targetIndikator->ti_target,
                        'mtid_capaian'    => $mtid_capaian,
                        'mtid_keterangan' => $mtid_keterangan,
                        'mtid_status'     => $mtid_status,
                        'mtid_url'        => $mtid_url,
                        'mtid_evaluasi'   => $mtid_evaluasi,
                        'mtid_tindaklanjut' => $mtid_tindaklanjut,
                        'mtid_peningkatan'  => $mtid_peningkatan,
                    ]
                );

                // Simpan history jika ada perubahan
                if (
                    !$existing ||
                    $existing->mtid_capaian      != $mtid_capaian ||
                    $existing->mtid_keterangan   != $mtid_keterangan ||
                    $existing->mtid_status       != $mtid_status ||
                    $existing->mtid_url          != $mtid_url ||
                    $existing->mtid_evaluasi     != $mtid_evaluasi ||
                    $existing->mtid_tindaklanjut != $mtid_tindaklanjut ||
                    $existing->mtid_peningkatan  != $mtid_peningkatan
                ) {
                    HistoryMonitoringIKU::create([
                        'hmi_id'           => 'HMI' . strtoupper(uniqid()),
                        'mtid_id'          => $mtid_id,
                        'ti_id'            => $ti_id,
                        'hmi_target'       => $targetIndikator->ti_target,
                        'hmi_capaian'      => $mtid_capaian,
                        'hmi_keterangan'   => $mtid_keterangan,
                        'hmi_status'       => $mtid_status,
                        'hmi_url'          => $mtid_url,
                        'hmi_evaluasi'     => $mtid_evaluasi,
                        'hmi_tindaklanjut' => $mtid_tindaklanjut,
                        'hmi_peningkatan'  => $mtid_peningkatan,
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

        // Ambil target indikator seperti di createDetail, tapi hanya 1 (berdasarkan ti_id)
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

        // Jika bukan admin → filter hanya unit kerjanya sendiri
        if ($user->role !== 'admin') {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($q) use ($user) {
                $q->where('unit_kerja.unit_id', $user->unit_id);
            });
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

        // Ambil baseline seperti createDetail
        $baseline = optional($targetIndikator->baselineTahun)->baseline;

        // Ambil unit kerja seperti createDetail
        $unitKerja = UnitKerja::all();

        return view('pages.edit-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'monitoringikuDetail' => $monitoringikuDetail ?? new MonitoringIKU_Detail(),
            'baseline'            => $baseline,
            'unitKerja'           => $unitKerja,
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin',
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
            return response()->json([
                'success' => false,
                'message' => 'Monitoring IKU ini sudah final dan tidak dapat diubah.',
            ]);
        }

        // Cek apakah unit kerja user sudah mengisi semua data monitoring
        if (! $monitoringiku->isCompleteForCurrentUnit()) {
            return response()->json([
                'success' => false,
                'message' => 'Unit kerja Anda belum melengkapi seluruh indikator yang menjadi tanggung jawabnya.',
            ]);
        }

        // Jika lengkap, finalisasi
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

        $Monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        // 🔎 Ambil keyword pencarian
        $q = trim($request->input('q', ''));

        // 🔧 Query utama
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

        // 🔎 Filter pencarian berdasarkan nama/kode indikator
        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // 🔒 Filter berdasarkan role
        if ($user->role !== 'admin') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); // hanya tampilkan yg sudah ada target
        }

        $targetIndikators = $targetIndikatorsQuery->get();

        return view('pages.index-show-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'type_menu'         => 'monitoringiku',
            'user'              => $user,
            'q'                 => $q, // kirim ke view agar tetap tampil di input pencarian
        ]);
    }

    public function finalizeUnit($mti_id)
    {
        $user = Auth::user();
        $unit_id = $user->unit_id;

        \App\Models\MonitoringFinalUnit::updateOrCreate(
            [
                'monitoring_iku_id' => $mti_id,
                'unit_id' => $unit_id,
            ],
            [
                'status' => true,
                'finalized_by' => $user->id,
                'finalized_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Unit berhasil difinalisasi.']);
    }

    // public function cancelFinalizeByAdmin($unit_id)
    // {
    //     if (Auth::user()->role !== 'admin') {
    //         return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
    //     }

    //     $record = \App\Models\MonitoringFinalUnit::where('unit_id', $unit_id)
    //         ->where('status', true)
    //         ->first();

    //     if (!$record) {
    //         return response()->json(['success' => false, 'message' => 'Tidak ada finalisasi aktif untuk unit ini.']);
    //     }

    //     $record->update([
    //         'status' => false,
    //         'finalized_by' => null,
    //         'finalized_at' => null,
    //     ]);

    //     return response()->json(['success' => true, 'message' => 'Finalisasi unit berhasil dibatalkan oleh admin.']);
    // }
    
    public function cancelFinalizeByAdmin($unit_id)
    {
        MonitoringFinalUnit::where('unit_id', $unit_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Finalisasi unit berhasil dibatalkan.'
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

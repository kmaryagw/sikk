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
                // Filter monitoringDetail berdasarkan mti_id aktif
                'monitoringDetail' => function ($q) use ($mti_id) {
                    $q->where('mti_id', $mti_id);
                },
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
                $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
            });
        }

        // Filter jika bukan admin atau fakultas
        if ($user->role !== 'admin' && $user->role !== 'fakultas') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); // hanya tampilkan yang sudah ada target
        }

        // Eksekusi query
        $targetIndikators = $targetIndikatorsQuery->get();

        // Debug untuk memeriksa apakah monitoringDetail sudah dimuat dengan benar
        // dd($targetIndikators->pluck('monitoringDetail')); // Memeriksa monitoringDetail pada setiap target_indikator

        // Ambil data unit kerja untuk dropdown filter
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
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];
        $user = Auth::user();
        $status = ['tercapai', 'tidak tercapai', 'tidak terlaksana'];

        $q = trim($request->input('q', '')); // keyword search
        $unitKerjaFilter = $request->input('unit_kerja', ''); // filter unit kerja

        // Query untuk target indikator
        $targetIndikatorQuery = target_indikator::where('prodi_id', $monitoringiku->prodi_id)
            ->where('th_id', $monitoringiku->th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($monitoringiku) {
                    $sub->where('prodi_id', $monitoringiku->prodi_id)
                        ->where('th_id', $monitoringiku->th_id);
                },
                // Mengambil monitoringDetail
                'monitoringDetail' => function ($q) use ($mti_id) {
                    $q->where('mti_id', $mti_id); // Pastikan data monitoringDetail yang sesuai dengan mti_id diambil
                },
            ]);

        // Filter pencarian
        if ($q !== '') {
            $targetIndikatorQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // Filter unit kerja
        if ($unitKerjaFilter) {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
            });
        }

        // Filter jika bukan admin/fakultas
        if ($user->role !== 'admin' && $user->role !== 'fakultas') {
            $targetIndikatorQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            });
        }

        // Eksekusi query untuk mengambil target indikator
        $targetIndikator = $targetIndikatorQuery->get();

        if ($targetIndikator->isEmpty()) {
            return redirect()->route('monitoringiku.index')
                ->with('error', 'Tidak ada indikator yang bisa diukur atau Anda tidak memiliki akses.');
        }

        // Ambil atau buat detail untuk setiap indikator terlebih dahulu (agar monitoringikuDetail tersedia)
        foreach ($targetIndikator as $target) {
            MonitoringIKU_Detail::firstOrCreate(
                ['mti_id' => $mti_id, 'ti_id' => $target->ti_id],
                [
                    'mtid_id' => 'MTID'.Str::uuid(),
                    'mtid_target' => $target->ti_target,
                    'mtid_capaian' => null,
                    'mtid_status' => 'Draft',
                ]
            );
        }

        // Ambil collection monitoring detail untuk semua ti_id yang akan ditampilkan
        // **Key by ti_id supaya pencocokan di view selalu tepat**
        $monitoringikuDetail = MonitoringIKU_Detail::where('mti_id', $mti_id)
            ->whereIn('ti_id', $targetIndikator->pluck('ti_id'))
            ->get()
            ->keyBy('ti_id'); // <<< penting

        $unitKerja = UnitKerja::all();

        return view('pages.create-detail-monitoringiku', [
            'monitoringiku'       => $monitoringiku,
            'targetIndikator'     => $targetIndikator,
            'status'              => $status,
            'monitoringikuDetail' => $monitoringikuDetail, // <<-- pastikan ini di-pass
            'unitKerja'           => $unitKerja,
            'type_menu'           => 'monitoringiku',
            'isAdmin'             => $user->role === 'admin' || $user->role === 'fakultas',
            'q'                   => $q,
            'unitKerjaFilter'     => $unitKerjaFilter,
        ]);
    }

public function storeDetail(Request $request, $mti_id)
{
    // 1. Validasi Input
    $validated = $request->validate([
        'ti_id'             => 'required|array',
        'ti_id.*'           => 'required|string',
        'mtid_capaian'      => 'nullable|array',
        'mtid_keterangan'   => 'nullable|array',
        'mtid_url'          => 'nullable|array',
        'mtid_url.*'        => 'nullable|url', 
        'mtid_evaluasi'     => 'nullable|array',
        'mtid_tindaklanjut' => 'nullable|array',
        'mtid_peningkatan'  => 'nullable|array',
    ]);

    try {
        $monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $user = Auth::user();

        if ($monitoringiku->status == 1) {
            return redirect()
                ->route('monitoringiku.index')
                ->with('error', 'Monitoring IKU ini sudah final dan tidak dapat diubah.');
        }

        // Loop data input
        foreach ($request->ti_id as $index => $ti_id) {
            
            // Ambil Data Target Indikator
            $targetIndikator = target_indikator::with('indikatorKinerja')
                ->where('ti_id', $ti_id)
                ->first();

            if (!$targetIndikator) {
                continue; 
            }

            // Ambil data existing di database (jika ada)
            $existing = MonitoringIKU_Detail::where('mti_id', $mti_id)
                ->where('ti_id', $ti_id)
                ->first();

            $mtid_id = $existing ? $existing->mtid_id : 'MTID' . strtoupper(uniqid());
            $jenis   = strtolower($targetIndikator->indikatorKinerja->ik_ketercapaian ?? 'nilai');

            // --- PERSIAPAN DATA ---
            // Default ambil dari existing agar data lama tidak hilang jika field disable
            $val_capaian      = $existing->mtid_capaian ?? null;
            $val_keterangan   = $existing->mtid_keterangan ?? null;
            $val_url          = $existing->mtid_url ?? null;
            $val_evaluasi     = $existing->mtid_evaluasi ?? null;
            $val_tindaklanjut = $existing->mtid_tindaklanjut ?? null;
            $val_peningkatan  = $existing->mtid_peningkatan ?? null;
            $val_status       = $existing->mtid_status ?? 'Draft';

            // --- LOGIC PERBAIKAN ROLE ---
            
            // Cek apakah User adalah Pengisi Data (Prodi / Unit Kerja)
            // Jika role BUKAN admin DAN BUKAN fakultas, maka dia adalah pengisi data
            if ($user->role !== 'admin' && $user->role !== 'fakultas') {
                
                // Update Capaian, Keterangan, URL
                // Gunakan array_key_exists atau isset untuk memastikan data dikirim dari form
                if (array_key_exists($index, $request->mtid_capaian ?? [])) {
                    $val_capaian = $request->mtid_capaian[$index];
                }
                if (array_key_exists($index, $request->mtid_keterangan ?? [])) {
                    $val_keterangan = $request->mtid_keterangan[$index];
                }
                if (array_key_exists($index, $request->mtid_url ?? [])) {
                    $val_url = $request->mtid_url[$index];
                }

                // Hitung Status otomatis
                if ($val_capaian !== null && $val_capaian !== '') {
                    $val_status = $this->hitungStatus($val_capaian, $targetIndikator->ti_target, $jenis);
                } else {
                    $val_status = 'Draft'; 
                }

            } else {
                // LOGIC ADMIN / FAKULTAS (Hanya Evaluasi & Tindak Lanjut)
                if (array_key_exists($index, $request->mtid_evaluasi ?? [])) {
                    $val_evaluasi = $request->mtid_evaluasi[$index];
                }
                if (array_key_exists($index, $request->mtid_tindaklanjut ?? [])) {
                    $val_tindaklanjut = $request->mtid_tindaklanjut[$index];
                }
                if (array_key_exists($index, $request->mtid_peningkatan ?? [])) {
                    $val_peningkatan = $request->mtid_peningkatan[$index];
                }
            }

            // 3. Simpan Data (Update Or Create)
            $detail = MonitoringIKU_Detail::updateOrCreate(
                [
                    'mti_id' => $mti_id,
                    'ti_id'  => $ti_id,
                ],
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

            // 4. Simpan History (Jika ada perubahan)
            $hasChanged = false;
            if (!$existing) {
                $hasChanged = true;
            } else {
                if (
                    $existing->mtid_capaian != $val_capaian ||
                    $existing->mtid_keterangan != $val_keterangan ||
                    $existing->mtid_status != $val_status ||
                    $existing->mtid_url != $val_url ||
                    $existing->mtid_evaluasi != $val_evaluasi ||
                    $existing->mtid_tindaklanjut != $val_tindaklanjut ||
                    $existing->mtid_peningkatan != $val_peningkatan
                ) {
                    $hasChanged = true;
                }
            }

            if ($hasChanged) {
                HistoryMonitoringIKU::create([
                    'hmi_id'           => 'HMI' . strtoupper(uniqid()),
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

        // Panggil Alert Facade (pastikan sudah di use di atas namespace)
        // use RealRashid\SweetAlert\Facades\Alert; 
        \RealRashid\SweetAlert\Facades\Alert::success('Sukses', 'Data Berhasil Disimpan/Diupdate');
        return redirect()->route('monitoringiku.index-detail', $mti_id);

    } catch (\Exception $e) {
        \RealRashid\SweetAlert\Facades\Alert::error('Error', 'Terjadi Kesalahan: ' . $e->getMessage());
        return redirect()->route('monitoringiku.index-detail', $mti_id);
    }
}


    public function editDetail($mti_id, $ti_id)
    {
        $monitoringiku = MonitoringIKU::with(['prodi', 'tahunKerja'])->findOrFail($mti_id);
        $user = Auth::user();
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
            // KHUSUS USER: Capaian, URL, Keterangan WAJIB DIISI
            $rules = [
                'mtid_capaian'    => 'required|string', 
                'mtid_keterangan' => 'required|string',
                'mtid_url'        => 'required|url', 
                
                // Validasi Kondisional untuk Nilai Angka
                'capaian_value'   => [
                    'nullable', // Tetap nullable di sini karena jika "Ada", nilai ini kosong/null
                    function ($attribute, $value, $fail) use ($request) {
                        $type = $request->mtid_capaian;
                        
                        // Jika tipe adalah angka/persen/rasio, MAKA value WAJIB DIISI
                        if (in_array($type, ['persentase', 'nilai', 'rasio'])) {
                            if (is_null($value) || trim($value) === '') {
                                $fail('Nilai capaian wajib diisi.');
                            }
                            
                            // Validasi format angka
                            if (in_array($type, ['persentase', 'nilai']) && !is_numeric($value)) {
                                $fail('Nilai capaian harus berupa angka.');
                            }
                            // Validasi format rasio
                            if ($type === 'rasio' && !preg_match('/^\d+\s*:\s*\d+$/', $value)) {
                                $fail('Format rasio harus angka:angka (contoh: 1:20).');
                            }
                        }
                    }
                ],
                // 'mtid_keterangan' => 'nullable|string',
                // 'mtid_status'     => 'required|in:tercapai,tidak tercapai,tidak terlaksana',
                // 'mtid_url'        => 'required|url',
            ];
        }

        $validated = $request->validate($rules);

        try {
            // 2. Ambil Data Existing atau Buat Baru
            $monitoringikuDetail = MonitoringIKU_Detail::firstOrNew([
                'mti_id' => $mti_id,
                'ti_id'  => $ti_id
            ]);

            if (!$monitoringikuDetail->mtid_id) {
                $monitoringikuDetail->mtid_id = 'MTID'.Str::uuid();
                $monitoringikuDetail->mtid_target = $targetIndikator->ti_target;
            }

            // 3. Proses Update Data
            if ($isAdmin) {
                // ADMIN
                $monitoringikuDetail->mtid_evaluasi     = $validated['mtid_evaluasi'];
                $monitoringikuDetail->mtid_tindaklanjut = $validated['mtid_tindaklanjut'];
                $monitoringikuDetail->mtid_peningkatan  = $validated['mtid_peningkatan'];
            } else {
                // USER
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
                
                // Hitung Status
                $monitoringikuDetail->mtid_status = $this->hitungStatus(
                    $capaianFinal,
                    $targetIndikator->ti_target,
                    $targetIndikator->indikatorKinerja->ik_ketercapaian
                );
            }

            // 4. Simpan
            $monitoringikuDetail->save();

            // 5. Simpan History
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

        // 1. Ambil Data Utama
        $Monitoringiku = MonitoringIKU::findOrFail($mti_id);
        $prodi_id = $Monitoringiku->prodi_id;
        $th_id = $Monitoringiku->th_id;

        // 2. Ambil Input Filter
        $q = trim($request->input('q', ''));
        $unitKerjaFilter = $request->input('unit_kerja'); // Input dari dropdown (jika ada)

        // 3. Query Dasar
        $targetIndikatorsQuery = target_indikator::where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->with([
                'indikatorKinerja.unitKerja',
                'baselineTahun' => function ($sub) use ($prodi_id, $th_id) {
                    $sub->where('prodi_id', $prodi_id)
                        ->where('th_id', $th_id);
                },
                'monitoringDetail', // Untuk data capaian, evaluasi, dll
                // 'historyMonitoring', // Bisa di-comment jika tidak ditampilkan di tabel ini untuk optimasi
            ]);

        // 4. Filter Pencarian (Keyword)
        if ($q !== '') {
            $targetIndikatorsQuery->whereHas('indikatorKinerja', function ($sub) use ($q) {
                $sub->where('ik_nama', 'LIKE', "%{$q}%")
                    ->orWhere('ik_kode', 'LIKE', "%{$q}%");
            });
        }

        // 5. Logika Filter Unit Kerja Berdasarkan Role
        $unitKerjas = []; // Default kosong

        if ($user->role == 'admin' || $user->role == 'fakultas') {
            // A. Role ADMIN/FAKULTAS
            // Ambil daftar unit kerja untuk dropdown di view
            $unitKerjas = \App\Models\UnitKerja::all(); 

            // Jika admin memilih filter unit kerja dari dropdown
            if ($unitKerjaFilter) {
                $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($unitKerjaFilter) {
                    // Gunakan 'unit_kerja.unit_id' untuk mencegah error ambigu
                    $sub->where('unit_kerja.unit_id', $unitKerjaFilter);
                });
            }
        } else {
            // B. Role UNIT KERJA / PRODI (User Biasa)
            // Paksa filter hanya unit kerja user tersebut
            $targetIndikatorsQuery->whereHas('indikatorKinerja.unitKerja', function ($sub) use ($user) {
                $sub->where('unit_kerja.unit_id', $user->unit_id);
            })
            ->whereNotNull('ti_target'); // Hanya tampilkan yang sudah didistribusikan targetnya
        }

        // 6. Eksekusi Query
        $targetIndikators = $targetIndikatorsQuery->get();

        return view('pages.index-show-monitoringiku', [
            'Monitoringiku'     => $Monitoringiku,
            'targetIndikators'  => $targetIndikators,
            'type_menu'         => 'monitoringiku',
            'user'              => $user,
            'q'                 => $q,
            'unitKerjas'        => $unitKerjas,      // Dikirim untuk isi dropdown
            'unitKerjaFilter'   => $unitKerjaFilter, // Dikirim agar dropdown tetap terpilih setelah search
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

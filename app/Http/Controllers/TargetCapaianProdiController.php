<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use App\Models\IkBaselineTahun;
use App\Models\target_indikator;
use App\Models\UnitKerja;  
use App\Notifications\TargetTersediaNotification;
use App\Models\User;
use App\Models\MonitoringIKU;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Collection;


class TargetCapaianProdiController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'prodi') {
            abort(403, 'Unauthorized access');
        }
    }

    public function index(Request $request)
    {
        $title = 'Data Target Capaian Prodi';
        
        $q = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');
        $unitKerjaId = $request->query('unit_kerja');

        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
        $tahun = tahun_kerja::all();
        $prodis = program_studi::all();
        
        $unitKerjas = UnitKerja::orderBy('unit_nama', 'asc')->get();

        $tahunLabel = '-';
        if ($tahunId) {
            $tahunModel = tahun_kerja::find($tahunId);
            $tahunLabel = $tahunModel ? $tahunModel->th_tahun : '-';
        } elseif ($tahunAktif) {
            $tahunLabel = $tahunAktif->th_tahun;
            $tahunId = $tahunAktif->th_id;
        }

        $query = target_indikator::query()
            ->select(
                'target_indikator.*', 
                'indikator_kinerja.ik_kode', 
                'indikator_kinerja.ik_nama', 
                'indikator_kinerja.ik_jenis', 
                'indikator_kinerja.ik_ketercapaian',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        if (Auth::user()->role == 'prodi') {
            $query->where('target_indikator.prodi_id', Auth::user()->prodi_id);
            $prodis = program_studi::where('prodi_id', Auth::user()->prodi_id)->get();
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('target_indikator.ti_target', 'like', "%$q%")
                    ->orWhere('indikator_kinerja.ik_nama', 'like', "%$q%")
                    ->orWhere('indikator_kinerja.ik_kode', 'like', "%$q%");
            });
        }

        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        if ($unitKerjaId) {
            $query->whereIn('indikator_kinerja.ik_id', function($subQuery) use ($unitKerjaId) {
                $subQuery->select('ik_id')
                        ->from('indikatorkinerja_unitkerja')
                        ->where('unit_id', $unitKerjaId);
            });
        }


        $query->orderBy('indikator_kinerja.ik_nama', 'asc');

        $target_capaians = $query->get();

        $ikProdiPairs = $target_capaians->map(function ($item) {
            return [
                'ik_id' => $item->ik_id,
                'prodi_id' => $item->prodi_id
            ];
        })->unique();

        $baselineData = IkBaselineTahun::where('th_id', $tahunId)
            ->whereIn('ik_id', $ikProdiPairs->pluck('ik_id'))
            ->whereIn('prodi_id', $ikProdiPairs->pluck('prodi_id'))
            ->get()
            ->keyBy(function ($item) {
                return $item->ik_id . '_' . $item->prodi_id;
            });

        $target_capaians->transform(function ($item) use ($baselineData) {
            $key = $item->ik_id . '_' . $item->prodi_id;
            $item->baseline_tahun = $baselineData[$key]->baseline ?? null;
            return $item;
        });

        return view('targetcapaian.index', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
            'tahunAktif' => $tahunAktif,
            'tahunLabel' => $tahunLabel,
            'prodis' => $prodis,
            'unitKerjas' => $unitKerjas,
            'unitKerjaId' => $unitKerjaId,
            'tahunId' => $tahunId,
            'prodiId' => $prodiId,
            'q' => $q,
            'type_menu' => 'targetcapaianprodi',
        ]);
    }

    public function create(Request $request) 
    {
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->firstOrFail();
        $title = 'Tambah Target Capaian';
        
        $selectedUnit = $request->query('unit_kerja');

        $unitKerjas = UnitKerja::orderBy('unit_nama', 'asc')->get();

        $baseline_from_prev = IkBaselineTahun::where('th_id', $tahuns->th_id)
            ->where('prodi_id', Auth::user()->prodi_id)
            ->pluck('baseline', 'ik_id')
            ->toArray();

        $queryIndikator = IndikatorKinerja::query()
            ->where('ik_is_aktif', 'y');

        if ($selectedUnit) {
            $queryIndikator->whereHas('unitKerja', function($q) use ($selectedUnit) {
                $q->where('indikatorkinerja_unitkerja.unit_id', $selectedUnit);
            });
        } else {
            $queryIndikator->has('unitKerja'); 
        }

        $indikatorkinerjas = $queryIndikator->orderBy('ik_nama')
            ->get()
            ->map(function ($ik) use ($baseline_from_prev) {
                $ik->baseline_tahun = $baseline_from_prev[$ik->ik_id] ?? null;
                return $ik;
            });

        $targetindikators = target_indikator::where('th_id', $tahuns->th_id)
            ->where('prodi_id', Auth::user()->prodi_id)
            ->get();

        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        $userRole = Auth::user()->role;
        $userProdi = $userRole === 'prodi' ? Auth::user()->programStudi : null;

        return view('targetcapaian.create', [
            'title'             => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'targetindikators'  => $targetindikators,
            'prodis'            => $prodis,
            'tahuns'            => $tahuns,
            'baseline_from_prev'=> $baseline_from_prev,
            'type_menu'         => 'targetcapaianprodi',
            'userRole'          => $userRole,
            'userProdi'         => $userProdi,
            'unitKerjas'        => $unitKerjas,   
            'selectedUnit'      => $selectedUnit, 
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validationRules = [
            'prodi_id' => 'required|string',
            'th_id'    => 'required|string',
            'indikator.*.ik_id' => 'required|string|exists:indikator_kinerja,ik_id',
            'indikator.*.keterangan' => 'nullable',
            
            'indikator.*.baseline' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $ikId = $request->input("indikator.$index.ik_id");
                    $indikator = \App\Models\IndikatorKinerja::find($ikId);

                    if (!$indikator) return;

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = is_null($value) ? '' : strtolower(trim($value));

                    if ($ketercapaian === 'persentase') {
                        if ($value === '') { $request->merge([ "indikator.$index.baseline" => '0' ]); return; }
                        if (!is_numeric($value) || $value < 0 || $value > 100) $fail("Baseline Persentase harus 0-100.");
                    } 
                    elseif ($ketercapaian === 'nilai') {
                        if ($value === '') { $request->merge([ "indikator.$index.baseline" => '0' ]); return; }
                        if (!is_numeric($value) || $value < 0) $fail("Baseline Nilai minimal 0.");
                    } 
                    elseif ($ketercapaian === 'ketersediaan') {
                        if ($value === '') { $request->merge([ "indikator.$index.baseline" => 'draft' ]); return; }
                        if (!in_array($value, ['ada', 'draft'])) $fail("Baseline harus 'ada' atau 'draft'.");
                    } 
                    elseif ($ketercapaian === 'rasio') {
                        if ($value === '') { $request->merge([ "indikator.$index.baseline" => '0:0' ]); return; }
                        if (!preg_match('/^\d+\s*:\s*\d+$/', $value)) { $fail("Format Baseline harus 'angka:angka'."); return; }
                        $value = preg_replace('/\s*/', '', $value);
                        [$left, $right] = explode(':', $value);
                        $request->merge([ "indikator.$index.baseline" => "{$left} : {$right}" ]);
                    }
                }
            ],

            'indikator.*.target' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $ikId = $request->input("indikator.$index.ik_id");
                    $indikator = \App\Models\IndikatorKinerja::find($ikId);

                    if (!$indikator) return;

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = is_null($value) ? '' : strtolower(trim($value));

                    if ($ketercapaian === 'persentase') {
                        if ($value === '') { $request->merge([ "indikator.$index.target" => '0' ]); return; }
                        if (!is_numeric($value) || $value < 0 || $value > 100) $fail("Target Persentase harus 0-100.");
                    } 
                    elseif ($ketercapaian === 'nilai') {
                        if ($value === '') { $request->merge([ "indikator.$index.target" => '0' ]); return; }
                        if (!is_numeric($value) || $value < 0) $fail("Target Nilai minimal 0.");
                    }
                    elseif ($ketercapaian === 'ketersediaan') {
                        if ($value === '') { $request->merge([ "indikator.$index.target" => 'draft' ]); return; }
                        if (!in_array($value, ['ada', 'draft'])) $fail("Target harus 'ada' atau 'draft'.");
                    } 
                    elseif ($ketercapaian === 'rasio') {
                        if ($value === '') { $request->merge([ "indikator.$index.target" => '0:0' ]); return; }
                        if (!preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) { $fail("Format Target harus 'angka:angka' atau '0'."); return; }
                        if ($value === '0') { $request->merge([ "indikator.$index.target" => '0' ]); return; }
                        $value = preg_replace('/\s*/', '', $value);
                        [$left, $right] = explode(':', $value);
                        $request->merge([ "indikator.$index.target" => "{$left} : {$right}" ]);
                    }
                }
            ],
        ];

        $request->validate($validationRules);

        // 2. Proses Penyimpanan Loop
        $th_id    = $request->th_id;
        $prodi_id = $request->prodi_id;
        $affectedUnits = [];

        collect($request->indikator)->each(function ($data) use ($th_id, $prodi_id, &$affectedUnits) {
            $customPrefix = 'TC';
            $timestamp = time();
            $md5Hash = md5($timestamp . $data["ik_id"]);
            $ti_id = $customPrefix . strtoupper($md5Hash);

            // A. Simpan Target Indikator
            target_indikator::updateOrCreate(
                [
                    'ik_id'    => $data["ik_id"],
                    'th_id'    => $th_id,
                    'prodi_id' => $prodi_id
                ],
                [
                    'ti_id'         => $ti_id,
                    'ik_id'         => $data["ik_id"],
                    'ti_target'     => $data["target"] ?? '0', 
                    'ti_keterangan' => $data["keterangan"] ?? '-',
                    'prodi_id'      => $prodi_id,
                    'th_id'         => $th_id
                ]
            );

            // B. Simpan Baseline Tahun
            if (isset($data["baseline"]) && $data["baseline"] !== '') {
                IkBaselineTahun::updateOrCreate(
                    [
                        'ik_id'    => $data["ik_id"],
                        'th_id'    => $th_id,
                        'prodi_id' => $prodi_id
                    ],
                    [
                        'baseline' => strtolower($data["baseline"])
                    ]
                );
            }

            // C. Ambil Unit ID dari Tabel Pivot untuk Notifikasi
            $pivotUnits = \DB::table('indikatorkinerja_unitkerja')
                            ->where('ik_id', $data["ik_id"])
                            ->pluck('unit_id')
                            ->toArray();
            
            if (!empty($pivotUnits)) {
                $affectedUnits = array_merge($affectedUnits, $pivotUnits);
            }
        });

        // 3. Proses Header Monitoring (MonitoringIKU)
        // Menggunakan firstOrCreate agar lebih efisien (Cari dulu, kalau tidak ada baru buat)
        $monitoringHeader = MonitoringIKU::firstOrCreate(
            [
                'prodi_id' => $prodi_id,
                'th_id'    => $th_id
            ],
            [
                'mti_id'   => 'EV' . md5(uniqid(rand(), true)),
                'status'   => 0
            ]
        );
        
        // Ambil ID Monitoring untuk link notifikasi
        $mti_id = $monitoringHeader->mti_id;

        // 4. Proses Pengiriman Notifikasi
        $uniqueUnits = array_unique($affectedUnits);

        if ($mti_id && !empty($uniqueUnits)) {
            
            $namaProdi = \Illuminate\Support\Facades\Auth::user()->programStudi->nama_prodi ?? 'Prodi';
            $tahunAktif = \App\Models\tahun_kerja::where('th_is_aktif', 'y')->first();
            $tahunString = $tahunAktif ? $tahunAktif->th_tahun : date('Y');

            // Ambil User dengan role 'unit kerja' yang terlibat
            $usersToNotify = User::where('role', 'unit kerja')
                                ->whereIn('unit_id', $uniqueUnits)
                                ->get();

            foreach ($usersToNotify as $user) {
                // Parameter: Nama Prodi, Tahun, ID Monitoring (untuk link)
                $user->notify(new \App\Notifications\TargetTersediaNotification($namaProdi, $tahunString, $mti_id, 'update'));
            }
        }

        Alert::success('Sukses', 'Data Berhasil Disimpan');
        return redirect()->route('targetcapaianprodi.index');
    }

    public function edit($targetcapaian)
    {
        $title = 'Edit Target Capaian';

        $targetcapaian = target_indikator::findOrFail($targetcapaian);

        $baselineTahun = IkBaselineTahun::where('ik_id', $targetcapaian->ik_id)
            ->where('th_id', $targetcapaian->th_id)
            ->where('prodi_id', $targetcapaian->prodi_id)
            ->value('baseline');

        $targetcapaian->baseline_tahun = $baselineTahun ?? 'Baseline belum diinput';

        $indikatorkinerjautamas = IndikatorKinerja::orderBy('ik_nama')->get();
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();

        $userRole = Auth::user()->role;
        $userProdi = $userRole === 'prodi' ? Auth::user()->programStudi : null;

        return view('targetcapaian.edit', [
            'title' => $title,
            'targetcapaian' => $targetcapaian,
            'indikatorkinerjautamas' => $indikatorkinerjautamas,
            'baseline' => $targetcapaian->baseline_tahun,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaianprodi',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function update(Request $request, $id)
    {
        $targetcapaian = target_indikator::findOrFail($id);
        $indikatorKinerjas = \App\Models\IndikatorKinerja::find($request->ik_id);

        $prodi_id = $request->prodi_id ?? $targetcapaian->prodi_id;
        $th_id    = $request->th_id ?? $targetcapaian->th_id;

        $validationRules = [
            'ik_id'         => 'required|string',
            'ti_keterangan' => 'required',
            
            'baseline' => [
                'nullable',
                function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                    if (!$indikatorKinerjas || $value === '') return;
                    $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);
                    $value = trim(strtolower($value));

                    if ($ketercapaian === 'persentase') {
                        if (!is_numeric($value) || $value < 0 || $value > 100) $fail("Baseline Persentase harus 0-100.");
                    } 
                    elseif ($ketercapaian === 'nilai') {
                        if (!is_numeric($value) || $value < 0) $fail("Baseline Nilai minimal 0.");
                    } 
                    elseif ($ketercapaian === 'ketersediaan') {
                        if (!in_array($value, ['ada', 'draft'])) $fail("Baseline harus 'ada' atau 'draft'.");
                    } 
                    elseif ($ketercapaian === 'rasio') {
                        if (!preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) { $fail("Format Baseline harus 'angka:angka' atau '0'."); return; }
                        $clean = preg_replace('/\s*/', '', $value);
                        if ($clean !== '0' && strpos($clean, ':') !== false) {
                            [$left, $right] = explode(':', $clean);
                            if ((int)$left === 0 && (int)$right === 0) $fail("Rasio tidak boleh 0:0.");
                        }
                    }
                }
            ],

            'ti_target' => [
                'required',
                function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                    if (!$indikatorKinerjas) return;
                    $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);
                    $value = trim(strtolower($value));
                    
                    if ($ketercapaian === 'persentase') {
                        if ($value !== '' && (!is_numeric($value) || $value < 0 || $value > 100)) $fail("Target Persentase harus 0-100.");
                    } 
                    elseif ($ketercapaian === 'nilai') {
                        if ($value !== '' && (!is_numeric($value) || $value < 0)) $fail("Target Nilai minimal 0.");
                    } 
                    elseif ($ketercapaian === 'ketersediaan') {
                        if (!in_array($value, ['ada', 'draft'])) $fail("Target harus 'ada' atau 'draft'.");
                    } 
                    elseif ($ketercapaian === 'rasio') {
                        if ($value !== '' && !preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) $fail("Format Target harus 'angka:angka' atau '0'.");
                    }
                }
            ]
        ];

        $request->validate($validationRules);

        $targetToSave = $request->ti_target;
        if ($indikatorKinerjas && strtolower($indikatorKinerjas->ik_ketercapaian) === 'rasio') {
            if ($targetToSave !== '0' && strpos($targetToSave, ':') !== false) {
                $clean = preg_replace('/\s*/', '', $targetToSave);
                [$left, $right] = explode(':', $clean);
                $targetToSave = "{$left} : {$right}";
            }
        }

        $targetcapaian->update([
            'ik_id'         => $request->ik_id,
            'ti_target'     => $targetToSave,
            'ti_keterangan' => $request->ti_keterangan,
            'prodi_id'      => $prodi_id, 
            'th_id'         => $th_id,   
        ]);

        $baselineInput = $request->baseline;
        
        if ($indikatorKinerjas && strtolower($indikatorKinerjas->ik_ketercapaian) === 'rasio') {
            if ($baselineInput && $baselineInput !== '0' && strpos($baselineInput, ':') !== false) {
                $clean = preg_replace('/\s*/', '', $baselineInput);
                [$left, $right] = explode(':', $clean);
                $baselineInput = "{$left} : {$right}";
            }
        }

        if (is_null($baselineInput) || $baselineInput === '') {
            $baselineInput = IkBaselineTahun::where('ik_id', $request->ik_id)
                ->where('th_id', $th_id)
                ->where('prodi_id', $prodi_id)
                ->value('baseline');
        }

        if (!is_null($baselineInput)) {
            IkBaselineTahun::updateOrCreate(
                [
                    'ik_id'    => $request->ik_id,
                    'th_id'    => $th_id,
                    'prodi_id' => $prodi_id
                ],
                [
                    'baseline' => strtolower($baselineInput)
                ]
            );
        }

        // Notifikasi ke Unit Kerja Terkait
        $unitIds = \DB::table('indikatorkinerja_unitkerja')
                    ->where('ik_id', $request->ik_id)
                    ->pluck('unit_id')
                    ->toArray();

        if (!empty($unitIds)) {
            
            $monitoringHeader = MonitoringIKU::firstOrCreate(
                [
                    'prodi_id' => $prodi_id,
                    'th_id'    => $th_id
                ],
                [
                    'mti_id'   => 'EV' . md5(uniqid(rand(), true)),
                    'status'   => 0
                ]
            );
            
            $mti_id = $monitoringHeader->mti_id;

            $usersToNotify = User::where('role', 'unit kerja')
                                ->whereIn('unit_id', $unitIds)
                                ->get();

            if ($usersToNotify->count() > 0) {
                $namaProdi = \Illuminate\Support\Facades\Auth::user()->programStudi->nama_prodi ?? 'Prodi';
                $tahunAktif = \App\Models\tahun_kerja::where('th_is_aktif', 'y')->first();
                $tahunString = $tahunAktif ? $tahunAktif->th_tahun : date('Y');

                foreach ($usersToNotify as $user) {
                    $user->notify(new \App\Notifications\TargetTersediaNotification($namaProdi, $tahunString, $mti_id, 'update'));
                }
            }
        }

        Alert::success('Sukses', 'Data Berhasil Diperbarui');
        return redirect()->route('targetcapaianprodi.index');
    }

    public function destroy($targetcapaian)
    {
        $targetcapaian = target_indikator::find($targetcapaian);
        $targetcapaian->delete();

        Alert::success('Sukses', 'Data Berhasil Dihapus');

        return redirect()->route('targetcapaianprodi.index');
    }
}

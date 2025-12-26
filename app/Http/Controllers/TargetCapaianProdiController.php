<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use App\Models\IkBaselineTahun;
use App\Models\target_indikator;
use App\Models\UnitKerja;  
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

                    if (!$indikator) {
                        $fail("Indikator tidak ditemukan.");
                        return;
                    }

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = is_null($value) ? '' : strtolower(trim($value));

                    // A. PERSENTASE (Harus 0 - 100)
                    if ($ketercapaian === 'persentase') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.baseline" => '0' ]);
                            return;
                        }
                        // Menggunakan is_numeric agar bisa menerima desimal jika diperlukan, 
                        // atau ganti ctype_digit jika harus angka bulat
                        if (!is_numeric($value) || $value < 0 || $value > 100) {
                            $fail("Baseline '$indikator->ik_nama' (Persentase) harus angka antara 0–100.");
                        }
                    } 
                    // B. NILAI (Bebas minimal 0)
                    elseif ($ketercapaian === 'nilai') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.baseline" => '0' ]);
                            return;
                        }
                        if (!is_numeric($value) || $value < 0) {
                            $fail("Baseline '$indikator->ik_nama' (Nilai) harus angka minimal 0.");
                        }
                    } 
                    // B. KETERSEDIAAN (Auto 'draft')
                    elseif ($ketercapaian === 'ketersediaan') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.baseline" => 'draft' ]);
                            return;
                        }
                        if (!in_array($value, ['ada', 'draft'])) {
                            $fail("Baseline '$indikator->ik_nama' hanya boleh 'ada' atau 'draft'.");
                        }
                    } 
                    // C. RASIO (Auto '0:0')
                    elseif ($ketercapaian === 'rasio') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.baseline" => '0:0' ]);
                            return;
                        }
                        if (!preg_match('/^\d+\s*:\s*\d+$/', $value)) {
                            $fail("Baseline '$indikator->ik_nama' harus format 'angka:angka'.");
                            return;
                        }
                        // Format spasi agar rapi "1:2" -> "1 : 2"
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

                    if (!$indikator) {
                        $fail("Indikator tidak ditemukan.");
                        return;
                    }

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = is_null($value) ? '' : strtolower(trim($value));

                    // A. PERSENTASE (Harus 0 - 100)
                    if ($ketercapaian === 'persentase') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.target" => '0' ]);
                            return;
                        }
                        if (!is_numeric($value) || $value < 0 || $value > 100) {
                            $fail("Target '$indikator->ik_nama' (Persentase) harus angka antara 0–100.");
                        }
                    } 
                    // B. NILAI (Bebas minimal 0)
                    elseif ($ketercapaian === 'nilai') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.target" => '0' ]);
                            return;
                        }
                        if (!is_numeric($value) || $value < 0) {
                            $fail("Target '$indikator->ik_nama' (Nilai) harus angka minimal 0.");
                        }
                    }
                    // B. KETERSEDIAAN (Auto 'draft')
                    elseif ($ketercapaian === 'ketersediaan') {
                        if ($value === '') {
                            $request->merge([ "indikator.$index.target" => 'draft' ]);
                            return;
                        }
                        if (!in_array($value, ['ada', 'draft'])) {
                            $fail("Target '$indikator->ik_nama' hanya boleh 'ada' atau 'draft'.");
                        }
                    } 
                    // C. RASIO (Auto '0:0')
                    elseif ($ketercapaian === 'rasio') {
                        // 1. Jika Kosong -> Set '0:0'
                        if ($value === '') {
                            $request->merge([ "indikator.$index.target" => '0:0' ]);
                            return;
                        }

                        // 2. Cek Format (Boleh angka:angka atau 0)
                        if (!preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Target '$indikator->ik_nama' harus '0' atau format 'angka:angka'.");
                            return;
                        }
                        
                        // 3. Jika user ketik '0' manual, biarkan.
                        if ($value === '0') {
                            $request->merge([ "indikator.$index.target" => '0' ]);
                            return;
                        }

                        // 4. Format spasi agar rapi
                        $value = preg_replace('/\s*/', '', $value);
                        [$left, $right] = explode(':', $value);
                        
                        // [PENTING] Validasi "tidak boleh 0:0" DIHAPUS agar 0:0 bisa tersimpan.
                        
                        $request->merge([ "indikator.$index.target" => "{$left} : {$right}" ]);
                    }
                }
            ],
        ];

        $request->validate($validationRules);

        $th_id    = $request->th_id;
        $prodi_id = $request->prodi_id;

        collect($request->indikator)->each(function ($data) use ($th_id, $prodi_id) {
            $customPrefix = 'TC';
            $timestamp = time();
            $md5Hash = md5($timestamp . $data["ik_id"]);
            $ti_id = $customPrefix . strtoupper($md5Hash);

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
        });

        $exists = MonitoringIKU::where('prodi_id', $prodi_id)
            ->where('th_id', $th_id)
            ->exists();

        if (!$exists) {
            MonitoringIKU::create([
                'mti_id' => 'EV' . md5(uniqid(rand(), true)),
                'prodi_id' => $prodi_id,
                'th_id' => $th_id,
                'status' => 0
            ]);
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

    public function update($targetcapaian, Request $request)
    {
        $targetcapaian = target_indikator::findOrFail($targetcapaian);
        $indikatorKinerjas = IndikatorKinerja::find($request->ik_id);

        $validationRules = [
            'ik_id'         => 'required|string',
            'ti_target'     => 'required',
            'ti_keterangan' => 'required',
            'prodi_id'      => 'required|string',
            'th_id'         => 'required|string',
            'baseline'      => [
                'nullable',
                function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                    if (!$indikatorKinerjas || $value === '') {
                        return;
                    }

                    $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);
                    $value = trim(strtolower($value));

                    // VALIDASI BASELINE - PERSENTASE (0 - 100)
                    if ($ketercapaian === 'persentase') {
                        if (!is_numeric($value) || $value < 0 || $value > 100) {
                            $fail("Baseline (Persentase) harus berupa angka antara 0-100.");
                        }
                    } 
                    // VALIDASI BASELINE - NILAI (Bebas >= 0)
                    elseif ($ketercapaian === 'nilai') {
                        if (!is_numeric($value) || $value < 0) {
                            $fail("Baseline (Nilai) harus berupa angka minimal 0.");
                        }
                    } 
                    elseif ($ketercapaian === 'ketersediaan') {
                        if (!in_array($value, ['ada', 'draft'])) {
                            $fail("Baseline hanya boleh 'ada' atau 'draft'.");
                        }
                    } 
                    elseif ($ketercapaian === 'rasio') {
                        if (!preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Baseline harus berupa '0' atau format 'angka:angka'.");
                            return;
                        }
                        $value = preg_replace('/\s*/', '', $value);
                        if ($value !== '0' && strpos($value, ':') !== false) {
                            [$left, $right] = explode(':', $value);
                            if ((int)$left === 0 && (int)$right === 0) {
                                $fail("Baseline rasio tidak boleh 0:0.");
                            }
                        }
                    }
                }
            ],
        ];

        if ($indikatorKinerjas) {
            $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);

            if ($ketercapaian === 'nilai') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                        // Target Nilai: Bebas >= 0
                        if ($value !== '' && (!is_numeric($value) || $value < 0)) {
                            $fail("Target (Nilai) untuk '{$indikatorKinerjas->ik_nama}' harus angka minimal 0.");
                        }
                    }
                ];
            } elseif ($ketercapaian === 'persentase') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                        // Target Persentase: 0 - 100
                        if ($value !== '' && (!is_numeric($value) || $value < 0 || $value > 100)) {
                            $fail("Target (Persentase) untuk '{$indikatorKinerjas->ik_nama}' harus angka antara 0-100.");
                        }
                    }
                ];
            } elseif ($ketercapaian === 'ketersediaan') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!in_array(strtolower($value), ['ada', 'draft'])) {
                            $fail("Target hanya boleh 'ada' atau 'draft'.");
                        }
                    }
                ];
            } elseif ($ketercapaian === 'rasio') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                        $value = trim($value);
                        if ($value !== '' && !preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Target harus berupa '0' atau format 'angka:angka'.");
                            return;
                        }
                        $clean = preg_replace('/\s*/', '', $value);
                        if ($clean !== '0' && strpos($clean, ':') !== false) {
                            [$left, $right] = explode(':', $clean);
                            if ((int)$left === 0 && (int)$right === 0) {
                                $fail("Rasio tidak boleh 0:0.");
                            }
                        }
                    }
                ];
            }
        }

        $request->validate($validationRules);

        // Update Target
        $target = $request->ti_target ?? $targetcapaian->ti_target;
        $targetcapaian->update([
            'ik_id' => $request->ik_id,
            'ti_target' => $target,
            'ti_keterangan' => $request->ti_keterangan,
            'prodi_id' => $request->prodi_id,
            'th_id' => $request->th_id,
        ]);

        // Update atau Simpan Baseline
        $baselineValue = $request->baseline;
        if (is_null($baselineValue) || $baselineValue === '') {
            $baselineValue = IkBaselineTahun::where('ik_id', $request->ik_id)
                ->where('th_id', $request->th_id)
                ->where('prodi_id', $request->prodi_id)
                ->value('baseline');
        }

        if (!is_null($baselineValue)) {
            IkBaselineTahun::updateOrCreate(
                [
                    'ik_id'    => $request->ik_id,
                    'th_id'    => $request->th_id,
                    'prodi_id' => $request->prodi_id
                ],
                [
                    'baseline' => strtolower($baselineValue)
                ]
            );
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

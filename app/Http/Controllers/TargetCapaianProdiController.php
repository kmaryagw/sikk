<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use App\Models\IkBaselineTahun;
use App\Models\target_indikator;
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

        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
        $tahun = tahun_kerja::all();
        $prodis = program_studi::all();

        $tahunLabel = '-';
        if ($tahunId) {
            $tahunModel = tahun_kerja::find($tahunId);
            $tahunLabel = $tahunModel ? $tahunModel->th_tahun : '-';
        } elseif ($tahunAktif) {
            $tahunLabel = $tahunAktif->th_tahun;
        }

        if (!$tahunId && $tahunAktif) {
            $tahunId = $tahunAktif->th_id;
        }

        $query = target_indikator::query()
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftjoin('unit_kerja', 'unit_kerja.unit_id', '=', 'indikator_kinerja.unit_id');

        $query->whereNotNull('indikator_kinerja.unit_id')
            ->where('indikator_kinerja.unit_id', '!=', '');
           

        // Jika user prodi, batasi ke prodi mereka
        if (Auth::user()->role == 'prodi') {
            $query->where('target_indikator.prodi_id', Auth::user()->prodi_id);
            $prodis = program_studi::where('prodi_id', Auth::user()->prodi_id)->get();
        }

        // Filter pencarian fleksibel
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

        $query->orderBy('indikator_kinerja.ik_nama', 'asc');

        $target_capaians = $query->paginate(10)->withQueryString();
        $no = $target_capaians->firstItem();

        // Ambil semua ik_id unik dari target capaian
        $ikIds = $target_capaians->pluck('ik_id')->unique()->toArray();

        // Ambil data baseline berdasarkan ik_id dan tahun aktif yang sedang ditampilkan
        $baselineQuery = IkBaselineTahun::whereIn('ik_id', $ikIds)
            ->where('th_id', $tahunId);

        if (Auth::user()->role == 'prodi') {
            $baselineQuery->where('prodi_id', Auth::user()->prodi_id);
        } elseif ($prodiId) {
            $baselineQuery->where('prodi_id', $prodiId);
        }

        $baselineMap = $baselineQuery->pluck('baseline', 'ik_id');

        // Tambahkan baseline ke setiap elemen koleksi
        $target_capaians->getCollection()->transform(function ($item) use ($baselineMap) {
            $item->baseline_tahun = $baselineMap[$item->ik_id] ?? null; // default 0 jika tidak ditemukan
            return $item;
        });

        return view('targetcapaian.index', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
            'tahunAktif' => $tahunAktif,
            'tahunLabel' => $tahunLabel,
            'prodis' => $prodis,
            'tahunId' => $tahunId,
            'prodiId' => $prodiId,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'targetcapaianprodi',
        ]);
    }

    public function create()
    {
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->firstOrFail();
        $title = 'Tambah Target Capaian';

        // Ambil baseline yang sudah di-generate oleh TahunController
        $baseline_from_prev = IkBaselineTahun::where('th_id', $tahuns->th_id)
            ->where('prodi_id', Auth::user()->prodi_id)
            ->pluck('baseline', 'ik_id')
            ->toArray();

        $indikatorkinerjas = IndikatorKinerja::where('ik_is_aktif', 'y')
            ->whereNotNull('unit_id')
            ->where('unit_id', '!=', '')
            ->orderBy('ik_nama')
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
            'title' => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'targetindikators' => $targetindikators,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'baseline_from_prev' => $baseline_from_prev,
            'type_menu' => 'targetcapaianprodi',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'prodi_id' => 'required|string',
            'th_id'    => 'required|string',
            'indikator.*.ik_id' => 'required|string|exists:indikator_kinerja,ik_id',
            'indikator.*.keterangan' => 'nullable',
            'indikator.*.baseline'   => 'nullable|string', // baseline per prodi

            'indikator.*.baseline' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $ikId = $request->input("indikator.$index.ik_id");
                    $indikator = IndikatorKinerja::find($ikId);

                    if (!$indikator) {
                        $fail("Indikator tidak ditemukan.");
                        return;
                    }

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = strtolower(trim($value));

                    if ($ketercapaian === 'nilai' || $ketercapaian === 'persentase') {
                        if ($value !== '' && (!ctype_digit($value) || $value < 0 || $value > 100)) {
                            $fail("Baseline untuk indikator '$indikator->ik_nama' harus bilangan bulat antara 0–100.");
                        }
                    } elseif ($ketercapaian === 'ketersediaan') {
                        if ($value !== '' && !in_array($value, ['ada', 'draft'])) {
                            $fail("Baseline untuk indikator '$indikator->ik_nama' hanya boleh 'ada' atau 'draft'.");
                        }
                    } elseif ($ketercapaian === 'rasio') {
                        if ($value !== '' && !preg_match('/^\d+\s*:\s*\d+$/', $value)) {
                            $fail("Baseline untuk indikator '$indikator->ik_nama' harus format 'angka:angka'.");
                            return;
                        }
                        $value = preg_replace('/\s*/', '', $value);
                        [$left, $right] = explode(':', $value);
                        if ((int)$left === 0 && (int)$right === 0) {
                            $fail("Baseline untuk indikator '$indikator->ik_nama' tidak boleh 0:0.");
                            return;
                        }
                        $request->merge([
                            "indikator.$index.baseline" => "{$left} : {$right}"
                        ]);
                    }
                }
            ],

            'indikator.*.target' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // ambil index array indikator
                    $ikId = $request->input("indikator.$index.ik_id");
                    $indikator = IndikatorKinerja::find($ikId);

                    if (!$indikator) {
                        $fail("Indikator tidak ditemukan.");
                        return;
                    }

                    $ketercapaian = strtolower($indikator->ik_ketercapaian);
                    $value = strtolower(trim($value));

                    if ($ketercapaian === 'nilai') {
                        if ($value !== '' && (!ctype_digit($value) || $value < 0 || $value > 100)) {
                            $fail("Target untuk indikator '$indikator->ik_nama' harus bilangan bulat >= 0 dan <= 100.");
                        }
                    } elseif ($ketercapaian === 'persentase') {
                        if ($value !== '' && (!ctype_digit($value) || $value < 0 || $value > 100)) {
                            $fail("Target untuk indikator '$indikator->ik_nama' harus bilangan bulat antara 0-100.");
                        }
                    } elseif ($ketercapaian === 'ketersediaan') {
                        if ($value !== '' && !in_array($value, ['ada', 'draft'])) {
                            $fail("Target untuk indikator '$indikator->ik_nama' hanya boleh 'ada' atau 'draft'.");
                        }
                    } elseif ($ketercapaian === 'rasio') {
                        $value = trim($value);

                        // Validasi pola: hanya '0' atau 'angka:angka'
                        if ($value !== '' && !preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Target untuk indikator '$indikator->ik_nama' harus berupa '0' atau format 'angka:angka' (contoh: 0, 1:2, 0:5).");
                            return;
                        }

                        // Hapus semua spasi
                        $value = preg_replace('/\s*/', '', $value);

                        // Jika hanya 0, langsung diterima tanpa explode
                        if ($value === '0') {
                            $request->merge([
                                "indikator.$index.target" => '0'
                            ]);
                            return;
                        }

                        // Jika mengandung ":", pecah jadi dua bagian
                        if (strpos($value, ':') !== false) {
                            [$left, $right] = explode(':', $value);

                            // Pastikan dua sisi adalah angka
                            if (!is_numeric($left) || !is_numeric($right)) {
                                $fail("Rasio untuk indikator '$indikator->ik_nama' harus berisi angka di kedua sisi (contoh: 1:2).");
                                return;
                            }

                            // Tidak boleh 0:0
                            if ((int)$left === 0 && (int)$right === 0) {
                                $fail("Rasio untuk indikator '$indikator->ik_nama' tidak boleh 0:0.");
                                return;
                            }

                            $request->merge([
                                "indikator.$index.target" => "{$left} : {$right}"
                            ]);
                        }
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

            // Simpan target capaian prodi
            target_indikator::updateOrCreate(
                [
                    'ik_id'    => $data["ik_id"],
                    'th_id'    => $th_id,
                    'prodi_id' => $prodi_id
                ],
                [
                    'ti_id'         => $ti_id,
                    'ik_id'         => $data["ik_id"],
                    'ti_target'     => $data["target"] ?? 0,
                    'ti_keterangan' => $data["keterangan"] ?? '-',
                    'prodi_id'      => $prodi_id,
                    'th_id'         => $th_id
                ]
            );

            // Simpan baseline prodi (kalau diisi)
            if (!empty($data["baseline"])) {
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

         // 🔹 Tambahan otomatis buat MonitoringIKU
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

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('targetcapaianprodi.index');
    }

    public function edit($targetcapaian)
    {
        $title = 'Edit Target Capaian';

        // Ambil target capaian prodi
        $targetcapaian = target_indikator::findOrFail($targetcapaian);

        // Ambil baseline tahun terkait
        $baselineTahun = IkBaselineTahun::where('ik_id', $targetcapaian->ik_id)
            ->where('th_id', $targetcapaian->th_id)
            ->where('prodi_id', $targetcapaian->prodi_id)
            ->value('baseline');

        // Simpan ke properti tambahan supaya bisa dipanggil di view
        $targetcapaian->baseline_tahun = $baselineTahun ?? 'Baseline belum diinput';

        // Data dropdown
        $indikatorkinerjautamas = IndikatorKinerja::orderBy('ik_nama')->get();
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $tahuns = tahun_kerja::orderBy('th_tahun', 'desc')->get();

        // Role & Prodi User
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

        // Validasi umum
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

                    if ($ketercapaian === 'nilai' || $ketercapaian === 'persentase') {
                        if (!ctype_digit($value) || $value < 0 || $value > 100) {
                            $fail("Baseline harus bilangan bulat antara 0-100.");
                        }
                    } elseif ($ketercapaian === 'ketersediaan') {
                        if (!in_array($value, ['ada', 'draft'])) {
                            $fail("Baseline hanya boleh 'ada' atau 'draft'.");
                        }
                    } elseif ($ketercapaian === 'rasio') {
                        // ✅ Izinkan "0" atau "angka:angka"
                        if (!preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Baseline harus berupa '0' atau format 'angka:angka' (contoh: 0, 1:2, 0:5).");
                            return;
                        }

                        // Hilangkan semua spasi
                        $value = preg_replace('/\s*/', '', $value);

                        // Jika hanya 0 → valid, tidak perlu proses lebih lanjut
                        if ($value === '0') {
                            return;
                        }

                        // Jika mengandung titik dua
                        if (strpos($value, ':') !== false) {
                            [$left, $right] = explode(':', $value);

                            // Validasi bahwa kedua sisi adalah angka
                            if (!is_numeric($left) || !is_numeric($right)) {
                                $fail("Baseline harus berisi angka di kedua sisi (contoh: 1:2).");
                                return;
                            }

                            // Tidak boleh 0:0
                            if ((int)$left === 0 && (int)$right === 0) {
                                $fail("Baseline tidak boleh 0:0.");
                                return;
                            }
                        }
                    }
                }
            ],
        ];

        // Validasi khusus berdasarkan jenis ketercapaian
        if ($indikatorKinerjas) {
            $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);

            if ($ketercapaian === 'nilai') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                        if ($value !== '' && (!ctype_digit($value) || $value < 0 || $value > 100)) {
                            $fail("Target untuk indikator '{$indikatorKinerjas->ik_nama}' harus bilangan bulat >= 0 dan <= 100.");
                        }
                    }
                ];
            } elseif ($ketercapaian === 'persentase') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {
                        if ($value !== '' && (!ctype_digit($value) || $value < 0 || $value > 100)) {
                            $fail("Target untuk indikator '{$indikatorKinerjas->ik_nama}' harus bilangan bulat antara 0-100.");
                        }
                    }
                ];
            } elseif ($ketercapaian === 'ketersediaan') {
                $validationRules['ti_target'] = 'required|in:ada,draft';
            } elseif ($ketercapaian === 'rasio') {
                $validationRules['ti_target'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($indikatorKinerjas) {

                        $value = trim($value);

                        // ✅ izinkan 0 atau angka:angka
                        if ($value !== '' && !preg_match('/^(0|\d+\s*:\s*\d+)$/', $value)) {
                            $fail("Target untuk indikator '{$indikatorKinerjas->ik_nama}' harus berupa '0' atau format 'angka:angka' (contoh: 0, 1:2, 0:5).");
                            return;
                        }

                        // hilangkan spasi
                        $clean = preg_replace('/\s*/', '', $value);

                        // Jika hanya "0", valid — langsung return
                        if ($clean === '0') {
                            return;
                        }

                        // Jika ada titik dua
                        if (strpos($clean, ':') !== false) {
                            [$left, $right] = explode(':', $clean);

                            // validasi numeric
                            if (!is_numeric($left) || !is_numeric($right)) {
                                $fail("Target untuk indikator '{$indikatorKinerjas->ik_nama}' harus berisi angka di kedua sisi (contoh: 1:2).");
                                return;
                            }

                            // Tidak boleh 0:0
                            if ((int)$left === 0 && (int)$right === 0) {
                                $fail("Rasio untuk indikator '{$indikatorKinerjas->ik_nama}' tidak boleh 0:0.");
                                return;
                            }
                        }
                    }
                ];
            }
        }


        $customMessages = [
            'ti_target.in' => 'Untuk jenis ketersediaan, hanya boleh diisi "ada" atau "draft".',
        ];

        $request->validate($validationRules, $customMessages);


        // Update target capaian
        $targetcapaian->update([
            'ik_id'         => $request->ik_id,
            'ti_target'     => $ti_target,
            'ti_keterangan' => $request->ti_keterangan,
            'prodi_id'      => $request->prodi_id,
            'th_id'         => $request->th_id,
        ]);

        // Ambil baseline lama kalau field kosong
        $baselineValue = $request->baseline;
        if (empty($baselineValue)) {
            $baselineValue = IkBaselineTahun::where('ik_id', $request->ik_id)
                ->where('th_id', $request->th_id)
                ->where('prodi_id', $request->prodi_id)
                ->value('baseline');
        }

        // Simpan baseline (update atau create)
        if (!is_null($baselineValue)) {
            IkBaselineTahun::updateOrCreate(
                [
                    'ik_id'    => $request->ik_id,
                    'th_id'    => $request->th_id,
                    'prodi_id' => $request->prodi_id
                ],
                [
                    'baseline' => $baselineValue
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

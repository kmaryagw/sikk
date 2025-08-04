<?php

namespace App\Http\Controllers;

use App\Exports\IndikatorKinerjaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IndikatorKinerja;
use App\Models\Standar;
use App\Models\tahun_kerja;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndikatorKinerjaImport;
use Illuminate\Support\Str;


class IndikatorKinerjaController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Indikator Kinerja Utama';
        $q = $request->query('q');

        // Ambil tahun aktif
        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();

        $query = IndikatorKinerja::select('indikator_kinerja.*', 'standar.std_nama', 'ik_baseline_tahun.baseline as baseline_tahun')
            ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
            ->leftJoin('ik_baseline_tahun', function ($join) use ($tahunAktif) {
                $join->on('indikator_kinerja.ik_id', '=', 'ik_baseline_tahun.ik_id')
                    ->where('ik_baseline_tahun.th_id', '=', $tahunAktif?->th_id);
            })
            ->orderBy('ik_kode', 'asc');

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('ik_kode', 'like', '%' . $q . '%')
                    ->orWhere('ik_nama', 'like', '%' . $q . '%')
                    ->orWhere('std_nama', 'like', '%' . $q . '%');
            });
        }

        $indikatorkinerjas = $query->paginate(10)->withQueryString();
        $no = $indikatorkinerjas->firstItem();

        return view('pages.index-indikatorkinerja', [
            'title' => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Lewati baris pertama jika itu header
            array_shift($rows);

            foreach ($rows as $row) {
                $stdNama = trim($row[2]); // Ambil `std_nama` dari kolom ke-3
                $standar = \App\Models\Standar::where('std_nama', $stdNama)->first();

                if (!$standar) {
                    return back()->with('error', "$stdNama tidak ditemukan.");
                }

                IndikatorKinerja::create([
                    'ik_id' => Str::uuid()->toString(),
                    'ik_kode' => trim($row[0]), 
                    'ik_nama' => trim($row[1]), 
                    'std_id' => $standar->std_id, // Menggunakan `std_id` dari hasil pencarian
                    'ik_jenis' => trim($row[3]),
                    'ik_ketercapaian' => trim($row[4]),
                    'ik_baseline' => trim($row[5]),
                    'ik_is_aktif' => strtolower(trim($row[6])) == 'y' ? 'y' : 'n',
                ]);
            }

            return back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function downloadTemplate()
    {
        return Excel::download(new IndikatorKinerjaTemplateExport, 'template_indikator_kinerja.xlsx');
    }

    public function create()
    {   
        $title = 'Tambah Indikator Kinerja Utama';
        $jeniss = ['IKU', 'IKT', 'IKU/IKT'];
        $ik_is_aktifs = ['y', 'n'];
        $ketercapaians = ['nilai', 'persentase', 'ketersediaan', 'rasio'];
        $standar = Standar::orderBy('std_nama')->get();

        return view('pages.create-indikatorkinerja', [
            'title' => $title,
            'standar' => $standar,
            'jeniss' => $jeniss,
            'ketercapaians' => $ketercapaians,
            'ik_is_aktifs' => $ik_is_aktifs,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
        ]);
    }

    public function store(Request $request)
    {
        $validationRules = [
            'ik_kode' => 'required|string|max:255',
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'ik_jenis' => 'required|in:IKU,IKT,IKU/IKT',
            'ik_is_aktif' => 'required|in:y,n',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan,rasio',
            'ik_baseline' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $ketercapaian = $request->ik_ketercapaian;
                    $value = strtolower(trim($value));

                    if ($ketercapaian === 'nilai') {
                        if (!is_numeric($value) || $value < 0) {
                            $fail('Nilai baseline untuk "nilai" harus berupa angka >= 0.');
                        }
                    } elseif ($ketercapaian === 'persentase') {
                        if (!is_numeric($value) || $value < 0 || $value > 100) {
                            $fail('Nilai baseline untuk "persentase" harus angka antara 0-100.');
                        }
                    } elseif ($ketercapaian === 'ketersediaan') {
                        if (!in_array($value, ['ada', 'draft'])) {
                            $fail('Baseline untuk "ketersediaan" hanya boleh "ada" atau "draft".');
                        }
                    } elseif ($ketercapaian === 'rasio') {
                        // Contoh validasi rasio: format "1:20"
                        if (!preg_match('/^\d+\s*:\s*\d+$/', $value)) {
                            $fail('Format rasio harus seperti "1:20" atau "1 : 25".');
                        }
                    }
                }
            ],
        ];

        $validatedData = $request->validate($validationRules);

        // Generate ik_id unik
        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);

        // Simpan data
        $indikatorkinerja = new IndikatorKinerja();
        $indikatorkinerja->ik_id = $ik_id;
        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
        $indikatorkinerja->ik_baseline = $request->ik_baseline;
        $indikatorkinerja->ik_is_aktif = $request->ik_is_aktif;
        $indikatorkinerja->ik_ketercapaian = $request->ik_ketercapaian;

        $indikatorkinerja->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('indikatorkinerja.index');
    }

    public function edit(IndikatorKinerja $indikatorkinerja)
    {
        $title = 'Ubah Indikator Kinerja Utama';

        // Eager load relasi untuk mencegah N+1
        $indikatorkinerja->load('baselineTahun');

        // Data referensi
        $standar = Standar::orderBy('std_nama')->get();
        $jeniss = ['IKU','IKT','IKU/IKT'];
        $ketercapaians = ['nilai','persentase','ketersediaan','rasio'];
        $ik_is_aktifs = ['y','n'];

        // Tahun kerja aktif
        $activeTahun = tahun_kerja::where('th_is_aktif', 'y')->first();

        // Validasi jika tidak ada tahun aktif
        if (!$activeTahun) {
            return redirect()->back()->with('error', 'Tahun kerja aktif tidak ditemukan.');
        }

        // Ambil nilai baseline dari relasi berdasarkan tahun aktif
        $baseline_tahun_aktif = optional(
            $indikatorkinerja->baselineTahun->firstWhere('th_id', $activeTahun->th_id)
        )->baseline;

        return view('pages.edit-indikatorkinerja', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
            'jeniss' => $jeniss,
            'ik_is_aktifs' => $ik_is_aktifs,
            'ketercapaians' => $ketercapaians,
            'indikatorkinerja' => $indikatorkinerja,
            'standar' => $standar,
            'baseline_tahun_aktif' => $baseline_tahun_aktif,
            'activeTahun' => $activeTahun,
        ]);
    }

    public function update(IndikatorKinerja $indikatorkinerja, Request $request)
    {
        // dd($request->all()); // Cek apakah data ik_ketercapaian terkirim dengan benar
        $validationRules = [
            'ik_kode' => 'required|string|max:255',
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'ik_jenis' => 'required|in:IKU,IKT,IKU/IKT',
            'ik_baseline' => 'required',
            'ik_is_aktif' => 'required|in:y,n',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan,rasio',
        ];

        $ketercapaian = strtolower($request->ik_ketercapaian);

        // Validasi khusus berdasarkan jenis ketercapaian
        if ($ketercapaian === 'nilai') {
            $validationRules['ik_baseline'] = 'required|numeric|min:0';
        } elseif ($ketercapaian === 'persentase') {
            $validationRules['ik_baseline'] = 'required|numeric|min:0|max:100';
        } elseif ($ketercapaian === 'ketersediaan') {
            $validationRules['ik_baseline'] = ['required', 'in:ada,draft'];
        } elseif ($ketercapaian === 'rasio') {
            $cleaned = preg_replace('/\s*/', '', $request->ik_baseline);
            if (!preg_match('/^\d+:\d+$/', $cleaned)) {
                return back()->withErrors(['ik_baseline' => 'Format rasio harus dalam bentuk angka:angka, misalnya 3:1.'])->withInput();
            }

            [$left, $right] = explode(':', $cleaned);
            if ((int)$left === 0 && (int)$right === 0) {
                return back()->withErrors(['ik_baseline' => 'Rasio tidak boleh 0:0.'])->withInput();
            }

            // Format konsisten
            $request->merge(['ik_baseline' => $left . ' : ' . $right]);
        }

        // Validasi input
        $request->validate($validationRules, [
            'ik_baseline.regex' => 'Format rasio harus dalam bentuk angka : angka (contoh: 3 : 1)',
            'ik_baseline.in' => 'Untuk jenis ketersediaan, hanya boleh diisi "ada" atau "draft".',
        ]);

        // Update data utama
        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
        $indikatorkinerja->ik_ketercapaian = $ketercapaian;
        $indikatorkinerja->ik_is_aktif = $request->ik_is_aktif;
        // $indikatorkinerja->ik_is_aktif = $request->ik_is_aktif;
        // $indikatorkinerja->ik_ketercapaian = $request->ik_ketercapaian;
        // $indikatorkinerja->ik_ketercapaian = $ketercapaian;
        
        $indikatorkinerja->save();

        // Simpan baseline ke relasi tahun
        $activeTahun = tahun_kerja::where('th_is_aktif', 'y')->first();
        if ($activeTahun) {
            $indikatorkinerja->baselineTahun()->updateOrCreate(
                ['th_id' => $activeTahun->th_id],
                ['baseline' => strtolower($request->ik_baseline)]
            );
        }

        Alert::success('Sukses', 'Data Berhasil Diubah') ;
        return redirect()->route('indikatorkinerja.index') ;
    }


    public function destroy(IndikatorKinerja $indikatorkinerja)
    {
        $indikatorkinerja->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('indikatorkinerja.index');
    }
}

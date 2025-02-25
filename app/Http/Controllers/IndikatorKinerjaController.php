<?php

namespace App\Http\Controllers;

use App\Exports\IndikatorKinerjaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IndikatorKinerja;
use App\Models\Standar;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndikatorKinerjaImport;
use Illuminate\Support\Str;


class IndikatorKinerjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Data Indikator Kinerja Utama';
        $q = $request->query('q');

        $query = IndikatorKinerja::where('ik_nama', 'like', '%'. $q. '%')
            ->orderBy('ik_kode', 'asc')
            ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id');

        if ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%');
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
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Tambah Indikator Kinerja Utama';
        $jeniss = ['IKU', 'IKT'];
        $ik_is_aktifs = ['y', 'n'];
        $ketercapaians = ['nilai', 'persentase', 'ketersediaan'];
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
            'ik_jenis' => 'required|in:IKU,IKT',
            'ik_baseline' => 'required',
            'ik_is_aktif' => 'required|in:y,n',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan',
        ];

        if ($request) {
            if ($request->ik_ketercapaian == 'nilai') {
                $validationRules['ik_baseline'] = 'required|numeric|min:0';
            } elseif ($request->ik_ketercapaian == 'persentase') {
                $validationRules['ik_baseline'] = 'required|numeric|min:0|max:100';
            } elseif ($request->ik_ketercapaian == 'ketersediaan') {
                $validationRules['ik_baseline'] = 'required|string';
            }
        }

        $request->validate($validationRules);

        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);

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
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $title = 'Ubah Indikator Kinerja Utama';
        $standar = Standar::orderBy('std_nama')->get();
        $jeniss = ['IKU', 'IKT'];
        $ketercapaians = ['nilai', 'persentase', 'ketersediaan'];
        $ik_is_aktifs = ['y', 'n'];
        
        return view('pages.edit-indikatorkinerja', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
            'jeniss' => $jeniss,
            'ik_is_aktifs' => $ik_is_aktifs,
            'ketercapaians' => $ketercapaians,
            'indikatorkinerja' => $indikatorkinerja,
            'standar' => $standar,
        ]);
    }

    public function update(IndikatorKinerja $indikatorkinerja, Request $request)
    {
        $validationRules = [
            'ik_kode' => 'required|string|max:255',
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'ik_jenis' => 'required|in:IKU,IKT',
            'ik_baseline' => 'required',
            'ik_is_aktif' => 'required|in:y,n',

            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan',
        ];

        // if ($request->ik_is_aktif == 'y') {
        //     IndikatorKinerja::where('ik_is_aktif', 'y')
        //         ->where('ik_id', '!=', $indikatorkinerja->ik_id)
        //         ->update(['ik_is_aktif' => 'n']);
        // }


        if ($request) {
            if ($request->ik_ketercapaian == 'nilai') {
                $validationRules['ik_baseline'] = 'required|numeric|min:0';
            } elseif ($request->ik_ketercapaian == 'persentase') {
                $validationRules['ik_baseline'] = 'required|numeric|min:0|max:100';
            } elseif ($request->ik_ketercapaian == 'ketersediaan') {
                $validationRules['ik_baseline'] = 'required|string';
            }
        }

        $request->validate($validationRules);

        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
        $indikatorkinerja->ik_baseline = $request->ik_baseline;
        $indikatorkinerja->ik_is_aktif = $request->ik_is_aktif;
        $indikatorkinerja->ik_ketercapaian = $request->ik_ketercapaian;

        $indikatorkinerja->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('indikatorkinerja.index');
    }

    public function destroy(IndikatorKinerja $indikatorkinerja)
    {
        $indikatorkinerja->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('indikatorkinerja.index');
    }
}

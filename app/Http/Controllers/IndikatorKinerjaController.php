<?php

namespace App\Http\Controllers;

use App\Exports\IndikatorKinerjaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IndikatorKinerja;
use App\Models\Standar;
use App\Models\tahun_kerja;
use App\Models\UnitKerja;
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

        $query = IndikatorKinerja::select('indikator_kinerja.*', 'standar.std_nama')
            ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
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
                $stdNama = trim($row[2]); 
                $standar = \App\Models\Standar::where('std_nama', $stdNama)->first();

                if (!$standar) {
                    return back()->with('error', "$stdNama tidak ditemukan.");
                }

                IndikatorKinerja::create([
                    'ik_id' => Str::uuid()->toString(),
                    'ik_kode' => trim($row[0]), 
                    'ik_nama' => trim($row[1]), 
                    'std_id' => $standar->std_id,
                    'ik_jenis' => trim($row[3]),
                    'ik_ketercapaian' => trim($row[4]),
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
        $standar = Standar::orderBy('std_nama')->get();
        $jeniss = ['IKU','IKT','IKU/IKT'];
        $ketercapaians = ['nilai','persentase','ketersediaan','rasio'];
        $ik_is_aktifs = ['y','n'];
        $unitKerjas = UnitKerja::all();

        return view('pages.create-indikatorkinerja', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
            'jeniss' => $jeniss,
            'ketercapaians' => $ketercapaians,
            'ik_is_aktifs' => $ik_is_aktifs,
            'standar' => $standar,
            'unitKerjas' => $unitKerjas
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
            'unit_id' => 'required|array',
            'unit_id.*' => 'exists:unit_kerja,unit_id',
        ];

        $validatedData = $request->validate($validationRules);

        $ik_id = 'IK' . strtoupper(md5(uniqid()));

        $indikator = IndikatorKinerja::create([
            'ik_id' => $ik_id,
            'ik_kode' => $validatedData['ik_kode'],
            'ik_nama' => $validatedData['ik_nama'],
            'std_id' => $validatedData['std_id'],
            'ik_jenis' => $validatedData['ik_jenis'],
            'ik_is_aktif' => $validatedData['ik_is_aktif'],
            'ik_ketercapaian' => strtolower($validatedData['ik_ketercapaian']),
        ]);

        // Simpan relasi ke tabel pivot
        $indikator->unitKerja()->sync($validatedData['unit_id']);

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('indikatorkinerja.index');
    }

    public function edit(IndikatorKinerja $indikatorkinerja)
    {
        $title = 'Ubah Indikator Kinerja Utama';

        // Muat relasi unitKerja
        $indikatorkinerja->load('unitKerja');

        // Debug untuk melihat data unitKerja yang dimuat
        // dd($indikatorkinerja->unitKerja);  // Tambahkan debug ini untuk memastikan unitKerja sudah dimuat

        $standar        = Standar::orderBy('std_nama')->get();
        $unitKerjas     = UnitKerja::orderBy('unit_nama')->get();
        $jeniss         = ['IKU', 'IKT', 'IKU/IKT'];
        $ketercapaians  = ['nilai', 'persentase', 'ketersediaan', 'rasio'];
        $ik_is_aktifs   = ['y', 'n'];

        return view('pages.edit-indikatorkinerja', [
            'title'                 => $title,
            'type_menu'             => 'masterdata',
            'sub_menu'              => 'indikatorkinerja',
            'indikatorkinerja'      => $indikatorkinerja,
            'standar'               => $standar,
            'unitKerjas'            => $unitKerjas,
            'jeniss'                => $jeniss,
            'ik_is_aktifs'          => $ik_is_aktifs,
            'ketercapaians'         => $ketercapaians,
        ]);
    }

    public function update(Request $request, IndikatorKinerja $indikatorkinerja)
    {
        $validationRules = [
            'ik_kode'         => 'required|string|max:255',
            'ik_nama'         => 'required|string|max:255',
            'std_id'          => 'required|string',
            'ik_jenis'        => 'required|in:IKU,IKT,IKU/IKT',
            'ik_is_aktif'     => 'required|in:y,n',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan,rasio',
            'unit_id'         => 'required|array',
            'unit_id.*'       => 'exists:unit_kerja,unit_id',
        ];

        $validatedData = $request->validate($validationRules);

        // Update data utama indikator
        $indikatorkinerja->update([
            'ik_kode'         => $validatedData['ik_kode'],
            'ik_nama'         => $validatedData['ik_nama'],
            'std_id'          => $validatedData['std_id'],
            'ik_jenis'        => $validatedData['ik_jenis'],
            'ik_ketercapaian' => strtolower($validatedData['ik_ketercapaian']),
            'ik_is_aktif'     => $validatedData['ik_is_aktif'],
        ]);

        // Sinkronisasi unit kerja (many-to-many)
        $indikatorkinerja->unitKerja()->sync($validatedData['unit_id']);

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

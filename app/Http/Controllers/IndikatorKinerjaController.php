<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\IndikatorKinerja;
use App\Models\Standar;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IndikatorKinerjaController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Indikator Kinerja Utama';
    $q = $request->query('q');

    $query = IndikatorKinerja::where('ik_nama', 'like', '%'. $q. '%')
        ->orderBy('ik_nama', 'asc')
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



public function create()
{
    $title = 'Tambah Indikator Kinerja Utama';
    $jeniss = ['IKU', 'IKT'];
    $ketercapaians = ['nilai', 'persentase', 'ketersediaan'];
    $standar = Standar::orderBy('std_nama')->get();

    return view('pages.create-indikatorkinerja', [
        'title' => $title,
        'standar' => $standar,
        'jeniss' => $jeniss,
        'ketercapaians' => $ketercapaians,
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
        $indikatorkinerja->ik_ketercapaian = $request->ik_ketercapaian;

        $indikatorkinerja->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('indikatorkinerja.index');
    }

    public function edit(IndikatorKinerja $indikatorkinerja)
    {
        $title = 'Ubah Indikator Kinerja Utama';
        $standar = Standar::orderBy('std_nama')->get();
        $jeniss = ['IKU', 'IKT'];
        $ketercapaians = ['nilai', 'persentase', 'ketersediaan'];
        
        return view('pages.edit-indikatorkinerja', [
            'title' => $title,
            'type_menu' => 'masterdata',
            'sub_menu' => 'indikatorkinerja',
            'jeniss' => $jeniss,
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

        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
        $indikatorkinerja->ik_baseline = $request->ik_baseline;
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

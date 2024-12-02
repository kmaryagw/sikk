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
    $tahunId = $request->query('tahun');

    $tahun = tahun_kerja::where('th_is_aktif', 'y')->get();

    $query = IndikatorKinerja::where('ik_nama', 'like', '%'. $q. '%')
        ->orderBy('ik_nama', 'asc')
        ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
        ->leftJoin('tahun_kerja', function($join) {
            $join->on('tahun_kerja.th_id', '=', 'indikator_kinerja.th_id')
                ->where('tahun_kerja.th_is_aktif', 'y');
        });

    if ($q) {
        $query->where('ik_nama', 'like', '%' . $q . '%');
    }

    if ($tahunId) {
        $query->where('tahun_kerja.th_id', $tahunId);
    }

    $indikatorkinerjas = $query->paginate(10)->withQueryString();
    $no = $indikatorkinerjas->firstItem();
    
    return view('pages.index-indikatorkinerja', [
        'title' => $title,
        'indikatorkinerjas' => $indikatorkinerjas,
        'q' => $q,
        'tahun' => $tahun,
        'tahunId' => $tahunId,
        'no' => $no,
        'type_menu' => 'indikatorkinerja',
    ]);
}



public function create()
{
    $title = 'Tambah Indikator Kinerja Utama';
    $jeniss = ['IKU', 'IKT'];
    $ketercapaians = ['nilai', 'persentase', 'ketersediaan'];
    $standar = Standar::orderBy('std_nama')->get();
    $tahunKerja = tahun_kerja::where('th_is_aktif', 'y')->get();

    return view('pages.create-indikatorkinerja', [
        'title' => $title,
        'standar' => $standar,
        'jeniss' => $jeniss,
        'ketercapaians' => $ketercapaians,
        'tahunKerja' => $tahunKerja,
        'type_menu' => 'indikatorkinerja',
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'ik_kode' => 'required|string|max:255',
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'th_id' => 'required',
            'ik_jenis' => 'required|in:IKU,IKT',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan',
        ]);

        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);

        $indikatorkinerja = new IndikatorKinerja();
        $indikatorkinerja->ik_id = $ik_id;
        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->th_id = $request->th_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
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
        $tahunKerja = DB::table('tahun_kerja')->where('th_is_aktif', 'y')->get();
        
        return view('pages.edit-indikatorkinerja', [
            'title' => $title,
            'type_menu' => 'indikatorkinerja',
            'jeniss' => $jeniss,
            'ketercapaians' => $ketercapaians,
            'indikatorkinerja' => $indikatorkinerja,
            'tahunKerja' => $tahunKerja,
            'standar' => $standar,
        ]);
    }

    public function update(IndikatorKinerja $indikatorkinerja, Request $request)
    {
        $request->validate([
            'ik_kode' => 'required|string|max:255',
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'th_id' => 'required',
            'ik_jenis' => 'required|in:IKU,IKT',
            'ik_ketercapaian' => 'required|in:nilai,persentase,ketersediaan',
        ]);

        $indikatorkinerja->ik_kode = $request->ik_kode;
        $indikatorkinerja->ik_nama = $request->ik_nama;
        $indikatorkinerja->std_id = $request->std_id;
        $indikatorkinerja->th_id = $request->th_id;
        $indikatorkinerja->ik_jenis = $request->ik_jenis;
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

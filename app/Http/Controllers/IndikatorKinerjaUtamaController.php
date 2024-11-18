<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\IndikatorKinerjaUtama;
use App\Models\Standar;
use App\Models\tahun_kerja;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IndikatorKinerjaUtamaController extends Controller
{
    public function index(Request $request)
{
    $title = 'Data Indikator Kinerja Utama';
    $q = $request->query('q');
    $tahunId = $request->query('tahun');

    $tahun = tahun_kerja::where('ren_is_aktif', 'y')->get();

    $query = IndikatorKinerjaUtama::where('ik_nama', 'like', '%'. $q. '%')
        ->orderBy('ik_nama', 'asc')
        ->leftJoin('standar', 'standar.std_id', '=', 'indikator_kinerja.std_id')
        ->leftJoin('tahun_kerja', function($join) {
            $join->on('tahun_kerja.th_id', '=', 'indikator_kinerja.th_id')
                ->where('tahun_kerja.ren_is_aktif', 'y');
        });

    if ($q) {
        $query->where('ik_nama', 'like', '%' . $q . '%');
    }

    if ($tahunId) {
        $query->where('tahun_kerja.th_id', $tahunId);
    }

    $indikatorkinerjautamas = $query->paginate(10)->withQueryString();
    $no = $indikatorkinerjautamas->firstItem();
    
    return view('pages.index-indikatorkinerjautama', [
        'title' => $title,
        'indikatorkinerjautamas' => $indikatorkinerjautamas,
        'q' => $q,
        'tahun' => $tahun,
        'tahunId' => $tahunId,
        'no' => $no,
        'type_menu' => 'indikatorkinerjautama',
    ]);
}



public function create()
{
    $title = 'Tambah Indikator Kinerja Utama';
    $standar = Standar::orderBy('std_nama')->get();
    $tahunKerja = tahun_kerja::where('ren_is_aktif', 'y')->get();

    return view('pages.create-indikatorkinerjautama', [
        'title' => $title,
        'standar' => $standar,
        'tahunKerja' => $tahunKerja,
        'type_menu' => 'indikatorkinerjautama',
    ]);
}


    public function store(Request $request)
    {
        $request->validate([
            'ik_nama' => 'required|string|max:255',
            'std_id' => 'required|string',
            'th_id' => 'required',
        ]);

        $customPrefix = 'IK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ik_id = $customPrefix . strtoupper($md5Hash);

        $indikatorkinerjautama = new IndikatorKinerjaUtama();
        $indikatorkinerjautama->ik_id = $ik_id;
        $indikatorkinerjautama->ik_nama = $request->ik_nama;
        $indikatorkinerjautama->std_id = $request->std_id;
        $indikatorkinerjautama->th_id = $request->th_id;

        $indikatorkinerjautama->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('indikatorkinerjautama.index');
    }

    public function edit(IndikatorKinerjaUtama $indikatorkinerjautama)
    {
        $title = 'Ubah Indikator Kinerja Utama';
        $standar = Standar::orderBy('std_nama')->get();
        $tahunKerja = DB::table('tahun_kerja')->where('ren_is_aktif', 'y')->get();
        
        return view('pages.edit-indikatorkinerjautama', [
            'title' => $title,
            'type_menu' => 'indikatorkinerjautama',
            'indikatorkinerjautama' => $indikatorkinerjautama,
            'tahunKerja' => $tahunKerja,
            'standar' => $standar,
        ]);
    }

    public function update(IndikatorKinerjaUtama $indikatorkinerjautama, Request $request)
    {
        $request->validate([
            'ik_nama' => 'required',
            'std_id' => 'required',
            'th_id' => 'required',
        ]);

        $indikatorkinerjautama->ik_nama = $request->ik_nama;
        $indikatorkinerjautama->std_id = $request->std_id;
        $indikatorkinerjautama->th_id = $request->th_id;

        $indikatorkinerjautama->save();

        Alert::success('Sukses', 'Data Berhasil Diubah');

        return redirect()->route('indikatorkinerjautama.index');
    }

    public function destroy(IndikatorKinerjaUtama $indikatorkinerjautama)
    {
        $indikatorkinerjautama->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('indikatorkinerjautama.index');
    }
}

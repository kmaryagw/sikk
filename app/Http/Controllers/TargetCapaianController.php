<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TargetCapaianController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Target Capaian';
        $q = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');

        $tahun = tahun_kerja::where('th_is_aktif', 'y')->get();
        $prodis = program_studi::all();

        $query = target_indikator::where('ti_target', 'like', '%' . $q . '%')
            ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja as aktif_tahun', function($join) {
                $join->on('aktif_tahun.th_id', '=', 'target_indikator.th_id')
                    ->where('aktif_tahun.th_is_aktif', 'y');
            });

        if (Auth::user()->role == 'prodi') {
            $query->where('target_indikator.prodi_id', Auth::user()->prodi_id);
        }

        if ($q) {
            $query->where('ik_nama', 'like', '%' . $q . '%');
        }

        if ($tahunId) {
            $query->where('tahun_kerja.th_id', $tahunId);
        }

        if ($prodiId) {
            $query->where('program_studi.prodi_id', $prodiId);
        }

        $query->orderBy('indikator_kinerja.ik_nama', 'asc');

        $target_capaians = $query->paginate(10)->withQueryString();
        $no = $target_capaians->firstItem();

        return view('pages.index-targetcapaian', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
            'prodis' => $prodis,
            'tahunId' => $tahunId,
            'prodiId' => $prodiId,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'targetcapaian',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Target Capaian';
        
        $indikatorkinerjas = IndikatorKinerja::orderBy('ik_nama')->get();
        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

        return view('pages.create-targetcapaian', [
            'title' => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaian',
        ]);
    }

    public function store(Request $request)
    {
        $indikatorKinerja = IndikatorKinerja::find($request->ik_id);

        $validationRules = [
            'ik_id' => 'required|string',
            'ti_target' => 'required',
            'ti_keterangan' => 'required',
            'prodi_id' => 'required|string',
            'th_id' => 'required|string',
        ];

        if ($indikatorKinerja) {
            if ($indikatorKinerja->ik_ketercapaian == 'nilai') {
                $validationRules['ti_target'] = 'required|numeric|min:0';
            } elseif ($indikatorKinerja->ik_ketercapaian == 'persentase') {
                $validationRules['ti_target'] = 'required|numeric|min:0|max:100';
            } elseif ($indikatorKinerja->ik_ketercapaian == 'ketersediaan') {
                $validationRules['ti_target'] = 'required|string';
            }
        }

        $request->validate($validationRules);

        $customPrefix = 'TC';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $ti_id = $customPrefix . strtoupper($md5Hash);

        $targetcapaian = new target_indikator();
        $targetcapaian->ti_id = $ti_id;
        $targetcapaian->ik_id = $request->ik_id;
        $targetcapaian->ti_target = $request->ti_target;
        $targetcapaian->ti_keterangan = $request->ti_keterangan;
        $targetcapaian->prodi_id = $request->prodi_id;
        $targetcapaian->th_id = $request->th_id;
        $targetcapaian->save();

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('targetcapaian.index');
    }

    public function edit(target_indikator $targetcapaian)
{
    $title = 'Edit Target Capaian';

    $indikatorkinerjautamas = IndikatorKinerja::orderBy('ik_nama')->get();
    $prodis = program_studi::orderBy('nama_prodi')->get();
    $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

    return view('pages.edit-targetcapaian', [
        'title' => $title,
        'targetcapaian' => $targetcapaian,
        'indikatorkinerjautamas' => $indikatorkinerjautamas,
        'prodis' => $prodis,
        'tahuns' => $tahuns,
        'type_menu' => 'targetcapaian',
    ]);
}

public function update(target_indikator $targetcapaian, Request $request)
{
    $indikatorKinerja = IndikatorKinerja::find($request->ik_id);

    $validationRules = [
        'ik_id' => 'required|string',
        'ti_target' => 'required',
        'ti_keterangan' => 'required',
        'prodi_id' => 'required|string',
        'th_id' => 'required|string',
    ];

    if ($indikatorKinerja) {
        if ($indikatorKinerja->ik_ketercapaian == 'nilai') {
            $validationRules['ti_target'] = 'required|numeric|min:0';
        } elseif ($indikatorKinerja->ik_ketercapaian == 'persentase') {
            $validationRules['ti_target'] = 'required|numeric|min:0|max:100';
        } elseif ($indikatorKinerja->ik_ketercapaian == 'ketersediaan') {
            $validationRules['ti_target'] = 'required|string';
        }
    }

    $request->validate($validationRules);

    $targetcapaian->ik_id = $request->ik_id;
    $targetcapaian->ti_target = $request->ti_target;
    $targetcapaian->ti_keterangan = $request->ti_keterangan;
    $targetcapaian->prodi_id = $request->prodi_id;
    $targetcapaian->th_id = $request->th_id;
    $targetcapaian->save();

    Alert::success('Sukses', 'Data Berhasil Diperbarui');

    return redirect()->route('targetcapaian.index');
}

    public function destroy(target_indikator $targetcapaian)
    {
        $targetcapaian->delete();

        Alert::success('Sukses', 'Data Berhasil Dihapus');

        return redirect()->route('targetcapaian.index');
    }
}
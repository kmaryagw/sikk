<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\program_studi;
use App\Models\SettingIKU;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TargetCapaianController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
    }

    public function index(Request $request)
    {
        $title = 'Data Target Capaian';
        $q = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');

        // Ambil semua tahun & prodi
        $tahunList = tahun_kerja::all();
        $prodis = program_studi::all();

        // Ambil tahun aktif
        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();

        // Jika tidak ada filter tahun, gunakan tahun aktif sebagai default
        if (!$tahunId && $tahunAktif) {
            $tahunId = $tahunAktif->th_id;
        }

        // Query data
        $query = target_indikator::query()
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        // Filter untuk role prodi
        if (Auth::user()->role == 'prodi') {
            $query->where('target_indikator.prodi_id', Auth::user()->prodi_id);
            $prodis = program_studi::where('prodi_id', Auth::user()->prodi_id)->get();
        }

        // Filter pencarian
        if ($q) {
            $query->where(function($subQuery) use ($q) {
                $subQuery->where('target_indikator.ti_target', 'like', "%$q%")
                        ->orWhere('indikator_kinerja.ik_nama', 'like', "%$q%")
                        ->orWhere('indikator_kinerja.ik_kode', 'like', "%$q%");
            });
        }

        // Filter berdasarkan tahun aktif atau request
        if ($tahunId) {
            $query->where('target_indikator.th_id', $tahunId);
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
            'tahun' => $tahunList,
            'tahunAktif' => $tahunAktif,
            'tahunId' => $tahunId,
            'prodis' => $prodis,
            'prodiId' => $prodiId,
            'q' => $q,
            'no' => $no,
            'type_menu' => 'targetcapaian',
        ]);
    }

    public function create()
    {
        $title = 'Tambah Target Capaian';
        $indikatorkinerjas = IndikatorKinerja::where('ik_is_aktif','y')->orderBy('ik_nama')->get();

        $baseline = null;
        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

        $loggedInUser = Auth::user();
        $userRole = $loggedInUser->role;
        $userProdi = null;

        if ($userRole === 'prodi') {
            $userProdi = $loggedInUser->programStudi;
        }

        return view('pages.create-targetcapaian', [
            'title' => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'baseline' => $baseline,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaian',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function store(Request $request)
    {
        $indikatorkinerjas = IndikatorKinerja::find($request->ik_id);

        $validationRules = [
            'ik_id' => 'required|string',
            'ti_target' => 'required',
            'ti_keterangan' => 'required',
            'prodi_id' => 'required|string',
            'th_id' => 'required|string',
        ];

        if ($indikatorkinerjas) {
            if ($indikatorkinerjas->ik_ketercapaian == 'nilai') {
                $validationRules['ti_target'] = 'required|numeric|min:0';
            } elseif ($indikatorkinerjas->ik_ketercapaian == 'persentase') {
                $validationRules['ti_target'] = 'required|numeric|min:0|max:100';
            } elseif ($indikatorkinerjas->ik_ketercapaian == 'ketersediaan') {
                $validationRules['ti_target'] = 'required|string';
            } elseif ($indikatorkinerjas->ik_ketercapaian == 'rasio') {
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

    $indikatorkinerjautamas = IndikatorKinerja::where('ik_is_aktif','y')->orderBy('ik_nama')->get();
    $prodis = program_studi::orderBy('nama_prodi')->get();
    $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

        $loggedInUser = Auth::user();
            $userRole = $loggedInUser->role;
            $userProdi = null;

        if ($userRole === 'prodi') {
            $userProdi = $loggedInUser->programStudi;
        }

        $baseline = optional($targetcapaian->indikatorKinerja)->ik_baseline ?? 'Baseline tidak ditemukan';

        return view('pages.edit-targetcapaian', [
            'title' => $title,
            'targetcapaian' => $targetcapaian,
            'indikatorkinerjautamas' => $indikatorkinerjautamas,
            'baseline' => $baseline,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaian',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function update(target_indikator $targetcapaian, Request $request)
    {
        $indikatorKinerjas = IndikatorKinerja::find($request->ik_id);

        $validationRules = [
            'ik_id' => 'required|string',
            'ti_target' => 'required',
            'ti_keterangan' => 'required',
            'prodi_id' => 'required|string',
            'th_id' => 'required|string',
        ];

        if ($indikatorKinerjas) {
            $ketercapaian = strtolower($indikatorKinerjas->ik_ketercapaian);

            if ($ketercapaian === 'nilai') {
                $validationRules['ti_target'] = 'required|numeric|min:0';
            } elseif ($ketercapaian === 'persentase') {
                $validationRules['ti_target'] = 'required|numeric|min:0|max:100';
            } elseif ($ketercapaian === 'ketersediaan') {
                $validationRules['ti_target'] = 'required|in:ada,draft';
            } elseif ($ketercapaian === 'rasio') {
                $validationRules['ti_target'] = [
                    'required',
                    'regex:/^\d+\s*:\s*\d+$/'
                ];
            }
        }

        $customMessages = [
            'ti_target.regex' => 'Format rasio harus dalam bentuk angka : angka (contoh: 3 : 1)',
            'ti_target.in' => 'Untuk jenis ketersediaan, hanya boleh diisi "ada" atau "draft".',
        ];

        $request->validate($validationRules, $customMessages);

        // Normalisasi nilai target untuk rasio (hilangkan spasi, beri format konsisten)
        $ti_target = $request->ti_target;
        if ($indikatorKinerjas && $indikatorKinerjas->ik_ketercapaian === 'rasio') {
            $cleaned = preg_replace('/\s*/', '', $ti_target);
            [$left, $right] = explode(':', $cleaned);
            $ti_target = $left . ' : ' . $right;
        }

        $targetcapaian->ik_id = $request->ik_id;
        $targetcapaian->ti_target = $ti_target;
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
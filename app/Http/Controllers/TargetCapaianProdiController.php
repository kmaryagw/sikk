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
        $title = 'Data Target Capaian';
        $q = $request->query('q');
        $tahunId = $request->query('tahun');
        $prodiId = $request->query('prodi');

        $tahun = tahun_kerja::where('th_is_aktif', 'y')->first();
        $prodis = program_studi::all();

        $query = target_indikator::leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja', 'tahun_kerja.th_id','=','target_indikator.th_id');
        
        //hanya tahun aktif
        $query->where('target_indikator.th_id', $tahun->th_id);

        if (Auth::user()->role == 'prodi') {
            $query->where('target_indikator.prodi_id', Auth::user()->prodi_id);
            $prodis = program_studi::where('prodi_id', Auth::user()->prodi_id)->get();
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
        // dd($target_capaians);
        // $query->dump();
        // dd($query->toSql());
        $no = $target_capaians->firstItem();

        return view('targetcapaian.index', [
            'title' => $title,
            'target_capaians' => $target_capaians,
            'tahun' => $tahun,
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
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->first();

        $title = 'Tambah Target Capaian';
        $indikatorkinerjas = IndikatorKinerja::where('ik_is_aktif','y')
                                ->orderBy('ik_nama')->get();

        //data yang sudah tersimpan
        $targetindikators = target_indikator::where('th_id', $tahuns->th_id)
                            ->where('prodi_id', Auth::user()->prodi_id)
                            ->get();
        
        $baseline = null;
        $prodis = program_studi::orderBy('nama_prodi', 'asc')->get();

        $loggedInUser = Auth::user();
        $userRole = $loggedInUser->role;
        $userProdi = null;

        if ($userRole === 'prodi') {
            $userProdi = $loggedInUser->programStudi;
        }
        //dd($indikatorkinerjas->toArray());

        return view('targetcapaian.create', [
            'title' => $title,
            'indikatorkinerjas' => $indikatorkinerjas,
            'targetindikators' => $targetindikators,
            'baseline' => $baseline,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaianprodi',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function store(Request $request)
    {
        $indikatorkinerjas = IndikatorKinerja::find($request->ik_id);

        $validationRules = [
            'prodi_id' => 'required|string',
            'th_id' => 'required|string',

            'indikator.*.ik_id' => 'required|string',
            'indikator.*.keterangan' => 'nullable',
            'indikator.*.target' => [
                                        'nullable',
                                        function ($attribute, $value, $fail) use ($request) {
                                            $index = explode('.', $attribute)[1]; // Get array index
                                            $type = $request->input("items.$index.type");
                                
                                            if ($type === 'number' && !is_numeric($value)) {
                                                $fail("The $attribute must be a number.");
                                            }
                                        }
                                ],
        ];

        // if ($indikatorkinerjas) {
        //     if ($indikatorkinerjas->ik_ketercapaian == 'nilai') {
        //         $validationRules['indikator.*.target'] = 'required|numeric|min:0';
        //     } elseif ($indikatorkinerjas->ik_ketercapaian == 'persentase') {
        //         $validationRules['indikator.*.target'] = 'required|numeric|min:0|max:100';
        //     } elseif ($indikatorkinerjas->ik_ketercapaian == 'ketersediaan') {
        //         $validationRules['indikator.*.target'] = 'required|string';
        //     }
        // }
        // dd($request->indikator);
        
        $request->validate($validationRules);

        $th_id = $request->th_id;
        $prodi_id = $request->prodi_id;

        collect($request->indikator)->each(function ($data) use ($th_id, $prodi_id) {
            $customPrefix = 'TC';
            $timestamp = time();
            $md5Hash = md5($timestamp.$data["ik_id"]);
            $ti_id = $customPrefix . strtoupper($md5Hash);

            target_indikator::updateOrCreate(
                [
                    'ik_id'=>$data["ik_id"],
                    'th_id'=>$th_id,
                    'prodi_id'=>$prodi_id
                ], 
                [
                    'ti_id'=>$ti_id,
                    'ik_id'=>$data["ik_id"],
                    'ti_target'=>$data["target"],
                    'ti_keterangan'=>$data["keterangan"],
                    'prodi_id'=>$prodi_id,
                    'th_id'=>$th_id
                ]
            );
        });

        Alert::success('Sukses', 'Data Berhasil Ditambah');

        return redirect()->route('targetcapaianprodi.index');
    }

    public function edit($targetcapaian)
    {
        $title = 'Edit Target Capaian';
        //dd($targetcapaian);

        $targetcapaian = target_indikator::find($targetcapaian);

        $indikatorkinerjautamas = IndikatorKinerja::orderBy('ik_nama')->get();
        $prodis = program_studi::orderBy('nama_prodi')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();

        $loggedInUser = Auth::user();
            $userRole = $loggedInUser->role;
            $userProdi = null;

        if ($userRole === 'prodi') {
            $userProdi = $loggedInUser->programStudi;
        }

        $baseline = optional($targetcapaian->indikatorKinerja)->ik_baseline ?? 'Baseline tidak ditemukan';

        return view('targetcapaian.edit', [
            'title' => $title,
            'targetcapaian' => $targetcapaian,
            'indikatorkinerjautamas' => $indikatorkinerjautamas,
            'baseline' => $baseline,
            'prodis' => $prodis,
            'tahuns' => $tahuns,
            'type_menu' => 'targetcapaianprodi',
            'userRole' => $userRole,
            'userProdi' => $userProdi,
        ]);
    }

    public function update($targetcapaian, Request $request)
    {
        $indikatorKinerjas = IndikatorKinerja::find($request->ik_id);
        $targetcapaian = target_indikator::find($targetcapaian);

        $validationRules = [
            'ik_id' => 'required|string',
            'ti_target' => 'required',
            'ti_keterangan' => 'required',
            'prodi_id' => 'required|string',
            'th_id' => 'required|string',
        ];

        if ($indikatorKinerjas) {
            if ($indikatorKinerjas->ik_ketercapaian == 'nilai') {
                $validationRules['ti_target'] = 'required|numeric|min:0';
            } elseif ($indikatorKinerjas->ik_ketercapaian == 'persentase') {
                $validationRules['ti_target'] = 'required|numeric|min:0|max:100';
            } elseif ($indikatorKinerjas->ik_ketercapaian == 'ketersediaan') {
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

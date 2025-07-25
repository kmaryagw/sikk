<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use App\Models\program_studi;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use App\Models\UnitKerja;
use App\Models\standar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProgramKerjaController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->role !== 'prodi' && Auth::user()->role !== 'unit kerja') {
            abort(403, 'Unauthorized access');
        }
    }
    
    public function index(Request $request)
    {
        $title = 'Data Program Kerja';
        $q = $request->query('q');
        $unit_id = $request->query('unit_id');
        $tahunId = $request->query('tahun');

        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();
        $standars = standar::orderBy('std_id', 'asc')->get();

        $query = RencanaKerja::with(['targetindikators.indikatorKinerja', 'periodes','standar'])
            ->leftJoin('unit_kerja', 'unit_kerja.unit_id', '=', 'rencana_kerja.unit_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'rencana_kerja.th_id')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->orderBy('rencana_kerja.rk_nama', 'asc');

        if (Auth::user()->role == 'unit kerja') {
            $query->where('rencana_kerja.unit_id', Auth::user()->unit_id);
        }

        if (Auth::user()->role == 'prodi') {
            $query->join('rencana_kerja_program_studi', 'rencana_kerja.rk_id', '=', 'rencana_kerja_program_studi.rk_id')
                ->where('rencana_kerja_program_studi.prodi_id', Auth::user()->prodi_id);
        }

        // Filter pencarian (rk_nama, indikator_kinerja)
        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('rencana_kerja.rk_nama', 'like', '%' . $q . '%')
                        ->orWhereHas('targetindikators.indikatorKinerja', function ($query) use ($q) {
                            $query->where('ik_kode', 'like', '%' . $q . '%')
                                  ->orWhere('ik_nama', 'like', '%' . $q . '%');
                        });
            });
        }

        if ($unit_id) {
            $query->where('rencana_kerja.unit_id', $unit_id);
        }

        if ($tahunId) {
            $query->where('rencana_kerja.th_id', $tahunId);
        }

        $programkerjas = $query->paginate(10)->withQueryString();
        $no = $programkerjas->firstItem();

        return view('pages.index-programkerja', [
            'title' => $title,
            'programkerjas' => $programkerjas,
            'standars' => $standars,
            'units' => $units,
            'tahuns' => $tahuns,
            'periodes' => $periodes,
            'q' => $q,
            'unit_id' => $unit_id,
            'tahun' => $tahunId,
            'no' => $no,
            'type_menu' => 'programkerja',
        ]);
    }


    public function create()
    {
        $title = 'Tambah Program Kerja';
        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();
        $programStudis = program_studi::whereHas('targetIndikator')
            ->orderBy('nama_prodi', 'asc')
            ->get();

        $targetindikators = target_indikator::with('indikatorKinerja')
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->orderBy('indikator_kinerja.ik_nama', 'asc')
            ->get(['target_indikator.ti_id', 'indikator_kinerja.ik_kode', 'indikator_kinerja.ik_nama']);
        
        $standars = standar::orderBy('std_id', 'asc')->get();

        $loggedInUser = Auth::user();
        $userRole = $loggedInUser->role;
        $userUnit = null;
    
        if ($userRole === 'unit kerja') {
            $userUnit = $loggedInUser->unitKerja;
        }


        return view('pages.create-programkerja', [
            'title' => $title,
            'units' => $units,
            'tahuns' => $tahuns,
            'periodes' => $periodes,
            'standars' => $standars,
            'targetindikators' => $targetindikators,
            'programStudis' => $programStudis,
            'type_menu' => 'programkerja',
            'userRole' => $userRole,
            'userUnit' => $userUnit,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'rk_nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_kerja,unit_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'std_id' => 'required|exists:standar,std_id',
            'anggaran' => 'nullable|numeric|min:0',
            'pm_id' => 'array',
            'ti_id' => 'array',
            'prodi_id' => 'required|array',
            'prodi_id' => 'exists:target_indikator,prodi_id',
        ]);

        $unitAktif = UnitKerja::where('unit_id', $request->unit_id)->where('unit_kerja', 'y')->exists();
        $tahunAktif = tahun_kerja::where('th_id', $request->th_id)->where('th_is_aktif', 'y')->exists();

        if (!$unitAktif || !$tahunAktif) {
            Alert::error('Gagal', 'Unit kerja atau tahun kerja tidak aktif.');
            return redirect()->back()->withInput();
        }

        $customPrefix = 'RK';
        $timestamp = time();
        $md5Hash = md5($timestamp);
        $rk_id = $customPrefix . strtoupper($md5Hash);

        $programkerja = new RencanaKerja();
        $programkerja->rk_id = $rk_id;
        $programkerja->rk_nama = $request->rk_nama;
        $programkerja->unit_id = $request->unit_id;
        $programkerja->std_id = $request->std_id;
        $programkerja->th_id = $request->th_id;
        $programkerja->anggaran = $request->anggaran;
        $programkerja->save();

        if ($request->has('pm_id')) {
            $pm_ids = $request->pm_id;
        
            $dataToInsert = [];
            foreach ($pm_ids as $pm_id) {
                $dataToInsert[] = [
                    'rkp_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'pm_id' => $pm_id,
                ];
            }
            DB::table('rencana_kerja_pelaksanaan')->insert($dataToInsert);
        }

        if ($request->has('prodi_id') && count($request->prodi_id) > 0) {
            $dataToInsert = collect($request->prodi_id)->map(function ($prodi_id) use ($programkerja) {
                return [
                    'rkps_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'prodi_id' => $prodi_id,
                ];
            })->toArray();
    
            DB::table('rencana_kerja_program_studi')->insert($dataToInsert);
        }

        if ($request->has('ti_id')) {
            $ti_ids = $request->ti_id;
        
            $dataToInsert = [];
            foreach ($ti_ids as $ti_id) {
                $dataToInsert[] = [
                    'rkti_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'ti_id' => $ti_id,
                ];
            }
            DB::table('rencana_kerja_target_indikator')->insert($dataToInsert);
        }
        

        Alert::success('Sukses', 'Data Berhasil Ditambah');
        return redirect()->route('programkerja.index');
    }

    public function edit(RencanaKerja $programkerja)
    {
        // dd($programkerja->all());

        $title = 'Ubah Program Kerja';
        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama', 'asc')->get();
        $standars = standar::orderBy('std_id', 'asc')->get();
        
        $programStudis = program_studi::whereHas('targetIndikator')
            ->orderBy('nama_prodi', 'asc')
            ->get();

        $targetindikators = target_indikator::with('indikatorKinerja')
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->orderBy('indikator_kinerja.ik_nama', 'asc')
            ->get([
                'target_indikator.ti_id', 
                'indikator_kinerja.ik_kode', 
                'indikator_kinerja.ik_nama'
            ]);

        // Mengambil data yang sudah dipilih
        $selectedStandar = $programkerja->std_id;
        $selectedPeriodes = $programkerja->periodes->pluck('pm_id')->toArray();
        $selectedIndikators = $programkerja->targetindikators->pluck('ti_id')->toArray();
        $selectedProgramStudis = $programkerja->programStudis->pluck('prodi_id')->toArray();

        $loggedInUser = Auth::user();
        $userRole = $loggedInUser->role;
        $userUnit = null;
    
        if ($userRole === 'unit kerja') {
            $userUnit = $loggedInUser->unitKerja;
        }

        return view('pages.edit-programkerja', [
            'title' => $title,
            'programkerja' => $programkerja,
            'units' => $units,
            'tahuns' => $tahuns,
            'periodes' => $periodes,
            'standars' => $standars,
            'selectedStandar' => $selectedStandar,
            'selectedPeriodes' => $selectedPeriodes,
            'selectedIndikators' => $selectedIndikators,
            'targetindikators' => $targetindikators,
            'programStudis' => $programStudis,
            'selectedProgramStudis' => $selectedProgramStudis,
            'userRole' => Auth::user()->role,
            'userUnit' => Auth::user()->unitKerja ?? null,
            'type_menu' => 'programkerja',
        ]);
    }


    public function update(RencanaKerja $programkerja, Request $request)
    {
        $request->validate([
            'rk_nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_kerja,unit_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'std_id' => 'required|exists:standar,std_id',
            'anggaran' => 'nullable|numeric|min:0',
            'pm_id' => 'array',
            'ti_id' => 'array',
            'prodi_id' => 'array',
        ]);

        $unitAktif = UnitKerja::where('unit_id', $request->unit_id)->where('unit_kerja', 'y')->exists();
        $tahunAktif = tahun_kerja::where('th_id', $request->th_id)->where('th_is_aktif', 'y')->exists();

        if (!$unitAktif || !$tahunAktif) {
            Alert::error('Gagal', 'Unit kerja atau tahun kerja tidak aktif.');
            return redirect()->back()->withInput();
        }

        $programkerja->rk_nama = $request->rk_nama;
        $programkerja->unit_id = $request->unit_id;
        $programkerja->std_id = $request->std_id;
        $programkerja->anggaran = $request->anggaran;
        $programkerja->th_id = $request->th_id;
        $programkerja->save();

        DB::table('rencana_kerja_pelaksanaan')
            ->where('rk_id', $programkerja->rk_id)
            ->delete();

        if ($request->has('pm_id')) {
            $pm_ids = $request->pm_id;

            $dataToInsert = [];
            foreach ($pm_ids as $pm_id) {
                $dataToInsert[] = [
                    'rkp_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'pm_id' => $pm_id,
                ];
            }
            DB::table('rencana_kerja_pelaksanaan')->insert($dataToInsert);
        }

        DB::table('rencana_kerja_target_indikator')
            ->where('rk_id', $programkerja->rk_id)
            ->delete();

        if ($request->has('ti_id')) {
            $ti_ids = $request->ti_id;

            $dataToInsert = [];
            foreach ($ti_ids as $ti_id) {
                $dataToInsert[] = [
                    'rkti_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'ti_id' => $ti_id,
                ];
            }
            DB::table('rencana_kerja_target_indikator')->insert($dataToInsert);
        }

        DB::table('rencana_kerja_program_studi')
            ->where('rk_id', $programkerja->rk_id)
            ->delete();

        if ($request->has('prodi_id')) {
            $prodi_ids = $request->prodi_id;

            $dataToInsert = [];
            foreach ($prodi_ids as $prodi_id) {
                $dataToInsert[] = [
                    'rkps_id' => Str::uuid()->toString(),
                    'rk_id' => $programkerja->rk_id,
                    'prodi_id' => $prodi_id,
                ];
            }
            DB::table('rencana_kerja_program_studi')->insert($dataToInsert);
        }

        Alert::success('Sukses', 'Data Berhasil Diubah');
        return redirect()->route('programkerja.index');
    }



    public function destroy(RencanaKerja $programkerja)
    {
        $programkerja->delete();
        Alert::success('Sukses', 'Data Berhasil Dihapus');
        return redirect()->route('programkerja.index');
    }
}
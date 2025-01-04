<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProgramKerjaController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Data Program Kerja';
        $q = $request->query('q');
        $unit_id = $request->query('unit_id');
        $tahunId = $request->query('tahun');

        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama')->get();

        $query = RencanaKerja::with(['targetindikators.indikatorKinerja', 'periodes'])
            ->where('rk_nama', 'like', '%' . $q . '%')
            ->orderBy('rk_nama', 'asc')
            ->leftJoin('unit_kerja', function ($join) {
                $join->on('unit_kerja.unit_id', '=', 'rencana_kerja.unit_id')
                    ->where('unit_kerja.unit_kerja', 'y');
            })
            ->leftJoin('tahun_kerja', function ($join) {
                $join->on('tahun_kerja.th_id', '=', 'rencana_kerja.th_id');
            });

        $query->where(function ($query) {
            $query->where('tahun_kerja.th_is_aktif', 'y');
        });

        if (Auth::user()->role == 'unit kerja') {
            $query->where('rencana_kerja.unit_id', Auth::user()->unit_id);
        }

        if ($q) {
            $query->where('rk_nama', 'like', '%' . $q . '%');
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
        $periodes = periode_monev::orderBy('pm_nama')->get();

        // $targetindikators = target_indikator::with('indikatorKinerja')
        //     ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
        //     ->orderBy('indikator_kinerja.ik_nama', 'asc')
        //     ->pluck('indikator_kinerja.ik_nama', 'target_indikator.ti_id');

        $targetindikators = target_indikator::with('indikatorKinerja')
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->orderBy('indikator_kinerja.ik_nama', 'asc')
            ->get(['target_indikator.ti_id', 'indikator_kinerja.ik_kode', 'indikator_kinerja.ik_nama']);


        return view('pages.create-programkerja', [
            'title' => $title,
            'units' => $units,
            'tahuns' => $tahuns,
            'periodes' => $periodes,
            'targetindikators' => $targetindikators,
            'type_menu' => 'programkerja',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rk_nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_kerja,unit_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'array',
            'ti_id' => 'array',
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
        $programkerja->th_id = $request->th_id;
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
        $title = 'Ubah Program Kerja';
        $units = UnitKerja::where('unit_kerja', 'y')->get();
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama')->get();
        // $targetindikators = target_indikator::with('indikatorKinerja')
        //     ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
        //     ->orderBy('indikator_kinerja.ik_nama', 'asc')
        //     ->pluck('indikator_kinerja.ik_nama', 'target_indikator.ti_id');

        $targetindikators = target_indikator::with('indikatorKinerja')
            ->join('indikator_kinerja', 'target_indikator.ik_id', '=', 'indikator_kinerja.ik_id')
            ->orderBy('indikator_kinerja.ik_nama', 'asc')
            ->get(['target_indikator.ti_id', 'indikator_kinerja.ik_kode', 'indikator_kinerja.ik_nama']);

        $selectedPeriodes = $programkerja->periodes->pluck('pm_id')->toArray();
        $selectedIndikators = $programkerja->targetindikators()->pluck('target_indikator.ti_id')->toArray();

        return view('pages.edit-programkerja', [
            'title' => $title,
            'programkerja' => $programkerja,
            'units' => $units,
            'tahuns' => $tahuns,
            'periodes' => $periodes,
            'selectedPeriodes' => $selectedPeriodes,
            'selectedIndikators' => $selectedIndikators,
            'targetindikators' => $targetindikators,
            'type_menu' => 'programkerja',
        ]);
    }

    public function update(RencanaKerja $programkerja, Request $request)
    {
        $request->validate([
            'rk_nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_kerja,unit_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'array',
            'ti_id' => 'array',
        ]);

        $unitAktif = UnitKerja::where('unit_id', $request->unit_id)->where('unit_kerja', 'y')->exists();
        $tahunAktif = tahun_kerja::where('th_id', $request->th_id)->where('th_is_aktif', 'y')->exists();

        if (!$unitAktif || !$tahunAktif) {
            Alert::error('Gagal', 'Unit kerja atau tahun kerja tidak aktif.');
            return redirect()->back()->withInput();
        }

        $programkerja->rk_nama = $request->rk_nama;
        $programkerja->unit_id = $request->unit_id;
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
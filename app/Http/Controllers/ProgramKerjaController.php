<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\periode_monev;
use App\Models\RencanaKerja;
use App\Models\RencanaKerjaPelaksanaan;
use App\Models\RencanaKerjaTargetIndikator;
use App\Models\tahun_kerja;
use App\Models\target_indikator;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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
        $targetindikators = target_indikator::with('indikatorKinerja') // Eager load indikatorKinerja
            ->orderBy('ti_id') // Sesuaikan dengan kolom yang Anda inginkan untuk sorting
            ->get();
        $query = RencanaKerja::where('rk_nama', 'like', '%' . $q . '%')
            ->orderBy('rk_nama', 'asc')
            ->leftJoin('unit_kerja', function($join) {
                $join->on('unit_kerja.unit_id', '=', 'rencana_kerja.unit_id')
                    ->where('unit_kerja.unit_kerja', 'y');
            })
            ->leftJoin('tahun_kerja', function($join) {
                $join->on('tahun_kerja.th_id', '=', 'rencana_kerja.th_id')
                    ->where('tahun_kerja.th_is_aktif', 'y');
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
            'targetindikators' => $targetindikators,
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
        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->get();
        $periodes = periode_monev::orderBy('pm_nama')->get();
        $indikatorKinerja = IndikatorKinerja::all();

        return view('pages.create-programkerja', [
            'title' => $title,
            'units' => $units,
            'tahunAktif' => $tahunAktif,
            'periodes' => $periodes,
            'type_menu' => 'programkerja',
            'indikatorKinerja' => $indikatorKinerja,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'rk_nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_kerja,unit_id',
            'th_id' => 'required|exists:tahun_kerja,th_id',
            'pm_id' => 'required|array',
            'pm_id.*' => 'exists:periode_monev,pm_id',
            'ik_id' => 'required|array',
            'ik_id.*' => 'exists:indikator_kinerja,ik_id',
        ]);

        // Cek apakah unit dan tahun aktif
        $unitAktif = UnitKerja::where('unit_id', $request->unit_id)
            ->where('unit_kerja', 'y')
            ->exists();

        $tahunAktif = tahun_kerja::where('th_id', $request->th_id)
            ->where('th_is_aktif', 'y')
            ->exists();

        if (!$unitAktif) {
            return back()->withErrors(['unit_id' => 'Unit kerja tidak aktif.']);
        }

        if (!$tahunAktif) {
            return back()->withErrors(['th_id' => 'Tahun tidak aktif.']);
        }

        // Simpan Program Kerja
        $programkerja = RencanaKerja::create([
            'rk_nama' => $request->rk_nama,
            'unit_id' => $request->unit_id,
            'th_id' => $request->th_id,
        ]);

        // Menyimpan indikator kinerja utama
        $programkerja->indikatorKinerja()->sync($request->ik_id);

        // Menyimpan periode monev
        if ($request->has('pm_id')) {
            $programkerja->periodes()->sync($request->pm_id);
        }

        Alert::success('Sukses', 'Program kerja berhasil disimpan.');

        return redirect()->route('programkerja.index');
    }
}

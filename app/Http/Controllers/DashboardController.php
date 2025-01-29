<?php

namespace App\Http\Controllers;

use App\Models\periode_monev;
use App\Models\program_studi;
use App\Models\RencanaKerja;
use App\Models\tahun_kerja;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){
        
        $title = 'Dashboard';

        $user = Auth::user();

        //TAHUN
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        
        // IKU
        if ($user->role === 'prodi') {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
            }])
            ->where('prodi_id', $user->prodi_id)
            ->get();
        } else {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
            }])
            ->orderBy('nama_prodi')->get();
        }

        // IKT
        if ($user->role === 'prodi') {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
            }])
            ->where('prodi_id', $user->prodi_id)
            ->get();
        } else {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
            }])
            ->orderBy('nama_prodi')->get();
        }

        //RENJA
        $totalrenja = RencanaKerja::count();

        $unitKerjarenja = UnitKerja::withCount(['rencanaKerja'])
            ->orderBy('unit_nama', 'asc')
            ->get();

        //PERIODE MONITORING RENJA
        $periodemonevrenja = periode_monev::withCount(['rencanaKerjas'])
            ->orderBy('pm_nama', 'asc')
            ->get();
        
        // REALISASI
        $realisasi = UnitKerja::with(['rencanaKerja', 'rencanaKerja.realisasi'])
            ->get()
            ->map(function ($unit) {
                $unit->jumlah_renja = $unit->rencanaKerja->count();
                $unit->jumlah_realisasi = $unit->rencanaKerja->reduce(function ($carry, $renja) {
                    return $carry + $renja->realisasi->count();
                }, 0);
                return $unit;
            });

        return view('pages.dashboard', [
            'title' => $title,
            'tahuns' => $tahuns,
            'jumlahiku' => $jumlahiku,
            'jumlahikt' => $jumlahikt,
            'totalrenja' => $totalrenja,
            'unitKerjarenja' => $unitKerjarenja,
            'periodemonevrenja' => $periodemonevrenja,
            'realisasi' => $realisasi,
            'type_menu' => 'dashboard',
        ]);
    }
}

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
        } elseif ($user->role === 'fakultas') {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
            }])
            ->where('id_fakultas', $user->id_fakultas)
            ->orderBy('nama_prodi')
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
        } elseif ($user->role === 'fakultas') {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
            }])
            ->where('id_fakultas', $user->id_fakultas)
            ->orderBy('nama_prodi')
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

        if ($user->role === 'unit kerja') {
            $unitKerjarenja = UnitKerja::withCount(['rencanaKerja'])
                ->orderBy('unit_nama', 'asc')
                ->where('unit_id', $user->unit_id)
                ->get();
        } else {
            $unitKerjarenja = UnitKerja::withCount(['rencanaKerja'])
            ->orderBy('unit_nama', 'asc')
            ->get();
        }

        //PERIODE MONITORING RENJA
        $periodemonevrenja = periode_monev::withCount(['rencanaKerjas'])
            ->orderBy('pm_nama', 'asc')
            ->get();
        
        // REALISASI
        // $realisasi = UnitKerja::with(['rencanaKerja', 'rencanaKerja.realisasi'])
        //     ->get()
        //     ->map(function ($unit) {
        //         $unit->jumlah_renja = $unit->rencanaKerja->count();
        //         $unit->jumlah_realisasi = $unit->rencanaKerja->reduce(function ($carry, $renja) {
        //             return $carry + $renja->realisasi->count();
        //         }, 0);
        //         return $unit;
        //     });
        if ($user->role === 'unit kerja') {
            $realisasi = UnitKerja::where('unit_id', $user->unit_id)
                ->with(['rencanaKerja', 'rencanaKerja.realisasi'])
                ->get()
                ->map(function ($unit) {
                    $unit->jumlah_renja = $unit->rencanaKerja->count();
                    $unit->jumlah_realisasi = $unit->rencanaKerja->reduce(function ($carry, $renja) {
                        return $carry + $renja->realisasi->count();
                    }, 0);
                    return $unit;
                });
        } else {
            // Jika role adalah admin atau lainnya, tampilkan semua unit kerja
            $realisasi = UnitKerja::with(['rencanaKerja', 'rencanaKerja.realisasi'])
                ->get()
                ->map(function ($unit) {
                    $unit->jumlah_renja = $unit->rencanaKerja->count();
                    $unit->jumlah_realisasi = $unit->rencanaKerja->reduce(function ($carry, $renja) {
                        return $carry + $renja->realisasi->count();
                    }, 0);
                    return $unit;
                });
        }
        

        //Monitoring
        $renjaPerPeriode = periode_monev::with(['rencanaKerjas' => function ($query) {
            $query->select('pm_id', 'rencana_kerja.rk_id')
                ->withCount([
                    'monitoring as tercapai' => function ($query) {
                        $query->where('mtg_status', 'y');
                    },
                    'monitoring as belum_tercapai' => function ($query) {
                        $query->where('mtg_status', 'n');
                    },
                    'monitoring as tidak_terlaksana' => function ($query) {
                        $query->where('mtg_status', 't');
                    },
                    'monitoring as perlu_tindak_lanjut' => function ($query) {
                        $query->where('mtg_status', 'p');
                    }
                ]);
        }])
        ->orderBy('pm_nama', 'asc')
        ->get();

        return view('pages.dashboard', [
            'title' => $title,
            'tahuns' => $tahuns,
            'jumlahiku' => $jumlahiku,
            'jumlahikt' => $jumlahikt,
            'totalrenja' => $totalrenja,
            'unitKerjarenja' => $unitKerjarenja,
            'periodemonevrenja' => $periodemonevrenja,
            'realisasi' => $realisasi,
            'renjaPerPeriode' => $renjaPerPeriode,
            'type_menu' => 'dashboard',
        ]);
    }
}

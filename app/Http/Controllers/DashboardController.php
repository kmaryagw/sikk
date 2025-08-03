<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\tahun_kerja;
use App\Models\RencanaKerja;
use App\Models\SuratNomor;
use Illuminate\Http\Request;
use App\Models\periode_monev;
use App\Models\program_studi;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(){
        
        $title = 'Dashboard';

        $user = Auth::user();

        //TAHUN
        $tahuns = tahun_kerja::where('th_is_aktif', 'y')->get();
        $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
        
        // IKU
        $totaliku = IndikatorKinerja::where('ik_jenis', 'IKU')->count();
        if ($user->role === 'prodi') {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
            }])
            ->where('prodi_id', $user->prodi_id)
            ->get();
        } elseif ($user->role === 'fakultas') {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
            }])
            ->where('id_fakultas', $user->id_fakultas)
            ->orderBy('nama_prodi')
            ->get();
        } else {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
            }])
            ->orderBy('nama_prodi')
            ->get();
        }
        
        // Untuk Chart
        $labelsIKU = $jumlahiku->pluck('nama_prodi');
        $dataIKU = $jumlahiku->pluck('target_indikator_count');
        
        // IKT
        $totalikt = IndikatorKinerja::where('ik_jenis', 'IKT')->count();
        if ($user->role === 'prodi') {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
            }])
            ->where('prodi_id', $user->prodi_id)
            ->get();
        } elseif ($user->role === 'fakultas') {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
            }])
            ->where('id_fakultas', $user->id_fakultas)
            ->orderBy('nama_prodi')
            ->get();
        } else {
            $jumlahikt = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKT');
                });
                
                if ($tahunAktif) {
                    $query->where('th_id', $tahunAktif->th_id);
                }
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
            $realisasi = UnitKerja::with(['rencanaKerja', 'rencanaKerja.realisasi'])
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
        
        //surat
        // Ambil data SuratNomor dengan pagination
        $suratNomors = SuratNomor::paginate(10);

        // Ambil data Organisasi Jabatan dan hitung jumlah surat, revisi, dan valid per organisasi
        $suratSummary = SuratNomor::select(
                'oj_id', // Kolom yang menghubungkan ke OrganisasiJabatan
                DB::raw('count(*) as jumlah_surat'),
                DB::raw('count(case when sn_status = "revisi" then 1 end) as jumlah_revisi'),
                DB::raw('count(case when sn_status = "validasi" then 1 end) as jumlah_valid')

            )
            ->with('organisasiJabatan') // Memuat relasi organisasiJabatan
            ->groupBy('oj_id') // Mengelompokkan berdasarkan organisasi jabatan
            ->get();

        // Ambil data Monitoring dan rincian terkait
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

        // Kirim data ke tampilan dashboard
        return view('pages.dashboard', [
            'title' => $title,
            'tahuns' => $tahuns,
            'jumlahiku' => $jumlahiku,
            'jumlahikt' => $jumlahikt,
            'totalrenja' => $totalrenja,
            'suratNomors' => $suratNomors,
            'totalikt' => $totalikt,
            'totaliku' => $totaliku,
            'unitKerjarenja' => $unitKerjarenja,
            'periodemonevrenja' => $periodemonevrenja,
            'realisasi' => $realisasi,
            'renjaPerPeriode' => $renjaPerPeriode,
            'suratSummary' => $suratSummary, // Mengirimkan data summary surat ke tampilan
            'labelsIKU' => $labelsIKU,
            'dataIKU' => $dataIKU,
            'type_menu' => 'dashboard',
        ]);        
    }
}

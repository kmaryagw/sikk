<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\tahun_kerja;
use App\Models\RencanaKerja;
use App\Models\SuratNomor;
use Illuminate\Http\Request;
use App\Models\periode_monev;
use App\Models\program_studi;
use App\Models\target_indikator;
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
        $totaliku = IndikatorKinerja::where('ik_jenis', 'IKU/IKT')->count();
        if ($user->role === 'prodi') {
            $jumlahiku = program_studi::withCount(['targetIndikator' => function ($query) use ($tahunAktif) {
                $query->whereHas('indikatorKinerja', function ($q) {
                    $q->where('ik_jenis', 'IKU/IKT');
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
                    $q->where('ik_jenis', 'IKU/IKT');
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
                    $q->where('ik_jenis', 'IKU/IKT');
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
        
        // IKT PER PRODI (dengan status & persentase)
        $totalikt = IndikatorKinerja::where('ik_jenis', 'IKU/IKT')->count();

        $jumlahikt = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
            ->when($user->role === 'prodi', function ($q) use ($user) {
                $q->where('prodi_id', $user->prodi_id);
            })
            ->when($user->role === 'fakultas', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultas);
            })
            ->orderBy('nama_prodi')
            ->get()
            ->map(function ($prodi) use ($tahunAktif) {
                $statusCount = [
                    'nama_prodi' => $prodi->nama_prodi,
                    'jumlah' => 0,
                    'tercapai' => 0,
                    'terlampaui' => 0,
                    'tidak_tercapai' => 0,
                    'tidak_terlaksana' => 0,
                    'persentase_tuntas' => 0,
                ];

                foreach ($prodi->targetIndikator as $item) {
                    if ($tahunAktif && $item->th_id != $tahunAktif->th_id) continue;

                    // ambil hanya IKT
                    if (optional($item->indikatorKinerja)->ik_jenis !== 'IKU/IKT') continue;

                    $statusCount['jumlah']++;

                    $status = hitungStatus(
                        optional($item->monitoringDetail)->mtid_capaian,
                        $item->ti_target,
                        optional($item->indikatorKinerja)->ik_ketercapaian
                    );

                    if (isset($statusCount[$status])) {
                        $statusCount[$status]++;
                    }
                }

                if ($statusCount['jumlah'] > 0) {
                    $statusCount['persentase_tuntas'] = round(
                        (($statusCount['tercapai'] + $statusCount['terlampaui']) / $statusCount['jumlah']) * 100,
                        2
                    );
                }

                return (object) $statusCount;
            });

        // RINGKASAN IKT PER PRODI
        $ikuiktPerProdi = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
            ->when($user->role === 'prodi', function ($q) use ($user) {
                $q->where('prodi_id', $user->prodi_id);
            })
            ->when($user->role === 'fakultas', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultas);
            })
            ->orderBy('nama_prodi')
            ->get()
            ->map(function ($prodi) use ($tahunAktif) {
                $statusCount = [
                    'nama_prodi' => $prodi->nama_prodi,
                    'jumlah' => 0,
                    'tercapai' => 0,
                    'terlampaui' => 0,
                    'tidak_tercapai' => 0,
                    'tidak_terlaksana' => 0,
                    'persentase_tuntas' => 0,
                ];

                foreach ($prodi->targetIndikator as $item) {
                    if ($tahunAktif && $item->th_id != $tahunAktif->th_id) continue;

                    // ✅ hanya ambil yang IKT
                    if (optional($item->indikatorKinerja)->ik_jenis !== 'IKU/IKT') continue;

                    $statusCount['jumlah']++;

                    $status = hitungStatus(
                        optional($item->monitoringDetail)->mtid_capaian,
                        $item->ti_target,
                        optional($item->indikatorKinerja)->ik_ketercapaian
                    );

                    if (isset($statusCount[$status])) {
                        $statusCount[$status]++;
                    }
                }

                // hitung persentase tuntas
                if ($statusCount['jumlah'] > 0) {
                    $statusCount['persentase_tuntas'] = round(
                        (($statusCount['tercapai'] + $statusCount['terlampaui']) / $statusCount['jumlah']) * 100,
                        2
                    );
                }

                return (object) $statusCount;
            });

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

        // RINGKASAN IKU/IKT
        $ringkasanIku = target_indikator::with(['indikatorKinerja', 'monitoringDetail', 'tahunKerja'])
        ->get()
        ->groupBy(fn($item) => optional($item->tahunKerja)->th_tahun)
        ->map(function ($items, $tahun) {
            $statusCount = [
                'tahun' => $tahun,
                'total' => $items->count(),
                'tercapai' => 0,
                'terlampaui' => 0,
                'tidak_tercapai' => 0,
                'tidak_terlaksana' => 0,
                'persentase_tuntas' => 0, // ✅ tambahkan key persentase
            ];

            foreach ($items as $item) {
                $status = hitungStatus(
                    optional($item->monitoringDetail)->mtid_capaian,
                    $item->ti_target,
                    optional($item->indikatorKinerja)->ik_ketercapaian
                );

                if (isset($statusCount[$status])) {
                    $statusCount[$status]++;
                }
            }

            // ✅ hitung persentase tuntas (tercapai + terlampaui)
            if ($statusCount['total'] > 0) {
                $statusCount['persentase_tuntas'] = round(
                    (($statusCount['tercapai'] + $statusCount['terlampaui']) / $statusCount['total']) * 100,
                    2
                );
            }

            return (object) $statusCount;
        })
        ->sortByDesc('tahun')
        ->values();

            // RINGKASAN IKU/IKT PER UNIT KERJA
            $ikuiktPerUnit = UnitKerja::with(['indikatorKinerja.targetIndikator.monitoringDetail'])
                ->when($user->role === 'unit kerja', function ($q) use ($user) {
                    $q->where('unit_id', $user->unit_id);
                })
                ->orderBy('unit_nama')
                ->get()
                ->map(function ($unit) use ($tahunAktif) {
                    $statusCount = [
                        'unit_nama' => $unit->unit_nama,
                        'jumlah' => 0,
                        'tercapai' => 0,
                        'terlampaui' => 0,
                        'tidak_tercapai' => 0,
                        'tidak_terlaksana' => 0,
                        'persentase_tuntas' => 0,
                    ];

                    foreach ($unit->indikatorKinerja as $indikator) {
                        foreach ($indikator->targetIndikator as $target) {
                            if ($tahunAktif && $target->th_id != $tahunAktif->th_id) continue;

                            // ✅ hanya ambil IKU/IKT
                            if ($indikator->ik_jenis !== 'IKU/IKT') continue;

                            $statusCount['jumlah']++;

                            $status = hitungStatus(
                                optional($target->monitoringDetail)->mtid_capaian,
                                $target->ti_target,
                                $indikator->ik_ketercapaian
                            );

                            if (isset($statusCount[$status])) {
                                $statusCount[$status]++;
                            }
                        }
                    }

                    // hitung persentase tuntas
                    if ($statusCount['jumlah'] > 0) {
                        $statusCount['persentase_tuntas'] = round(
                            (($statusCount['tercapai'] + $statusCount['terlampaui']) / $statusCount['jumlah']) * 100,
                            2
                        );
                    }

                    return (object) $statusCount;
                });

                

        // Kirim data ke tampilan dashboard
        return view('pages.dashboard', [
            'title' => $title,
            'tahuns' => $tahuns,
            'jumlahiku' => $jumlahiku,
            'jumlahikt' => $jumlahikt,
            'ikuiktPerProdi' => $ikuiktPerProdi,
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
            'ringkasanIku' => $ringkasanIku,
            'ikuiktPerUnit' => $ikuiktPerUnit,
            'type_menu' => 'dashboard',
        ]);        
    }
}

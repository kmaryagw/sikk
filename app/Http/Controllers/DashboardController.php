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
use App\Models\MonitoringFinalUnit;
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

        if ($user->role === 'prodi') {
            $jumlahikt = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->where('prodi_id', $user->prodi_id)
                ->orderBy('nama_prodi')
                ->get()
                ->map(fn($prodi) => $this->hitungStatusProdi($prodi, $tahunAktif));

        } elseif ($user->role === 'fakultas') {
            $jumlahikt = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->where('id_fakultas', $user->id_fakultas)
                ->orderBy('nama_prodi')
                ->get()
                ->map(fn($prodi) => $this->hitungStatusProdi($prodi, $tahunAktif));
        } else {
            $jumlahikt = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->orderBy('nama_prodi')
                ->get()
                ->map(fn($prodi) => $this->hitungStatusProdi($prodi, $tahunAktif));
        }


        // RINGKASAN IKT PER PRODI
        if ($user->role === 'prodi') {

            //Data untuk prodi yang sedang login
            $ikuiktPerProdiSendiri = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->where('prodi_id', $user->prodi_id)
                ->get()
                ->map(function ($prodi) use ($tahunAktif) {
                    return $this->hitungStatusProdi($prodi, $tahunAktif);
                });

            //Data untuk seluruh prodi
            $ikuiktPerProdiSemua = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->orderBy('nama_prodi')
                ->get()
                ->map(function ($prodi) use ($tahunAktif) {
                    return $this->hitungStatusProdi($prodi, $tahunAktif);
                });

        } else {
            // --- Untuk admin atau role lain ---
            $ikuiktPerProdiSendiri = collect(); // kosong
            $ikuiktPerProdiSemua = program_studi::with(['targetIndikator.indikatorKinerja', 'targetIndikator.monitoringDetail'])
                ->orderBy('nama_prodi')
                ->get()
                ->map(function ($prodi) use ($tahunAktif) {
                    return $this->hitungStatusProdi($prodi, $tahunAktif);
                });
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
        

        $suratNomors = SuratNomor::paginate(10);

        $querySummary = SuratNomor::select(
            DB::raw('count(*) as jumlah_surat'),
            DB::raw('sum(case when sn_status = "revisi" then 1 else 0 end) as jumlah_revisi'),
            DB::raw('sum(case when sn_status = "validasi" then 1 else 0 end) as jumlah_valid')
        );

        if ($user->role === 'unit kerja') {
            $suratSummary = $querySummary
                ->addSelect('oj_id') 
                ->where('unit_id', $user->unit_id)
                ->with('organisasiJabatan')
                ->groupBy('oj_id')
                ->get();
        } 
        else {
            $suratSummary = $querySummary
                ->addSelect('unit_id') 
                ->with('unitKerja')   
                ->groupBy('unit_id')
                ->get();
        }

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
                'persentase_tuntas' => 0,
            ];

            foreach ($items as $item) {
                $status = $this->hitungStatus(
                    optional($item->monitoringDetail)->mtid_capaian,
                    $item->ti_target,
                    optional($item->indikatorKinerja)->ik_ketercapaian
                );

                if (isset($statusCount[$status])) {
                    $statusCount[$status]++;
                }
            }

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

            if ($user->role === 'unit kerja') {

                $ikuiktPerUnitSendiri = UnitKerja::with(['indikatorKinerja.targetIndikator.monitoringDetail'])
                    ->where('unit_id', $user->unit_id)
                    ->get()
                    ->map(function ($unit) use ($tahunAktif) {
                        $data = $this->hitungStatusUnit($unit, $tahunAktif);

                        $isFinal = MonitoringFinalUnit::where('unit_id', $unit->unit_id)
                            ->where('status', true)
                            ->exists();

                        $data->sudah_final = $isFinal;
                        $data->unit_id = $unit->unit_id;

                        return $data;
                    });

                $ikuiktPerUnitSemua = UnitKerja::with(['indikatorKinerja.targetIndikator.monitoringDetail'])
                    ->orderBy('unit_nama')
                    ->get()
                    ->map(function ($unit) use ($tahunAktif) {
                        $data = $this->hitungStatusUnit($unit, $tahunAktif);

                        $isFinal = MonitoringFinalUnit::where('unit_id', $unit->unit_id)
                            ->where('status', true)
                            ->exists();

                        $data->sudah_final = $isFinal;
                        $data->unit_id = $unit->unit_id;

                        return $data;
                    });

            } else {
                $ikuiktPerUnitSendiri = collect(); 
                $ikuiktPerUnitSemua = UnitKerja::with(['indikatorKinerja.targetIndikator.monitoringDetail'])
                    ->orderBy('unit_nama')
                    ->get()
                    ->map(function ($unit) use ($tahunAktif) {
                        $data = $this->hitungStatusUnit($unit, $tahunAktif);

                        $isFinal = MonitoringFinalUnit::where('unit_id', $unit->unit_id)
                            ->where('status', true)
                            ->exists();

                        $data->sudah_final = $isFinal;
                        $data->unit_id = $unit->unit_id;

                        return $data;
                    });
            }

        return view('pages.dashboard', [
            'title' => $title,
            'tahuns' => $tahuns,
            'jumlahiku' => $jumlahiku,
            'jumlahikt' => $jumlahikt,
            'ikuiktPerProdiSendiri' => $ikuiktPerProdiSendiri,
            'ikuiktPerProdiSemua' => $ikuiktPerProdiSemua,
            'totalrenja' => $totalrenja,
            'suratNomors' => $suratNomors,
            'totalikt' => $totalikt,
            'totaliku' => $totaliku,
            'unitKerjarenja' => $unitKerjarenja,
            'periodemonevrenja' => $periodemonevrenja,
            'realisasi' => $realisasi,
            'renjaPerPeriode' => $renjaPerPeriode,
            'suratSummary' => $suratSummary,
            'labelsIKU' => $labelsIKU,
            'dataIKU' => $dataIKU,
            'ringkasanIku' => $ringkasanIku,
            'ikuiktPerUnitSendiri' => $ikuiktPerUnitSendiri,
            'ikuiktPerUnitSemua' => $ikuiktPerUnitSemua,
            'type_menu' => 'dashboard',
        ]);
    }

    
    private function hitungStatusUnit($unit, $tahunAktif)
    {
        $managedProdis = program_studi::where('unit_id_pengelola', $unit->unit_id)
                            ->orderBy('nama_prodi', 'asc')
                            ->get();

        if ($managedProdis->isNotEmpty()) {
            $targetProdis = $managedProdis;
        } else {
            $targetProdis = program_studi::orderBy('nama_prodi', 'asc')->get();
        }

        $allowedProdiIds = $targetProdis->pluck('prodi_id')->toArray();

        $statusCount = [
            'unit_id' => $unit->unit_id,
            'unit_nama' => $unit->unit_nama,
            'jumlah' => 0,
            'tercapai' => 0,
            'terlampaui' => 0,
            'tidak_tercapai' => 0,
            'tidak_terlaksana' => 0,
            'persentase_tuntas' => 0,
            'detail_finalisasi' => [],
            'status_global' => 'belum',
        ];

        foreach ($unit->indikatorKinerja as $indikator) {
            if ($indikator->ik_jenis !== 'IKU/IKT') continue;

            foreach ($indikator->targetIndikator as $target) {
                if ($tahunAktif && $target->th_id != $tahunAktif->th_id) continue;
                
                if (!in_array($target->prodi_id, $allowedProdiIds)) continue;

                $statusCount['jumlah']++;
                $status = $this->hitungStatus(
                    optional($target->monitoringDetail)->mtid_capaian,
                    $target->ti_target,
                    $indikator->ik_ketercapaian
                );

                if (isset($statusCount[$status])) $statusCount[$status]++;
            }
        }

        if ($statusCount['jumlah'] > 0) {
            $statusCount['persentase_tuntas'] = round(
                (($statusCount['tercapai'] + $statusCount['terlampaui']) / $statusCount['jumlah']) * 100,
                2
            );
        }

        $listFinalisasi = [];
        $jumlahFinal = 0;
        $totalProdiDalamCakupan = $targetProdis->count();

        foreach ($targetProdis as $prodi) {
            $isFinal = false;
            $currentMtiId = null;

            if ($tahunAktif) {
                $monitoringIku = DB::table('monitoring_iku')
                    ->where('prodi_id', $prodi->prodi_id)
                    ->where('th_id', $tahunAktif->th_id)
                    ->first();

                if ($monitoringIku) {
                    $currentMtiId = $monitoringIku->mti_id;
                    $cekFinal = DB::table('monitoring_final_units')
                        ->where('unit_id', $unit->unit_id)
                        ->where('monitoring_iku_id', $monitoringIku->mti_id)
                        ->where('status', 1) 
                        ->exists();

                    if ($cekFinal) {
                        $isFinal = true;
                        $jumlahFinal++;
                    }
                }
            }

            $listFinalisasi[] = [
                'nama_prodi' => $prodi->nama_prodi,
                'status'     => $isFinal,
                'mti_id'     => $currentMtiId 
            ];
        }

        if ($totalProdiDalamCakupan > 0) {
            if ($jumlahFinal === 0) {
                $statusCount['status_global'] = 'belum'; 
            } elseif ($jumlahFinal === $totalProdiDalamCakupan) {
                $statusCount['status_global'] = 'semua'; 
            } else {
                $statusCount['status_global'] = 'sebagian';
            }
        }

        $statusCount['detail_finalisasi'] = $listFinalisasi;

        return (object) $statusCount;
    }

    private function hitungStatusProdi($prodi, $tahunAktif)
    {
        $statusCount = [
            'nama_prodi' => $prodi->nama_prodi,
            'jumlah' => 0,
            'tercapai' => 0,
            'terlampaui' => 0,
            'tidak_tercapai' => 0,
            'tidak_terlaksana' => 0,
            'persentase_tuntas' => 0,
        ];

        foreach ($prodi->targetIndikator as $target) {
            if ($tahunAktif && $target->th_id != $tahunAktif->th_id) continue;

            if (optional($target->indikatorKinerja)->ik_jenis !== 'IKU/IKT') continue;

            $statusCount['jumlah']++;

            $status = $this->hitungStatus(
                optional($target->monitoringDetail)->mtid_capaian,
                $target->ti_target,
                optional($target->indikatorKinerja)->ik_ketercapaian
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
    }

    private function parseNumber($value)
    {
        if (is_null($value) || $value === '') return 0;

        $string = (string) $value;
        // Hapus simbol selain angka, titik, koma, minus
        $clean = preg_replace('/[^0-9.,-]/', '', $string);

        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }

    private function hitungStatus($capaian, $target, $jenis)
    {
        $jenis = strtolower(trim($jenis));
        
        if (is_null($capaian) || trim($capaian) === '') {
            return 'tidak_terlaksana';
        }

        if (in_array($jenis, ['nilai', 'persentase'])) {
            $valCapaian = $this->parseNumber($capaian);
            $valTarget  = $this->parseNumber($target);
            $epsilon = 0.00001;

            if (abs($valCapaian - $valTarget) < $epsilon) {
                return 'tercapai';
            } elseif ($valCapaian > $valTarget) {
                return 'terlampaui';
            } else {
                return 'tidak_tercapai';
            }
        }

        if ($jenis === 'rasio') {
            $capaianStr = preg_replace('/\s+/', '', $capaian);
            $targetStr  = preg_replace('/\s+/', '', $target);

            $partsCapaian = explode(':', $capaianStr);
            $partsTarget  = explode(':', $targetStr);

            if (count($partsCapaian) == 2 && count($partsTarget) == 2) {
                $rightCapaian = $this->parseNumber($partsCapaian[1]);
                $rightTarget  = $this->parseNumber($partsTarget[1]);

                if ($rightCapaian > $rightTarget) {
                    return 'terlampaui';
                } elseif ($rightCapaian == $rightTarget) {
                    return 'tercapai';
                } else {
                    return 'tidak_tercapai';
                }
            }
            return 'tidak_tercapai'; 
        }

        // --- LOGIKA 3: KETERSEDIAAN (String) ---
        if ($jenis === 'ketersediaan') {
            $capaianLower = strtolower(trim($capaian));
            if ($capaianLower === 'ada') {
                return 'tercapai';
            } elseif ($capaianLower === 'draft') {
                return 'tidak_tercapai';
            }
            return 'tidak_terlaksana';
        }

        return 'tidak_terlaksana';
    }


}
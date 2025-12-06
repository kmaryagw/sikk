<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 

class IkuExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $tahunId, $prodiId, $unitId, $keyword;

    public function __construct($tahunId = null, $prodiId = null, $unitId = null, $keyword = null)
    {
        $this->tahunId = $tahunId;
        $this->prodiId = $prodiId;
        $this->unitId  = $unitId;
        $this->keyword = $keyword;
    }

    public function collection()
    {
        $query = target_indikator::with([
                'indikatorKinerja.unitKerja',
                'monitoringDetail'
            ])
            ->select(
                'target_indikator.*',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id');

        if ($this->tahunId) {
            $query->where('tahun_kerja.th_id', $this->tahunId);
        } else {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $query->where('tahun_kerja.th_id', $tahunAktif->th_id);
            }
        }

        if ($this->prodiId) {
            $query->where('program_studi.prodi_id', $this->prodiId);
        }

        if ($this->unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) {
                $q->where('unit_kerja.unit_id', $this->unitId);
            });
        }

        if ($this->keyword) {
            $query->whereHas('indikatorKinerja', function ($q) {
                $q->where('ik_nama', 'like', '%' . $this->keyword . '%');
            });
        }

        return $query->orderBy('ti_target', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Prodi',
            'Unit Kerja',
            'Indikator Kinerja',
            'Target Capaian',
            'Capaian',
            'Status',
        ];
    }

    public function map($row): array
    {
        $unitKerja = $row->indikatorKinerja->unitKerja->pluck('unit_nama')->join(', ');

        $detail = $row->monitoringDetail;
        $capaian = $detail->mtid_capaian ?? 'Belum Ada';

        
        if ($detail && !empty($detail->mtid_status) && $detail->mtid_status !== 'Draft') {
            $status = ucfirst($detail->mtid_status);
        } else {
            $status = $detail ? $this->hitungStatus(
                $detail->mtid_capaian,
                $row->ti_target,
                $row->indikatorKinerja->ik_ketercapaian
            ) : 'Belum Ada';
        }

        return [
            $row->th_tahun ?? '-',
            $row->nama_prodi ?? '-',
            $unitKerja ?: '-',
            $row->indikatorKinerja->ik_nama ?? '-',
            $row->ti_target ?? '-',
            $capaian,
            $status,
        ];
    }

    private function parseNumber($value)
    {
        if (is_null($value) || $value === '') return 0;

        $string = (string) $value;
        // Hapus simbol selain angka, titik, koma, minus
        $clean = preg_replace('/[^0-9.,-]/', '', $string);

        // Handle koma vs titik
        if (strpos($clean, '.') !== false && strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean); // Buang ribuan
            $clean = str_replace(',', '.', $clean); // Koma jadi desimal
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }

    private function hitungStatus($capaian, $target, $jenis)
    {
        $jenis = strtolower(trim($jenis));
        
        if (is_null($capaian) || trim($capaian) === '') {
            return 'Belum Ada';
        }

        if (in_array($jenis, ['nilai', 'persentase'])) {
            $valCapaian = $this->parseNumber($capaian);
            $valTarget  = $this->parseNumber($target);
            $epsilon = 0.00001;

            if (abs($valCapaian - $valTarget) < $epsilon) {
                return 'Tercapai';
            } elseif ($valCapaian > $valTarget) {
                return 'Terlampaui';
            } else {
                return 'Tidak Tercapai';
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
                    return 'Terlampaui';
                } elseif ($rightCapaian == $rightTarget) {
                    return 'Tercapai';
                } else {
                    return 'Tidak Tercapai';
                }
            }
            return 'Tidak Tercapai';
        }

        if ($jenis === 'ketersediaan') {
            $capaianLower = strtolower(trim($capaian));
            if ($capaianLower === 'ada') {
                return 'Tercapai';
            }
            return 'Tidak Tercapai';
        }

        return 'Tidak Tercapai';
    }
}
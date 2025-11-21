<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IkuExport implements FromCollection, WithHeadings, WithMapping
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

        // Filter Tahun
        if ($this->tahunId) {
            $query->where('tahun_kerja.th_id', $this->tahunId);
        } else {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $query->where('tahun_kerja.th_id', $tahunAktif->th_id);
            }
        }

        // Filter Prodi
        if ($this->prodiId) {
            $query->where('program_studi.prodi_id', $this->prodiId);
        }

        // Filter Unit Kerja (many-to-many)
        if ($this->unitId) {
            $query->whereHas('indikatorKinerja.unitKerja', function ($q) {
                $q->where('unit_kerja.unit_id', $this->unitId);
            });
        }

        // Filter Keyword pada indikator IKU
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
        // Ambil unit kerja (many-to-many)
        $unitKerja = $row->indikatorKinerja->unitKerja->pluck('unit_nama')->join(', ');

        // Ambil monitoring detail
        $detail = $row->monitoringDetail;

        $capaian = $detail->mtid_capaian ?? 'Belum Ada';

        // Hitung status
        $status = $detail ? $this->hitungStatus(
            $detail->mtid_capaian,
            $row->ti_target,
            $row->indikatorKinerja->ik_ketercapaian
        ) : 'Belum Ada';

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

    /**
     * Menghitung status capaian IKU sama seperti di halaman index
     */
    private function hitungStatus($capaian, $target, $ketercapaian)
    {
        if ($capaian === null || $capaian === '' || $target === null) {
            return 'Belum Ada';
        }

        switch ($ketercapaian) {
            case 'persentase':
                if ($capaian >= $target) return 'Tercapai';
                return 'Tidak Tercapai';

            case 'nilai':
                if ($capaian >= $target) return 'Tercapai';
                return 'Tidak Tercapai';

            case 'rasio':
                if ($target == 0) return 'Belum Ada';
                $rasio = $capaian / $target;
                if ($rasio >= 1) return 'Tercapai';
                return 'Tidak Tercapai';

            default:
                return 'Belum Ada';
        }
    }
}

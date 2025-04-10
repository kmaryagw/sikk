<?php

namespace App\Exports;

use App\Models\target_indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IkuExport implements FromCollection, WithHeadings, WithMapping
{
    private $rowNumber = 1;

    public function collection()
    {
        return target_indikator::select(
                'target_indikator.*',
                'indikator_kinerja.ik_nama',
                'indikator_kinerja.ik_ketercapaian',
                'program_studi.nama_prodi',
                'tahun_kerja.th_tahun'
            )
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->with(['monitoringDetail'])
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Indikator Kinerja',
            'Target Capaian',
            'Capaian',
            'Keterangan',
            'Prodi',
            'Tahun'
        ];
    }

    public function map($targetIndikator): array
    {
        $capaian = optional($targetIndikator->monitoringDetail)->mtid_capaian;

        return [
            $this->rowNumber++,
            $targetIndikator->ik_nama,
            $targetIndikator->ti_target,
            $capaian ?? 'Belum Ada',
            $targetIndikator->ti_keterangan,
            $targetIndikator->nama_prodi ?? '-',
            $targetIndikator->th_tahun ?? '-',
        ];
    }
}

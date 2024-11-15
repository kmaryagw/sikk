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
        return target_indikator::select('indikator_kinerja.ik_nama', 'target_indikator.ti_target', 'target_indikator.ti_keterangan', 'program_studi.nama_prodi', 'tahun_kerja.th_tahun')
            ->leftjoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftjoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftjoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->where('tahun_kerja.ren_is_aktif', 'y')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Indikator Kinerja',
            'Target Capaian',
            'Keterangan',
            'Prodi',
            'Tahun'
        ];
    }

    public function map($targetIndikator): array
    {
        return [
            $this->rowNumber++,
            $targetIndikator->ik_nama,
            $targetIndikator->ti_target,
            $targetIndikator->ti_keterangan,
            $targetIndikator->nama_prodi ?? '-',
            $targetIndikator->th_tahun ?? '-',
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\target_indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanIkuBdExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return target_indikator::select(
                'tahun_kerja.th_tahun',
                'program_studi.nama_prodi',
                'indikator_kinerja.ik_nama',
                'target_indikator.ti_target',
                'monitoring_iku_detail.mtid_capaian',
                'monitoring_iku_detail.mtid_status'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftJoin('monitoring_iku_detail', 'monitoring_iku_detail.ti_id', '=', 'target_indikator.ti_id')
            ->where('program_studi.nama_prodi', 'Bisnis Digital')
            ->where('tahun_kerja.th_is_aktif', 'y')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Prodi',
            'Indikator Kinerja',
            'Target Capaian',
            'Capaian',
            'Status',
        ];
    }
}

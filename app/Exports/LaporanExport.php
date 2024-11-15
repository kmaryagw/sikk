<?php

namespace App\Exports;

use App\Models\RencanaKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{ 
    private $rowNumber = 1;

    public function collection()
    {
        return RencanaKerja::with('tahunKerja', 'UnitKerja', 'periodes')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Program Kerja',
            'Unit Kerja',
            'Tahun',
            'Periode Monev',
        ];
    }

    public function map($rencanaKerja): array
    {
        $pm_nama = $rencanaKerja->periodes->pluck('pm_nama')->implode(', ');

        return [
            $this->rowNumber++,
            $rencanaKerja->rk_nama,
            $rencanaKerja->UnitKerja->unit_nama ?? '-',
            $rencanaKerja->tahunKerja->th_tahun ?? '-',
            $pm_nama ?: 'Tidak ada periode',
        ];
    }
}
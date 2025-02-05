<?php

namespace App\Exports;

use App\Models\IndikatorKinerja;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikatorKinerjaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    // public function collection()
    // {
    //     return IndikatorKinerja::select(
    //         'ik_kode', 'ik_nama', 'std_id', 'ik_jenis', 'ik_baseline', 'ik_ketercapaian', 'ik_is_aktif'
    //     )->get();
    // }

    public function array(): array
    {
        return [
            ['IK001', 'Indikator Contoh', 'STDB04DDEAC3765BE32BE71700C36A6EE7B', 'IKU', 100, 'y', 'persentase'],
        ];
    }

    public function headings(): array
    {
        return ['Kode IKU/IKT', 'Nama IKU/IKT', 'Standar', 'Jenis', 'Baseline', 'Ketercapaian', 'Status Aktif'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
        ]);

        // Set border untuk semua sel dengan data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G$highestRow")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
        ]);

        // Auto-size kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    
}

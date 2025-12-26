<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IndikatorKinerjaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'IK001',                // Kode
                'Indikator Contoh',     // Nama
                'Standar Pendidikan',   // Standar
                'IKU',                  // Jenis
                'persentase',           // Ketercapaian
                'y',                    // Status Aktif (Maju ke kolom F)
                'Prodi TI, LPM'         // Unit Kerja (Maju ke kolom G)
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode IKU/IKT', 
            'Nama IKU/IKT', 
            'Standar', 
            'Jenis', 
            'Ketercapaian', 
            'Status Aktif (y/n)', 
            'Unit Kerja (PIC)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Ubah range dari H1 menjadi G1 (karena kolom berkurang 1)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4e73df'] 
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        
        // Ubah range border dari H menjadi G
        $sheet->getStyle("A1:G$highestRow")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => 'center'
            ]
        ]);

        // Auto size hanya sampai G
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
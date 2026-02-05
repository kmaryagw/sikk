<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IndikatorKinerjaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        $contohStandar = "STANDAR PENDIDIKAN\nStandar ini mengatur tentang mutu kurikulum dan pembelajaran.";

        return [
            [
                'IK001',                // Kode
                'Indikator Contoh',     // Nama
                $contohStandar,         // Standar (Nama \n Deskripsi)
                'IKU',                  // Jenis
                'persentase',           // Ketercapaian
                'y',                    // Status Aktif
                'Prodi TI, LPM'         // Unit Kerja
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode IKU/IKT', 
            'Nama IKU/IKT', 
            'Standar (Nama & Deskripsi)', 
            'Jenis', 
            'Ketercapaian', 
            'Status Aktif (y/n)', 
            'Unit Kerja (PIC)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4e73df'] 
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G$highestRow")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP 
            ]
        ]);

        $sheet->getStyle('C1:C' . $highestRow)->getAlignment()->setWrapText(true);

        // 4. Pengaturan Lebar Kolom
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(40); 
        $sheet->getColumnDimension('C')->setWidth(50); 
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        return [];
    }
}
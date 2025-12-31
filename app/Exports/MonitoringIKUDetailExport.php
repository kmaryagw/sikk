<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class MonitoringIKUDetailExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $data;
    protected $type;
    protected $rowIndex = 0;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data->sortBy(function ($item) {
            return optional($item->indikatorKinerja)->ik_kode;
        }, SORT_NATURAL | SORT_FLAG_CASE);
    }

    public function map($item): array
    {
        $this->rowIndex++;
        
        $safeString = function($value) {
            if (is_array($value) || is_object($value)) return ''; 
            return trim((string) $value);
        };

        $standarDeskripsi = optional($item->indikatorKinerja->standar)->std_deskripsi ?? '-';

        $ik_kode = $safeString(optional($item->indikatorKinerja)->ik_kode);
        $ik_nama = $safeString(optional($item->indikatorKinerja)->ik_nama);
        $indikator = trim($ik_kode ? ($ik_kode . ' - ' . $ik_nama) : $ik_nama);
        
        $ketercapaian = strtolower($safeString(optional($item->indikatorKinerja)->ik_ketercapaian));

        $baselineRaw = trim((string) ($item->fetched_baseline ?? '0')); 
        $cleanNumBase = str_replace(['%', ' '], '', $baselineRaw);
        $baselineDisplay = $baselineRaw;
        if ($ketercapaian === 'persentase' && is_numeric($cleanNumBase)) {
            $baselineDisplay = (strpos($baselineRaw, '%') === false) ? $cleanNumBase . '%' : $baselineRaw;
        } elseif ($ketercapaian === 'rasio') {
            $cleaned = preg_replace('/\s*/', '', $baselineRaw);
            if (preg_match('/^\d+:\d+$/', $cleaned)) {
                [$a, $b] = explode(':', $cleaned);
                $baselineDisplay = "{$a} : {$b}";
            }
        }

        $targetRaw = trim($item->ti_target);
        $cleanNumTarget = str_replace(['%', ' '], '', $targetRaw);
        $targetDisplay = $targetRaw;
        if ($ketercapaian === 'persentase' && is_numeric($cleanNumTarget)) {
            $targetDisplay = $cleanNumTarget . '%';
        }

        $detail = $item->monitoringDetail;
        if ($detail instanceof \Illuminate\Support\Collection) {
            $detail = $detail->first();
        }

        $row = [
            $this->rowIndex,
            $standarDeskripsi, 
            $indikator,
        ];

        if ($this->type == 'penetapan') {
            $row[] = $baselineDisplay;
            $row[] = $targetDisplay;
        }

        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $keterlaksanaan = ($detail->mtid_keterangan ?? '-') . "\n(Status: " . ucfirst($detail->mtid_status ?? 'Draft') . ")";
            $row[] = $keterlaksanaan;
            $row[] = $detail ? $safeString($detail->mtid_url) : ''; 
        }

        if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) {
            $row[] = $detail->mtid_evaluasi ?? '-';
        }

        if (in_array($this->type, ['pengendalian', 'peningkatan'])) {
            $row[] = $detail->mtid_tindaklanjut ?? '-';
        }

        if ($this->type == 'peningkatan') {
            $row[] = $detail->mtid_peningkatan ?? '-';
        }

        return $row;
    }

    public function headings(): array
    {
        $headers = ['No', 'Standar', 'Indikator Kinerja'];

        if ($this->type == 'penetapan') {
            array_push($headers, 'Baseline', 'Target');
        }

        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            array_push($headers, 'Keterlaksanaan', 'URL Bukti Dukung');
        }
        
        if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) {
            array_push($headers, 'Evaluasi');
        }

        if (in_array($this->type, ['pengendalian', 'peningkatan'])) {
            array_push($headers, 'Tindak Lanjut');
        }

        if ($this->type == 'peningkatan') {
            array_push($headers, 'Peningkatan');
        }

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_TOP) 
              ->setWrapText(true);

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFC00000'],
            ],
        ];
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ]
            ],
        ];
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray($borderStyle);

        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Standar
        $sheet->getColumnDimension('C')->setWidth(30);  // Indikator
        $sheet->getColumnDimension('D')->setWidth(40);  // Pelaksanaan
        $sheet->getColumnDimension('F')->setWidth(40);  // Evaluasi
        $sheet->getColumnDimension('G')->setWidth(40);  // Tindak Lanjut
        $sheet->getColumnDimension('H')->setWidth(40);  // Peningkatan

        $highestColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            
            // Jika kolom belum diatur lebarnya secara manual di atas, beri lebar default 25
            if (!in_array($columnLetter, ['A', 'B', 'C', 'D', 'F', 'G', 'H'])) {
                $sheet->getColumnDimension($columnLetter)->setWidth(25);
            }

            if ($this->type == 'penetapan' && ($columnLetter == 'D' || $columnLetter == 'E')) {
                $sheet->getStyle("{$columnLetter}2:{$columnLetter}{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A2');

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['argb' => 'FFF9FAFB'],
                            ],
                        ]);
                    }
                }

                $headers = $this->headings();
                $urlColIndex = null;
                foreach ($headers as $index => $label) {
                    if (str_contains(strtolower($label), 'url')) {
                        $urlColIndex = $index + 1;
                        break;
                    }
                }

                if ($urlColIndex) {
                    $urlColLetter = Coordinate::stringFromColumnIndex($urlColIndex);
                    for ($row = 2; $row <= $lastRow; $row++) {
                        $cell = $sheet->getCell("{$urlColLetter}{$row}");
                        $val = $cell->getValue();
                        if (!empty($val) && filter_var($val, FILTER_VALIDATE_URL)) {
                            $cell->getHyperlink()->setUrl($val);
                            $sheet->getStyle("{$urlColLetter}{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '0000FF'], 'underline' => true]
                            ]);
                        }
                    }
                }
            },
        ];
    }
}
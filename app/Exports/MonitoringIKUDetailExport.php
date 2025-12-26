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
        // Logika Sorting: Mengurutkan berdasarkan ik_kode secara natural (A-Z, 1-10)
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

        $ik_kode = $safeString(optional($item->indikatorKinerja)->ik_kode);
        $ik_nama = $safeString(optional($item->indikatorKinerja)->ik_nama);
        $indikator = $ik_kode ? ($ik_kode . ' - ' . $ik_nama) : $ik_nama;
        
        $ketercapaian = strtolower($safeString(optional($item->indikatorKinerja)->ik_ketercapaian));

        $baselineRaw = $safeString($item->fetched_baseline);
        $cleanNumBase = str_replace(['%', ' '], '', $baselineRaw);
        $baselineDisplay = $baselineRaw;

        if ($ketercapaian === 'persentase' && is_numeric($cleanNumBase)) {
            if (strpos($baselineRaw, '%') === false && $baselineRaw !== '') {
                 $baselineDisplay = $cleanNumBase . '%';
            }
        } elseif ($ketercapaian === 'rasio') {
            $cleaned = preg_replace('/\s*/', '', $baselineRaw);
            if (preg_match('/^\d+:\d+$/', $cleaned)) {
                [$a, $b] = explode(':', $cleaned);
                $baselineDisplay = "{$a} : {$b}";
            }
        } elseif (in_array(strtolower($baselineRaw), ['ada', 'draft'])) {
            $baselineDisplay = ucfirst($baselineRaw); 
        }

        $targetRaw = $safeString($item->ti_target);
        $cleanNumTarget = str_replace(['%', ' '], '', $targetRaw);
        $targetDisplay = $targetRaw;
        
        if ($ketercapaian === 'persentase' && is_numeric($cleanNumTarget) && $targetRaw !== '') {
             if (strpos($targetRaw, '%') === false) {
                 $targetDisplay = $cleanNumTarget . '%';
             }
        }

        $detail = $item->monitoringDetail;
        if ($detail instanceof \Illuminate\Support\Collection) {
            $detail = $detail->first();
        }

        $row = [
            $this->rowIndex,
            $indikator,
            $baselineDisplay,
            $targetDisplay
        ];

        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            $row[] = $detail ? $safeString($detail->mtid_capaian) : '';
            $row[] = $detail ? $safeString($detail->mtid_url) : '';
        }
        if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) {
            $statusRaw = $detail ? ucfirst($safeString($detail->mtid_status)) : '';
            $row[] = $statusRaw;
        }
        if (in_array($this->type, ['pengendalian', 'peningkatan'])) {
            $row[] = $detail ? $safeString($detail->mtid_keterangan) : '';
            $row[] = $detail ? $safeString($detail->mtid_evaluasi) : '';
            $row[] = $detail ? $safeString($detail->mtid_tindaklanjut) : '';
        }
        if ($this->type == 'peningkatan') {
            $row[] = $detail ? $safeString($detail->mtid_peningkatan) : '';
        }

        return $row;
    }

    public function headings(): array
    {
        $headers = ['No', 'Indikator Kinerja', 'Baseline', 'Target'];

        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            array_push($headers, 'Capaian', 'URL Bukti Dukung');
        }
        if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) {
            array_push($headers, 'Status');
        }
        if (in_array($this->type, ['pengendalian', 'peningkatan'])) {
            array_push($headers, 'Keterangan', 'Evaluasi', 'Tindak Lanjut');
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
              ->setVertical(Alignment::VERTICAL_CENTER)
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
        $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);

        $highestColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 5; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setWidth(25);
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
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => true
                                ]
                            ]);
                        }
                    }
                }
            },
        ];
    }
}
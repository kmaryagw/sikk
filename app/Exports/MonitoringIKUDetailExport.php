<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class MonitoringIKUDetailExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $type;
    protected $monitoring;
    protected $rowIndex = 0;

    public function __construct($data, $type, $monitoring)
    {
        $this->data = $data;
        $this->type = $type;
        $this->monitoring = $monitoring;
    }

    public function startCell(): string
    {
        return 'A6';
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
        $baselineDisplay = $baselineRaw;
        if ($ketercapaian === 'persentase' && is_numeric(str_replace(['%', ' '], '', $baselineRaw))) {
            $baselineDisplay = (strpos($baselineRaw, '%') === false) ? str_replace(' ', '', $baselineRaw) . '%' : $baselineRaw;
        }

        $targetRaw = trim($item->ti_target);
        $targetDisplay = $targetRaw;
        if ($ketercapaian === 'persentase' && is_numeric(str_replace(['%', ' '], '', $targetRaw))) {
            $targetDisplay = str_replace(['%', ' '], '', $targetRaw) . '%';
        }

        $detail = $item->monitoringDetail;
        if ($detail instanceof \Illuminate\Support\Collection) {
            $detail = $detail->first();
        }

        $row = [$this->rowIndex, $standarDeskripsi, $indikator];

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
        if ($this->type == 'penetapan') array_push($headers, 'Baseline', 'Target');
        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) array_push($headers, 'Keterlaksanaan', 'URL Bukti Dukung');
        if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) array_push($headers, 'Evaluasi');
        if (in_array($this->type, ['pengendalian', 'peningkatan'])) array_push($headers, 'Tindak Lanjut');
        if ($this->type == 'peningkatan') array_push($headers, 'Peningkatan');

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $headers = $this->headings();
        $totalCols = count($headers);
        $lastColumn = Coordinate::stringFromColumnIndex($totalCols);
        $lastRow = $sheet->getHighestRow();

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        $sheet->getStyle("A6:{$lastColumn}{$lastRow}")
              ->getAlignment()
              ->setVertical(Alignment::VERTICAL_TOP) 
              ->setWrapText(true);

        $sheet->getStyle("A6:{$lastColumn}6")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFC00000']],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(35);

        $sheet->getStyle("A6:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);

        foreach ($headers as $index => $label) {
            $colLetter = Coordinate::stringFromColumnIndex($index + 1);
            switch ($label) {
                case 'No': $sheet->getColumnDimension($colLetter)->setWidth(6); 
                           $sheet->getStyle("{$colLetter}7:{$colLetter}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); break;
                case 'Standar': $sheet->getColumnDimension($colLetter)->setWidth(45); break;
                case 'Indikator Kinerja': $sheet->getColumnDimension($colLetter)->setWidth(40); break;
                case 'Baseline': case 'Target': 
                    $sheet->getColumnDimension($colLetter)->setWidth(15);
                    $sheet->getStyle("{$colLetter}7:{$colLetter}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); break;
                case 'URL Bukti Dukung': $sheet->getColumnDimension($colLetter)->setWidth(30); break;
                default: $sheet->getColumnDimension($colLetter)->setWidth(40);
            }
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $headers = $this->headings();
                $totalCols = count($headers);
                $lastColumn = Coordinate::stringFromColumnIndex($totalCols);

                $judul = "LAPORAN " . strtoupper($this->type) . " INDIKATOR KINERJA INSTIKI";
                $fakultas = "FAKULTAS " . strtoupper(optional($this->monitoring->prodi->Fakultasn)->nama_fakultas ?? 'TIDAK DITEMUKAN');
                $prodi = "PROGRAM STUDI " . strtoupper($this->monitoring->prodi->nama_prodi ?? '-');
                $tahun = "TAHUN " . optional($this->monitoring->tahunKerja)->th_tahun ?? '-';

                $titles = [$judul, $fakultas, $prodi, $tahun];
                
                foreach ($titles as $i => $text) {
                    $rowNum = $i + 1;
                    $sheet->mergeCells("A{$rowNum}:{$lastColumn}{$rowNum}");
                    $sheet->setCellValue("A{$rowNum}", $text);
                    $sheet->getStyle("A{$rowNum}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => ($rowNum == 1 ? 14 : 12)],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                }

                $lastRow = $sheet->getHighestRow();
                $sheet->setAutoFilter("A6:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A7');

                for ($row = 7; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF9FAFB']],
                        ]);
                    }
                }

                foreach ($headers as $index => $label) {
                    if (str_contains(strtolower($label), 'url')) {
                        $urlColLetter = Coordinate::stringFromColumnIndex($index + 1);
                        for ($row = 7; $row <= $lastRow; $row++) {
                            $val = $sheet->getCell("{$urlColLetter}{$row}")->getValue();
                            if (!empty($val) && filter_var($val, FILTER_VALIDATE_URL)) {
                                $sheet->getCell("{$urlColLetter}{$row}")->getHyperlink()->setUrl($val);
                                $sheet->getStyle("{$urlColLetter}{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0000FF'))->setUnderline(true);
                            }
                        }
                    }
                }
            },
        ];
    }
}
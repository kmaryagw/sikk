<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class MonitoringIKUDetailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            
            // Helper string aman
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

            $capaian     = $detail ? $safeString($detail->mtid_capaian) : '';
            $url         = $detail ? $safeString($detail->mtid_url) : '';
            $status      = $detail ? ucfirst($safeString($detail->mtid_status)) : '';
            $keterangan  = $detail ? $safeString($detail->mtid_keterangan) : '';
            $evaluasi    = $detail ? $safeString($detail->mtid_evaluasi) : '';
            $tindak      = $detail ? $safeString($detail->mtid_tindaklanjut) : '';
            $peningkatan = $detail ? $safeString($detail->mtid_peningkatan) : '';

            $row = [(string)($index+1), $indikator, $baselineDisplay, $targetDisplay];

            if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
                $row[] = $capaian;
                $row[] = $url;
            }
            if (in_array($this->type, ['evaluasi', 'pengendalian', 'peningkatan'])) {
                $row[] = $status;
            }
            if (in_array($this->type, ['pengendalian', 'peningkatan'])) {
                $row[] = $keterangan;
                $row[] = $evaluasi;
                $row[] = $tindak;
            }
            if ($this->type == 'peningkatan') {
                $row[] = $peningkatan;
            }

            return $row;
        });
    }

    public function headings(): array
    {
        $headers = ['No', 'Indikator Kinerja', 'Baseline', 'Target'];

        if (in_array($this->type, ['pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'])) {
            array_push($headers, 'Capaian', 'URL');
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
        $sheet->getStyle('A:K')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:K')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
              ->getBorders()->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getStyle('1:1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => 'F2F2F2']
            ],
        ]);

        foreach (range(1, $sheet->getHighestRow()) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
        
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        
        return [];
    }
}
<?php

namespace App\Exports;

use App\Models\MonitoringIKU;
use App\Models\target_indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringIKUDetailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $mti_id;
    protected $type;
    protected $unit_kerja_id; 

    public function __construct($mti_id, $type, $unit_kerja_id = null)
    {
        $this->mti_id = $mti_id;
        $this->type   = $type;
        $this->unit_kerja_id = $unit_kerja_id;
    }

    public function collection()
    {
        $monitoring = MonitoringIKU::findOrFail($this->mti_id);

        // Perbaikan 1: 'indikatorKinerja.unitKerja' (sesuai nama fungsi di model Anda)
        $query = target_indikator::with([
                'indikatorKinerja.unitKerja', 
                'baselineTahun',
                'monitoringDetail'
            ])
            ->where('prodi_id', $monitoring->prodi_id)
            ->where('th_id', $monitoring->th_id);

        // Perbaikan 2: Filter menggunakan 'unitKerja'
        if (!empty($this->unit_kerja_id)) {
            $query->whereHas('indikatorKinerja.unitKerja', function($q) {
                // Pastikan nama tabel pivot benar sesuai database
                $q->where('indikatorkinerja_unitkerja.unit_id', $this->unit_kerja_id);
            });
        }

        $indikators = $query->orderBy('ti_id')->get();

        return $indikators->map(function ($item, $index) {
            $ik_kode = optional($item->indikatorKinerja)->ik_kode ?? '';
            $ik_nama = optional($item->indikatorKinerja)->ik_nama ?? '';
            $indikator = trim($ik_kode ? ($ik_kode . ' - ' . $ik_nama) : $ik_nama);

            $baseline = data_get($item->baselineTahun, 'baseline')
                     ?? data_get($item->baselineTahun, '0.baseline')
                     ?? '';

            $target = $item->ti_target ?? '';
            $detail = $item->monitoringDetail ?? null;

            $capaian     = $detail->mtid_capaian ?? '';
            $url         = $detail->mtid_url ?? '';
            $status      = $detail->mtid_status ?? '';
            $keterangan  = $detail->mtid_keterangan ?? '';
            $evaluasi    = $detail->mtid_evaluasi ?? '';
            $tindak      = $detail->mtid_tindaklanjut ?? '';
            $peningkatan = $detail->mtid_peningkatan ?? '';

            switch ($this->type) {
                case 'penetapan':
                    return [$index+1, $indikator, $baseline, $target];
                case 'pelaksanaan':
                    return [$index+1, $indikator, $baseline, $target, $capaian, $url];
                case 'evaluasi':
                    return [$index+1, $indikator, $baseline, $target, $capaian, $url, $status];
                case 'pengendalian':
                    return [$index+1, $indikator, $baseline, $target, $capaian, $url, $status, $keterangan, $evaluasi, $tindak];
                case 'peningkatan':
                default:
                    return [$index+1, $indikator, $baseline, $target, $capaian, $url, $status, $keterangan, $evaluasi, $tindak, $peningkatan];
            }
        });
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'penetapan':
                return ['No', 'Indikator Kinerja', 'Baseline', 'Target'];
            case 'pelaksanaan':
                return ['No', 'Indikator Kinerja', 'Baseline', 'Target', 'Capaian', 'URL'];
            case 'evaluasi':
                return ['No', 'Indikator Kinerja', 'Baseline', 'Target', 'Capaian', 'URL', 'Status'];
            case 'pengendalian':
                return ['No', 'Indikator Kinerja', 'Baseline', 'Target', 'Capaian', 'URL', 'Status', 'Keterangan', 'Evaluasi', 'Tindak Lanjut'];
            case 'peningkatan':
            default:
                return ['No', 'Indikator Kinerja', 'Baseline', 'Target', 'Capaian', 'URL', 'Status', 'Keterangan', 'Evaluasi', 'Tindak Lanjut', 'Peningkatan'];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:K')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:K')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);

        return [];
    }
}
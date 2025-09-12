<?php

namespace App\Exports;

use App\Models\MonitoringIKU;
use App\Models\target_indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonitoringIKUDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $mti_id;
    protected $type;

    public function __construct($mti_id, $type)
    {
        $this->mti_id = $mti_id;
        $this->type   = $type;
    }

    public function collection()
    {
        // Ambil MonitoringIKU → tahu prodi & tahun
        $monitoring = MonitoringIKU::findOrFail($this->mti_id);

        // Ambil semua target indikator untuk prodi & tahun ini
        $indikators = target_indikator::with([
                'indikatorKinerja',
                'baselineTahun',
                'monitoringDetail'
            ])
            ->where('prodi_id', $monitoring->prodi_id)
            ->where('th_id', $monitoring->th_id)
            ->orderBy('ti_id')
            ->get();

        // Map ke row Excel
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
}

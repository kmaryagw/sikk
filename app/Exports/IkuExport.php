<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IkuExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahunId, $prodiId, $keyword;

    public function __construct($tahunId = null, $prodiId = null, $keyword = null)
    {
        $this->tahunId = $tahunId;
        $this->prodiId = $prodiId;
        $this->keyword = $keyword;
    }

    public function collection()
    {
        $query = target_indikator::select(
                'tahun_kerja.th_tahun',
                'program_studi.nama_prodi',
                'indikator_kinerja.ik_nama',
                'target_indikator.ti_target',
                'monitoring_iku_detail.mtid_capaian',
                'monitoring_iku_detail.mtid_status'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftJoin('monitoring_iku_detail', 'monitoring_iku_detail.ti_id', '=', 'target_indikator.ti_id');

        // Filter berdasarkan tahun (jika ada), jika tidak pakai tahun aktif
        if ($this->tahunId) {
            $query->where('tahun_kerja.th_id', $this->tahunId);
        } else {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $query->where('tahun_kerja.th_id', $tahunAktif->th_id);
            }
        }

        // Filter prodi
        if ($this->prodiId) {
            $query->where('program_studi.prodi_id', $this->prodiId);
        }

        // Filter keyword (indikator kinerja)
        if ($this->keyword) {
            $query->where('indikator_kinerja.ik_nama', 'like', '%' . $this->keyword . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Prodi',
            'Indikator Kinerja',
            'Target Capaian',
            'Capaian',
            'Status',
        ];
    }

    public function map($target): array
    {
        return [
            $target->th_tahun ?? '-',
            $target->nama_prodi ?? '-',
            $target->ik_nama ?? '-',
            $target->ti_target ?? '-',
            $target->mtid_capaian ?? 'Belum Ada',
            $target->mtid_status ?? 'Belum Ada',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\target_indikator;
use App\Models\tahun_kerja;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanIkuRskExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahunId;
    protected $keyword;

    public function __construct($tahunId = null, $keyword = null)
    {
        $this->tahunId = $tahunId;
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
                'monitoring_iku_detail.mtid_status',
                'uk.unit_nama'
            )
            ->leftJoin('program_studi', 'program_studi.prodi_id', '=', 'target_indikator.prodi_id')
            ->leftJoin('indikator_kinerja', 'indikator_kinerja.ik_id', '=', 'target_indikator.ik_id')
            ->leftJoin('tahun_kerja', 'tahun_kerja.th_id', '=', 'target_indikator.th_id')
            ->leftJoin('monitoring_iku_detail', 'monitoring_iku_detail.ti_id', '=', 'target_indikator.ti_id')
            ->leftJoin('unit_kerja as uk', 'uk.unit_id', '=', 'indikator_kinerja.unit_id')
            ->where('program_studi.nama_prodi', 'Rekayasa Sistem Komputer');

        // Filter unit kerja kalau role = unit kerja
        if (Auth::user()->role === 'unit kerja') {
            $query->where('uk.unit_id', Auth::user()->unit_id);
        }

        // Filter tahun
        if ($this->tahunId) {
            $query->where('tahun_kerja.th_id', $this->tahunId);
        } else {
            $tahunAktif = tahun_kerja::where('th_is_aktif', 'y')->first();
            if ($tahunAktif) {
                $query->where('tahun_kerja.th_id', $tahunAktif->th_id);
            }
        }

        // Filter keyword
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
            'Unit Kerja',
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
            $target->unit_nama ?? '-',
            $target->ik_nama ?? '-',
            $target->ti_target ?? '-',
            $target->mtid_capaian ?? 'Belum Ada',
            $target->mtid_status ?? 'Belum Ada',
        ];
    }
}

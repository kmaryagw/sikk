<?php

namespace App\Exports;

use App\Models\RencanaKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanRenjaRskExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return RencanaKerja::with('unitKerja', 'tahunKerja', 'programStudis')
            ->whereHas('programStudis', function ($query) {
                $query->where('nama_prodi', 'Rekayasa Sistem Komputer');
            })
            ->get()
            ->map(function ($item) {
                return [
                    'Program Kerja' => $item->rk_nama,
                    'Unit Kerja' => $item->unitKerja->unit_nama ?? '-',
                    'Tahun' => $item->tahunKerja->th_tahun ?? '-',
                    'Anggaran' => $item->anggaran,
                ];
            });
    }

    public function headings(): array
    {
        return ['Program Kerja', 'Unit Kerja', 'Tahun', 'Anggaran'];
    }
}

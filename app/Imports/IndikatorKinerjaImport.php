<?php

namespace App\Imports;

use App\Models\IndikatorKinerja;
use App\Models\Standar;
use App\Models\UnitKerja;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;

class IndikatorKinerjaImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        if (!isset($row['kode_ikuikt'], $row['nama_ikuikt'], $row['standar'], $row['jenis'], $row['ketercapaian'])) {
            return; 
        }

        $standar = Standar::where('std_nama', trim($row['standar']))->first();
        if (!$standar) {
            return; 
        }

        $validKetercapaian = ['nilai', 'persentase', 'rasio', 'ada', 'ketersediaan'];
        $ketercapaian = strtolower(trim($row['ketercapaian']));

        if (!in_array($ketercapaian, $validKetercapaian)) {
            return; 
        }

        $indikator = IndikatorKinerja::updateOrCreate(
            ['ik_kode' => trim($row['kode_ikuikt'])], 
            [
                'ik_id'           => IndikatorKinerja::where('ik_kode', trim($row['kode_ikuikt']))->exists() 
                                     ? IndikatorKinerja::where('ik_kode', trim($row['kode_ikuikt']))->first()->ik_id 
                                     : Str::uuid()->toString(),
                'ik_nama'         => trim($row['nama_ikuikt']),
                'std_id'          => $standar->std_id,
                'ik_jenis'        => strtoupper(trim($row['jenis'])),
                'ik_ketercapaian' => $ketercapaian,
                'ik_is_aktif'     => isset($row['status_aktif_yn']) && strtolower(trim($row['status_aktif_yn'])) === 'y' ? 'y' : 'n',
            ]
        );

        if (!empty($row['unit_kerja_pic'])) {
            $unitNames = preg_split('/[,\/]/', $row['unit_kerja_pic']);
            
            $unitNames = array_map('trim', $unitNames);
            $unitNames = array_filter($unitNames);

            $unitIds = UnitKerja::whereIn('unit_nama', $unitNames)->pluck('unit_id');
            if ($unitIds->isNotEmpty()) {
                $indikator->unitKerja()->sync($unitIds);
            }
        }
    }
}
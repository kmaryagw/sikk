<?php

namespace App\Imports;

use App\Models\IndikatorKinerja;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class IndikatorKinerjaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan semua kolom tidak kosong sebelum menyimpan
        if (!isset($row['ik_kode']) || !isset($row['ik_nama']) || !isset($row['std_id']) || 
            !isset($row['ik_jenis']) || !isset($row['ik_baseline']) || !isset($row['ik_ketercapaian'])) {
            return null; // Lewati jika ada kolom yang kosong
        }

        return new IndikatorKinerja([
            'ik_id' => 'IK' . strtoupper(substr(md5(time()), 0, 8)), // Buat ID unik
            'ik_kode' => $row['ik_kode'],
            'ik_nama' => $row['ik_nama'],
            'std_id' => $row['std_id'],
            'ik_jenis' => strtoupper($row['ik_jenis']), 
            'ik_baseline' => $row['ik_baseline'],
            'ik_is_aktif' => strtolower($row['ik_is_aktif']) === 'y' ? 'y' : 'n',
            'ik_ketercapaian' => $row['ik_ketercapaian'],
        ]);
    }
}

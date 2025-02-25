<?php

namespace App\Imports;

use App\Models\IndikatorKinerja;
use App\Models\standar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class IndikatorKinerjaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validasi jika ada kolom kosong
        if (!isset($row['ik_kode'], $row['ik_nama'], $row['std_nama'], $row['ik_jenis'], $row['ik_baseline'], $row['ik_ketercapaian'])) {
            return null; // Lewati baris jika ada yang kosong
        }

        // Cari std_id berdasarkan std_nama
        $standar = standar::where('std_nama', $row['std_nama'])->first();

        if (!$standar) {
            return null; // Lewati jika Standar tidak ditemukan
        }

        $validKetercapaian = ['nilai', 'persentase', 'ketersediaan'];

        // Pastikan `ik_ketercapaian` memiliki nilai yang valid
        $ketercapaian = strtolower(trim($row['ik_ketercapaian']));
    
        // Jika isinya angka atau tidak valid, beri default atau abaikan
        if (!in_array($ketercapaian, $validKetercapaian)) {
            return null; // Lewati jika data tidak valid
        }
        return new IndikatorKinerja([
            'ik_id' => Str::uuid()->toString(), // Menggunakan UUID
            'ik_kode' => trim($row['ik_kode']),
            'ik_nama' => trim($row['ik_nama']),
            'std_id' => $standar->std_id, // Gunakan ID standar yang ditemukan
            'ik_jenis' => strtoupper(trim($row['ik_jenis'])),
            'ik_baseline' => trim($row['ik_baseline']),
            'ik_is_aktif' => strtolower(trim($row['ik_is_aktif'])) === 'y' ? 'y' : 'n',
            'ik_ketercapaian' => $ketercapaian, 
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrganisasiJabatan;
use Illuminate\Support\Str;

class OrganisasiJabatanSeeder extends Seeder

{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Level 1
            ['oj_nama' => 'Rektor', 'oj_kode' => 'INSTIKI', 'oj_induk' => null],

            // Level 2
            // ['oj_nama' => 'Ketua dan Sekretaris SA', 'oj_kode' => 'INSTIKI.SA', 'oj_induk' => 'INSTIKI.SA.ORG'],
            ['oj_nama' => 'Direktorat Penjaminan Mutu', 'oj_kode' => 'INSTIKI.DPM', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Wakil Rektor Bidang Pendidikan', 'oj_kode' => 'INSTIKI.R1', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Wakil Rektor Bidang Sumber Daya', 'oj_kode' => 'INSTIKI.R2', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Wakil Rektor Bidang Kemahasiswaan, INBIS, Humas & Kerjasama', 'oj_kode' => 'INSTIKI.R3', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Wakil Rektor Bidang Riset dan Inovasi', 'oj_kode' => 'INSTIKI.R4', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Fakultas Teknologi & Informatika (FTI)', 'oj_kode' => 'INSTIKI.F1', 'oj_induk' => 'INSTIKI'],
            ['oj_nama' => 'Fakultas Bisnis & Desain Kreatif (FBDK)', 'oj_kode' => 'INSTIKI.F2', 'oj_induk' => 'INSTIKI'],

            // SA
            ['oj_nama' => 'Ketua dan Sekretaris SA', 'oj_kode' => 'INSTIKI.SA', 'oj_induk' => 'INSTIKI.SA'],

            // WR 1
            ['oj_nama' => 'Direktorat Pengembangan Pendidikan', 'oj_kode' => 'INSTIKI.R1.D1', 'oj_induk' => 'INSTIKI.R1'],
            ['oj_nama' => 'Kepala Departemen Pengembangan dan Evaluasi Kurikulum', 'oj_kode' => 'INSTIKI.R1.D1.01', 'oj_induk' => 'INSTIKI.R1.D1'],
            ['oj_nama' => 'Kepala Departemen MBKM', 'oj_kode' => 'INSTIKI.R1.D1.02', 'oj_induk' => 'INSTIKI.R1.D1'],
            ['oj_nama' => 'Kepala Departemen Pusat Karir', 'oj_kode' => 'INSTIKI.R1.D1.03', 'oj_induk' => 'INSTIKI.R1.D1'],
            ['oj_nama' => 'Kepala Departemen Tracer Study', 'oj_kode' => 'INSTIKI.R1.D1.04', 'oj_induk' => 'INSTIKI.R1.D1'],
            ['oj_nama' => 'Biro Layanan Pendidikan', 'oj_kode' => 'INSTIKI.R1.B1', 'oj_induk' => 'INSTIKI.R1'],

            // WR 2
            ['oj_nama' => 'Direktorat Pengembangan Tenaga Pendidik dan Kependidikan', 'oj_kode' => 'INSTIKI.R2.D1', 'oj_induk' => 'INSTIKI.R2'],
            ['oj_nama' => 'Kepala Departemen Pengembangan Tenaga Pendidik', 'oj_kode' => 'INSTIKI.R2.D1.01', 'oj_induk' => 'INSTIKI.R2.D1'],
            ['oj_nama' => 'Kepala Departemen Pengembangan Tenaga Kependidikan', 'oj_kode' => 'INSTIKI.R2.D1.02', 'oj_induk' => 'INSTIKI.R2.D1'],
            ['oj_nama' => 'Direktorat Perencanaan dan Pengembangan Sarana-Prasarana', 'oj_kode' => 'INSTIKI.R2.D2', 'oj_induk' => 'INSTIKI.R2'],
            ['oj_nama' => 'Kepala Departemen Pengelolaan Laboratorium', 'oj_kode' => 'INSTIKI.R2.D2.01', 'oj_induk' => 'INSTIKI.R2.D2'],
            ['oj_nama' => 'Kepala Departemen Perencanaan Infrastruktur', 'oj_kode' => 'INSTIKI.R2.D2.02', 'oj_induk' => 'INSTIKI.R2.D2'],
            ['oj_nama' => 'Kepala Departemen Manajemen Sistem Informasi', 'oj_kode' => 'INSTIKI.R2.D2.03', 'oj_induk' => 'INSTIKI.R2.D2'],
            ['oj_nama' => 'Kepala Departemen Jaringan dan Keamanan Sistem Informasi', 'oj_kode' => 'INSTIKI.R2.D2.04', 'oj_induk' => 'INSTIKI.R2.D2'],
            ['oj_nama' => 'Biro Keuangan', 'oj_kode' => 'INSTIKI.R2.B1', 'oj_induk' => 'INSTIKI.R2'],
            ['oj_nama' => 'Biro Layanan Umum dan Sarana-Prasarana', 'oj_kode' => 'INSTIKI.R2.B2', 'oj_induk' => 'INSTIKI.R2'],
            ['oj_nama' => 'Administrasi Perpustakaan', 'oj_kode' => 'INSTIKI.R2.B2.01', 'oj_induk' => 'INSTIKI.R2.B2'],

            // WR 3
            ['oj_nama' => 'Direktorat Kemahasiswaan', 'oj_kode' => 'INSTIKI.R3.D1', 'oj_induk' => 'INSTIKI.R3'],
            ['oj_nama' => 'Kepala Departemen Pengembangan Karakter', 'oj_kode' => 'INSTIKI.R3.D1.01', 'oj_induk' => 'INSTIKI.R3.D1'],
            ['oj_nama' => 'Kepala Departemen Prestasi Mahasiswa', 'oj_kode' => 'INSTIKI.R3.D1.02', 'oj_induk' => 'INSTIKI.R3.D1'],
            ['oj_nama' => 'Direktorat Inkubator Bisnis', 'oj_kode' => 'INSTIKI.R3.D2', 'oj_induk' => 'INSTIKI.R3'],
            ['oj_nama' => 'Kepala Departemen Rekrutmen dan Pendampingan Tenant', 'oj_kode' => 'INSTIKI.R3.D2.01', 'oj_induk' => 'INSTIKI.R3.D2'],
            ['oj_nama' => 'Direktorat Humas dan Kerjasama', 'oj_kode' => 'INSTIKI.R3.D3', 'oj_induk' => 'INSTIKI.R3'],
            ['oj_nama' => 'Kepala Departemen Marketing dan Branding', 'oj_kode' => 'INSTIKI.R3.D3.01', 'oj_induk' => 'INSTIKI.R3.D3'],
            ['oj_nama' => 'Kepala Departemen Kerjasama', 'oj_kode' => 'INSTIKI.R3.D3.02', 'oj_induk' => 'INSTIKI.R3.D3'],
            ['oj_nama' => 'Biro Kemahasiswaan, INBIS, Humas & Kerjasama', 'oj_kode' => 'INSTIKI.R3.B1', 'oj_induk' => 'INSTIKI.R3'],

            // WR 4
            ['oj_nama' => 'Direktorat Riset & Pengabdian Masyarakat', 'oj_kode' => 'INSTIKI.R4.D1', 'oj_induk' => 'INSTIKI.R4'],
            ['oj_nama' => 'Kepala Departemen Pengelolaan Riset', 'oj_kode' => 'INSTIKI.R4.D1.01', 'oj_induk' => 'INSTIKI.R4.D1'],
            ['oj_nama' => 'Kepala Departemen PkM', 'oj_kode' => 'INSTIKI.R4.D1.02', 'oj_induk' => 'INSTIKI.R4.D1'],
            ['oj_nama' => 'Kepala Departemen Riset Kolaborasi Internasional', 'oj_kode' => 'INSTIKI.R4.D1.03', 'oj_induk' => 'INSTIKI.R4.D1'],
            ['oj_nama' => 'Kepala Departemen Pengelolaan Pusat Studi', 'oj_kode' => 'INSTIKI.R4.D1.04', 'oj_induk' => 'INSTIKI.R4.D1'],
            ['oj_nama' => 'Kepala Departemen Publikasi Ilmiah dan Publisher', 'oj_kode' => 'INSTIKI.R4.D1.05', 'oj_induk' => 'INSTIKI.R4.D1'],
            ['oj_nama' => 'Direktorat Pengembangan Inovasi', 'oj_kode' => 'INSTIKI.R4.D2', 'oj_induk' => 'INSTIKI.R4'],
            ['oj_nama' => 'Kepala Departemen Pengelolaan Proyek', 'oj_kode' => 'INSTIKI.R4.D2.01', 'oj_induk' => 'INSTIKI.R4.D2'],
            ['oj_nama' => 'Kepala Departemen Manajemen Inovasi', 'oj_kode' => 'INSTIKI.R4.D2.02', 'oj_induk' => 'INSTIKI.R4.D2'],
            ['oj_nama' => 'Kepala Departemen Pengelolaan Sentra HKI', 'oj_kode' => 'INSTIKI.R4.D2.03', 'oj_induk' => 'INSTIKI.R4.D2'],
            ['oj_nama' => 'Biro Administrasi Riset & Inovasi', 'oj_kode' => 'INSTIKI.R4.B1', 'oj_induk' => 'INSTIKI.R4'],

            // FTI
            ['oj_nama' => 'Dekan FTI', 'oj_kode' => 'INSTIKI.F1', 'oj_induk' => 'INSTIKI.F1'],
            ['oj_nama' => 'Koordinator Program Studi Teknik Informatika', 'oj_kode' => 'INSTIKI.F1.P1', 'oj_induk' => 'INSTIKI.F1'],
            ['oj_nama' => 'Koordinator Program Studi Sistem Komputer', 'oj_kode' => 'INSTIKI.F1.P2', 'oj_induk' => 'INSTIKI.F1'],
            ['oj_nama' => 'Kepala Biro Administrasi Akademik Fakultas FTI', 'oj_kode' => 'INSTIKI.F1.B1', 'oj_induk' => 'INSTIKI.F1'],

            // FBDK
            ['oj_nama' => 'Dekan FBDK', 'oj_kode' => 'INSTIKI.F2', 'oj_induk' => 'INSTIKI.F2'],
            ['oj_nama' => 'Koordinator Program Studi Bisnis Digital (BD)', 'oj_kode' => 'INSTIKI.F2.P1', 'oj_induk' => 'INSTIKI.F2'],
            ['oj_nama' => 'Koordinator Program Studi Desain Komunikasi Visual (DKV)', 'oj_kode' => 'INSTIKI.F2.P2', 'oj_induk' => 'INSTIKI.F2'],
            ['oj_nama' => 'Kepala Biro Administrasi Akademik Fakultas FBDK', 'oj_kode' => 'INSTIKI.F2.B1', 'oj_induk' => 'INSTIKI.F2'],
        ];

        $kodeToId = [];

        foreach ($data as $item) {
            $oj = OrganisasiJabatan::updateOrCreate(
                ['oj_id' => $item['oj_kode']],     
                [
                    'oj_nama'               => $item['oj_nama'],
                    'oj_kode'               => $item['oj_kode'],
                    'oj_induk'              => null,      
                    'oj_mengeluarkan_nomor' => 'y',
                    'oj_status'             => 'y',
                ]
            );
        
            // Simpan mapping kode → id (di sini id == kode karena kita pakai kode sebagai oj_id)
            $kodeToId[$item['oj_kode']] = $oj->oj_id;
        }        

        foreach ($data as $item) {
            if ($item['oj_induk']) {
                OrganisasiJabatan::where('oj_kode', $item['oj_kode'])->update([
                    'oj_induk' => $kodeToId[$item['oj_induk']] ?? null,
                ]);
            }
        }
    }
}


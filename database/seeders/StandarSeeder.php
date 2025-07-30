<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StandarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('standar')->insert([
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'std_nama' => 'Standar Dosen Dan Tenaga Kependidikan',
            'std_deskripsi' => 'Dosen Institut Bisnis dan Teknologi Indonesia wajib memenuhi beberapa kriteria dan kemampuan untuk menyelenggarakan pendidikan dalam rangka pemenuhan capaian pembelajaran lulusan: dalam keadaan sehat jasmani dan rohani; memiliki kualifikasi akademik paling rendah lulusan magister atau magister terapan yang relevan dengan program studi; dosen program diploma tiga dan sarjana pada poin sebelumnya dapat menggunakan dosen bersertifikat profesi yang relevan dengan program studi dan berkualifikasi paling rendah setara dengan jenjang 8 (delapan) KKNI yang dilakukan oleh Direktur Jenderal Pembelajaran dan Kemahasiswaan melalui mekanisme rekognisi pembelajaran lampau; dosen tetap program studi wajib memiliki jabatan fungsional akademik; serta memiliki sertifikat pendidik dan/atau sertifikat profesi yang relevan dengan program studi.',
            'std_url' => 'https://elsa.instiki.ac.id/my/',
            'std_kategori' => 'Masukan Pendidikan',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

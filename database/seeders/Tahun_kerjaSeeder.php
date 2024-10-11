<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tahun_kerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_kerja')->insert([
                'th_id' => 1,
                'th_tahun' => 2023,
                'ren_is_aktif' => 'y',
                'ren_id' => 'REN001',
            ]);
    }
}

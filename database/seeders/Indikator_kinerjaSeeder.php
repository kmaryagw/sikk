<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Indikator_kinerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IK001',
            'ik_nama' => 'Indikator Kinerja 1',
            'std_id' => 'STD001',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Target_indikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('target_indikator')->insert([
                'ti_id' => 1,
                'ik_id' => 'IK001',
                'ti_target' => '80%',
                'ti_keterangan' => 'Target pencapaian 80% untuk indikator ini',
                'prodi_id' => '1',
                'th_id' => '1',
            ]);
    }
}

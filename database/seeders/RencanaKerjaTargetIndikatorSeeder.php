<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RencanaKerjaTargetIndikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rencana_kerja_target_indikator')->insert([
            'rkti_id' => '1',
            'rk_id' => 'RK1D8AF05A0CCC61BA70294829D2313168',
            'ti_id' => 'TC6B8CE3B7A60BE6057CC704E3B498C96A',
        ]);
    }
}

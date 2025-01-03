<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Periode_monevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('periode_monev')->insert([
            'pm_id' => 'PM6EGEFUYWERFJHREFUB874R857B78F4U7',
            'pm_nama' => 'Q1',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('periode_monev')->insert([
            'pm_id' => 'PMB53161D39C75013BF12F39B41FA93915',
            'pm_nama' => 'Q2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('periode_monev')->insert([
            'pm_id' => 'PM6DA104A10B4F0DED85F92F877AF01684',
            'pm_nama' => 'Q3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('periode_monev')->insert([
            'pm_id' => 'PM0A1C8847BC9316A6FC058F47C1EC7682',
            'pm_nama' => 'Q4',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

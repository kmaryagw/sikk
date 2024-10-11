<?php

namespace Database\Seeders;

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
                'pm_id' => 'PM001',
                'pm_nama' => 'Periode Monev 2024',
            ]);
    }
}

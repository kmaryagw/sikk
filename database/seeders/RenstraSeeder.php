<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RenstraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('renstra')->insert([
            'ren_id' => 'REN001',
                'ren_nama' => 'Rencana Strategis 2024',
                'ren_pimpinan' => 'Dr. John Doe',
                'ren_periode_awal' => 2024,
                'ren_periode_akhir' => 2028,
                'ren_is_aktif' => 'y',
                'create_date' => Carbon::now(),
                'update_date' => Carbon::now(),

        ]);
    }
}

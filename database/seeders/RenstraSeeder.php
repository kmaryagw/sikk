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
            'ren_id' => 'REN7EJHFWGERU3R34TGWRF7TGRJFGWJHRGF2',
            'ren_nama' => 'RENSTRA 2024-2028',
            'ren_pimpinan' => 'I Dewa Made Krishna Muku, M.T.',
            'ren_periode_awal' => '2024',
            'ren_periode_akhir' => '2028',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

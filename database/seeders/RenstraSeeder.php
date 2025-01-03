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
            'ren_nama' => 'RENSTRA 2024',
            'ren_pimpinan' => '-',
            'ren_periode_awal' => '2024',
            'ren_periode_akhir' => '2025',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('renstra')->insert([
            'ren_id' => 'RENE294010143ECE84328A7E0BBF4196DD8',
            'ren_nama' => 'RENSTRA 2025',
            'ren_pimpinan' => '-',
            'ren_periode_awal' => '2025',
            'ren_periode_akhir' => '2026',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('renstra')->insert([
            'ren_id' => 'REN4BA402CAE62EF89B8F476EA848B7B901',
            'ren_nama' => 'RENSTRA 2026',
            'ren_pimpinan' => '-',
            'ren_periode_awal' => '2026',
            'ren_periode_akhir' => '2027',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('renstra')->insert([
            'ren_id' => 'REN5BE9444768F9B591C0821E13CC57B32D',
            'ren_nama' => 'RENSTRA 2027',
            'ren_pimpinan' => '-',
            'ren_periode_awal' => '2027',
            'ren_periode_akhir' => '2028',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('renstra')->insert([
            'ren_id' => 'REN84GR2YUUIEFBWHBEFHWEFUREFIR8HJ',
            'ren_nama' => 'RENSTRA 2028',
            'ren_pimpinan' => '-',
            'ren_periode_awal' => '2028',
            'ren_periode_akhir' => '2029',
            'ren_is_aktif' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

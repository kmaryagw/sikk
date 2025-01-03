<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
            'th_id' => 'TH6B273BCF126E6449EC9E1933BCA12D2F',
            'th_tahun' => '2024',
            'th_is_aktif' => 'y',
            'ren_id' => 'REN7EJHFWGERU3R34TGWRF7TGRJFGWJHRGF2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('tahun_kerja')->insert([
            'th_id' => 'TH0B86521B7D919E1B27229579758583E8',
            'th_tahun' => '2025',
            'th_is_aktif' => 'n',
            'ren_id' => 'RENE294010143ECE84328A7E0BBF4196DD8',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('tahun_kerja')->insert([
            'th_id' => 'TH99A257C8B78234834F4542A5147D57FC',
            'th_tahun' => '2026',
            'th_is_aktif' => 'n',
            'ren_id' => 'REN4BA402CAE62EF89B8F476EA848B7B901',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('tahun_kerja')->insert([
            'th_id' => 'TH4E9A2073726C0714AF82D0D70CE406C5',
            'th_tahun' => '2027',
            'th_is_aktif' => 'n',
            'ren_id' => 'REN5BE9444768F9B591C0821E13CC57B32D',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('tahun_kerja')->insert([
            'th_id' => 'TH4E91E8B17F8A27BCBAC176B1FA10EF52',
            'th_tahun' => '2028',
            'th_is_aktif' => 'n',
            'ren_id' => 'REN84GR2YUUIEFBWHBEFHWEFUREFIR8HJ',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);
    }
}

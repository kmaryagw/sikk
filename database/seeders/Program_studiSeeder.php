<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Program_studiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('program_studi')->insert([
            'prodi_id' => 'PR66A330D9AA18520C38F84970A3D4541C',
            'nama_prodi' => 'Rekayasa Sistem Komputer',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PR777A48667363814E42914D7CABEA4A68',
            'nama_prodi' => 'Desain Komunikasi Visual',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PR88857BD83DF08496600380EF34E3BE9E',
            'nama_prodi' => 'Informatika',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PRB7BDE07D1577C5E5F1B54352C2420DF4',
            'nama_prodi' => 'Bisnis Digital',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

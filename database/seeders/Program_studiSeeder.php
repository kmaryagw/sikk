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
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541A',
            'nama_prodi' => 'Rekayasa Sistem Komputer',
            'singkatan_prodi' => 'RSK',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PR777A48667363814E42914D7CABEA4A68',
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541B',
            'nama_prodi' => 'Desain Komunikasi Visual',
            'singkatan_prodi' => 'DKV',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PR88857BD83DF08496600380EF34E3BE9E',
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541A',
            'nama_prodi' => 'Informatika',
            'singkatan_prodi' => 'IF',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('program_studi')->insert([
            'prodi_id' => 'PRB7BDE07D1577C5E5F1B54352C2420DF4',
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541B',
            'nama_prodi' => 'Bisnis Digital',
            'singkatan_prodi' => 'BD',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

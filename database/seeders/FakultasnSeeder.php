<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakultasnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fakultasn')->insert([
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541A',
            'nama_fakultas' => 'Teknologi dan Informatika',  
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('fakultasn')->insert([
            'id_fakultas' => 'FK66A330D9AA18520C38F84970A3D4541B',
            'nama_fakultas' => 'Bisnis dan Desain Kreatif',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

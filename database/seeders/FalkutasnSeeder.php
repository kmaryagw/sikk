<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FalkutasnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('falkutasn')->insert([
            
            'id_falkutas' => 'FK66A330D9AA18520C38F84970A3D4541A',
            'nama_falkutas' => 'TI',
            
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('falkutasn')->insert([
            
            'id_falkutas' => 'FK66A330D9AA18520C38F84970A3D4541B',
            'nama_falkutas' => 'BD',
            
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('falkutasn')->insert([
            
            'id_falkutas' => 'FK66A330D9AA18520C38F84970A3D4541D',
            'nama_falkutas' => 'RSK',
            
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('falkutasn')->insert([
            
            'id_falkutas' => 'FK66A330D9AA18520C38F84970A3D4541E',
            'nama_falkutas' => 'DGM',
            
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

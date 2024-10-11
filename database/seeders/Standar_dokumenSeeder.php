<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Standar_dokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Standar_dokumen')->insert([
            'std_id' => 'STD001',
            'stdd_id' => 'STDD001',
            'stdd_file' => 'document1',
        ]);
    }
}

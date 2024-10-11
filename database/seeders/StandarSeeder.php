<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('standar')->insert([
            'std_id' => 'STD001',
            'std_nama' => 'Standar Pendidikan',
            'std_deskripsi' => 'Deskripsi untuk standar pendidikan.',
        ]);
    }
}

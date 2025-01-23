<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StandarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('standar')->insert([
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'std_nama' => 'STANDAR 1',
            'std_deskripsi' => '-',
            'std_url' => 'https://elsa.instiki.ac.id/my/',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);
    }
}

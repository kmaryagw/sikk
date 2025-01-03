<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id_user' => 'US279DJFSD87HFDGSF8G9YSFH847HSD734',
            'username' => 'admin',
            'status' => '1',
            'password' => hash::make('admin'),
            'role'=>'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'id_user' => 'US2CBA43FF5C76829E891323DA14F3C0F5',
            'username' => 'informatika',
            'status' => '0',
            'password' => hash::make('informatika'),
            'role'=>'prodi',
            'prodi_id' => 'PR88857BD83DF08496600380EF34E3BE9E',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'id_user' => 'US4BF830807E0EF55EC86F9BD8C766CCB1',
            'username' => 'DPM',
            'status' => '0',
            'password' => hash::make('dpm'),
            'role'=>'unit kerja',
            'unit_id' => 'UK8113820D89CFCA8431D9DEC87A0F7A2E',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

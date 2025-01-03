<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unit_kerja')->insert([
            'unit_id' => 'UK54E2CFB17D83E299FA8F471CF8733B7E',
            'unit_nama' => 'WR2',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('unit_kerja')->insert([
            'unit_id' => 'UK1B16CC1326BF17FDE08CAE04F9058192',
            'unit_nama' => 'WR1',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('unit_kerja')->insert([
            'unit_id' => 'UK565BE1772A30D599765FBEDDE099A724',
            'unit_nama' => 'Dekanat',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('unit_kerja')->insert([
            'unit_id' => 'UK8113820D89CFCA8431D9DEC87A0F7A2E',
            'unit_nama' => 'DPM',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('unit_kerja')->insert([
            'unit_id' => 'UKBFFD3F4ADB4436664F96BD52BB798A6C',
            'unit_nama' => 'WR3',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('unit_kerja')->insert([
            'unit_id' => 'UKF974DA83DEB9418787680C86E4370F06',
            'unit_nama' => 'BAAK',
            'unit_kerja' => 'y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

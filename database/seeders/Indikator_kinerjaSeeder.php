<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Indikator_kinerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IKBF0E6E4C6DDC39837EFA11A4AE2DE426',
            'ik_kode' => 'IKU001',
            'ik_nama' => 'INDIKATOR KINERJA UTAMA 1',
            'ik_jenis' => 'IKU',
            'ik_ketercapaian' => 'persentase',
            'ik_baseline' => '50',
            'ik_is_aktif'=> 'y',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IK987DSIFHSEUR47T94HUIGRBS48TU438',
            'ik_kode' => 'IKU002',
            'ik_nama' => 'INDIKATOR KINERJA UTAMA 2',
            'ik_jenis' => 'IKU',
            'ik_ketercapaian' => 'persentase',
            'ik_baseline' => '60',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IKRTJSEIGO9484T89WF948TQ5JRT48W45R',
            'ik_kode' => 'IKU003',
            'ik_nama' => 'INDIKATOR KINERJA UTAMA 3',
            'ik_jenis' => 'IKU',
            'ik_ketercapaian' => 'nilai',
            'ik_baseline' => '5',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IK948HWUEHISBET7Y94Q7PTSRGHRGHBR',
            'ik_kode' => 'IKU004',
            'ik_nama' => 'INDIKATOR KINERJA UTAMA 4',
            'ik_jenis' => 'IKU',
            'ik_ketercapaian' => 'ketersediaan',
            'ik_baseline' => 'draft',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IKROIS9845URHSERIT8S943TSI3984YHT',
            'ik_kode' => 'IKT001',
            'ik_nama' => 'INDIKATOR KINERJA TAMBAHAN 1',
            'ik_jenis' => 'IKT',
            'ik_ketercapaian' => 'persentase',
            'ik_baseline' => '50',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IKHRUYTI8Y4URG7843BRHBSW7437T5784',
            'ik_kode' => 'IKT002',
            'ik_nama' => 'INDIKATOR KINERJA TAMBAHAN 2',
            'ik_jenis' => 'IKT',
            'ik_ketercapaian' => 'persentase',
            'ik_baseline' => '60',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IK908RSTHJAW4TIYA4RBSG7BJDFHJGSRG',
            'ik_kode' => 'IKT003',
            'ik_nama' => 'INDIKATOR KINERJA TAMBAHAN 3',
            'ik_jenis' => 'IKT',
            'ik_ketercapaian' => 'nilai',
            'ik_baseline' => '5',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);

        DB::table('indikator_kinerja')->insert([
            'ik_id' => 'IKOAEORTOAIERT834BTJK8RIAE47T8BT',
            'ik_kode' => 'IKT004',
            'ik_nama' => 'INDIKATOR KINERJA TAMBAHAN 4',
            'ik_jenis' => 'IKT',
            'ik_ketercapaian' => 'ketersediaan',
            'ik_baseline' => 'draft',
            'ik_is_aktif'=> 'n',
            'std_id' => 'STDB04DDEAC3765BE32BE71700C36A6EE7B',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(), 
        ]);
    }
}

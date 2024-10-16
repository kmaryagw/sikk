<?php

namespace Database\Seeders;

use App\Models\RencanaKerjaPelaksanaan;
use App\Models\RencanaKerjaTargetIndikator;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'nama' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            Program_studiSeeder::class,
            UnitKerjaSeeder::class,
            UserSeeder::class,
            Tahun_kerjaSeeder::class,
            StandarSeeder::class,
            Indikator_kinerjaSeeder::class,
            Target_indikatorSeeder::class,
            Periode_monevSeeder::class,
            Standar_dokumenSeeder::class,
            EvaluasiSeeder::class,
            RencanaKerjaTargetIndikatorSeeder::class,
            RencanaKerjaSeeder::class,
            MonitoringSeeder::class,
            RencanaKerjaPelaksanaanSeeder::class,
        ]);
    }

}

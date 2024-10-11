<?php

namespace Database\Seeders;

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
            UserSeeder::class,
            Program_studiSeeder::class,
            UnitKerjaSeeder::class,
            Tahun_kerjaSeeder::class,
            Target_indikatorSeeder::class,
            Indikator_kinerjaSeeder::class,
            StandarSeeder::class,
            Periode_monevSeeder::class,
            Standar_dokumenSeeder::class,
        ]);
    }

}

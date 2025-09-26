<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data lama dulu (opsional, hati-hati di production)
        Announcement::truncate();

        // Pengumuman utama
        Announcement::create([
            'title' => 'Pengumuman Utama: Perubahan Jadwal Kuliah',
            'summary' => 'Diberitahukan bahwa jadwal kuliah semester ini mengalami perubahan mulai tanggal 1 Oktober.',
            'date' => Carbon::now()->subDays(1),
            'image' => null, // bisa diisi path gambar kalau ada
            'is_main' => true,
        ]);

        // Pengumuman lain (dummy 10 data)
        for ($i = 1; $i <= 10; $i++) {
            Announcement::create([
                'title' => 'Pengumuman Penting #' . $i,
                'summary' => 'Ini adalah ringkasan pengumuman ke-' . $i . ' yang dibuat sebagai data dummy.',
                'date' => Carbon::now()->subDays($i + 1),
                'image' => null,
                'is_main' => false,
            ]);
        }
    }
}

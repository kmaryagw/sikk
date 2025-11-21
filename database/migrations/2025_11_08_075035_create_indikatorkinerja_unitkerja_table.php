<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Buat tabel pivot
        Schema::create('indikatorkinerja_unitkerja', function (Blueprint $table) {
            $table->uuid('ik_id');
            $table->uuid('unit_id');

            $table->foreign('ik_id')
                ->references('ik_id')
                ->on('indikator_kinerja')
                ->onDelete('cascade');

            $table->foreign('unit_id')
                ->references('unit_id')
                ->on('unit_kerja')
                ->onDelete('cascade');

            $table->primary(['ik_id', 'unit_id']);
        });

        // 2️⃣ Migrasi data lama (jika masih ada kolom unit_id di tabel indikator_kinerja)
        if (Schema::hasColumn('indikator_kinerja', 'unit_id')) {
            $oldData = DB::table('indikator_kinerja')
                        ->whereNotNull('unit_id')
                        ->select('ik_id', 'unit_id')
                        ->get();

            foreach ($oldData as $row) {
                DB::table('indikatorkinerja_unitkerja')->insert([
                    'ik_id' => $row->ik_id,
                    'unit_id' => $row->unit_id,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('indikatorkinerja_unitkerja');
    }
};


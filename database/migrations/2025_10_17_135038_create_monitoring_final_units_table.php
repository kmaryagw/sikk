<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monitoring_final_units', function (Blueprint $table) {
            $table->id();

            // ✅ Sesuaikan tipe data dan panjang
            $table->string('monitoring_iku_id', 50);
            $table->string('unit_id', 50);

            $table->boolean('status')->default(false);
            $table->string('finalized_by', 50)->nullable();
            $table->timestamp('finalized_at')->nullable();

            // ✅ Foreign key cocok dengan tabel monitoring_iku
            $table->foreign('monitoring_iku_id')
                  ->references('mti_id')
                  ->on('monitoring_iku')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // ✅ Foreign key cocok dengan tabel unit_kerja
            $table->foreign('unit_id')
                  ->references('unit_id')
                  ->on('unit_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // ✅ Foreign key cocok dengan tabel users (id_user)
            $table->foreign('finalized_by')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_final_units');
    }
};
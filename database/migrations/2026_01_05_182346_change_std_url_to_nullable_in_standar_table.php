<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk mengubah kolom menjadi nullable.
     */
    public function up(): void
    {
        Schema::table('standar', function (Blueprint $table) {
            $table->text('std_url')->nullable()->change();
        });
    }

    /**
     * Mengembalikan perubahan (rollback).
     */
    public function down(): void
    {
        Schema::table('standar', function (Blueprint $table) {
            $table->text('std_url')->nullable(false)->change();
        });
    }
};
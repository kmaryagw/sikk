<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id_user', 50)->primary();
            $table->string('username', 255);
            $table->string('nama', 255)->nullable();
            $table->string('email', 255)->nullable(); 
            $table->string('password', 255);
            $table->string('status', 1);
            $table->enum('role', ['admin', 'prodi', 'unit kerja', 'fakultas']);

             // Menambahkan kolom prodi_id dan unit_kerja_id
            $table->string('prodi_id', 50)->nullable(); // menempatkan setelah kolom 'role'
            $table->string('unit_id', 50)->nullable();
            $table->string('id_fakultas', 50)->nullable();; // menempatkan setelah kolom 'prodi_id'
 
             // Menambahkan foreign key constraint
            $table->foreign('prodi_id')->references('prodi_id')->on('program_studi')->onDelete('set null');
            $table->foreign('unit_id')->references('unit_id')->on('unit_kerja')->onDelete('set null');
            $table->foreign('id_fakultas')->references('id_fakultas')->on('fakultasn')->onDelete('set null');

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign(['prodi_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['id_fakultas']);
            $table->dropColumn(['prodi_id', 'unit_id', 'id_fakultas']);
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

    }
};

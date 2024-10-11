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
            $table->string('password', 255);
            $table->string('status', 1);
            $table->enum('role', ['admin', 'prodi', 'unit kerja']);

             // Menambahkan kolom prodi_id dan unit_kerja_id
             $table->string('id_prodi', 50)->nullable(); // menempatkan setelah kolom 'role'
             $table->string('id_unit_kerja', 50)->nullable();; // menempatkan setelah kolom 'prodi_id'
 
             // Menambahkan foreign key constraint
             $table->foreign('id_prodi')->references('id_prodi')->on('prodi')->onDelete('set null');
             $table->foreign('id_unit_kerja')->references('id_unit_kerja')->on('unit_kerja')->onDelete('set null');

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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        Schema::table('users', function (Blueprint $table) {
            // Menghapus foreign key dan kolom
            $table->dropForeign(['id_prodi']);
            $table->dropForeign(['id_unit_kerja']);
            $table->dropColumn(['id_prodi', 'id_unit_kerja']);
        });

    }
};

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
        Schema::create('standar', function (Blueprint $table) {
            
                $table->string('std_id', 50)->primary(); 
                $table->string('std_nama', 20);
                $table->text('std_deskripsi');
                $table->text('std_url');
                $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standar');
    }
};

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
        Schema::create('bahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan'); // Nama bahan unik
            $table->string('satuan'); // Satuan bahan
            $table->timestamps();

            // Pastikan kombinasi nama_bahan dan satuan unik
            $table->unique(['nama_bahan', 'satuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};

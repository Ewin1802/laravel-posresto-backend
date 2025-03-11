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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan')->nullable();
            $table->foreignId('bahan_id')->nullable()->constrained('bahans')->onDelete('set null');
            $table->decimal('amount', 10, 2); // Jumlah uang transaksi
            $table->timestamp('tanggal_masuk');
            $table->integer('saldo_awal'); // jumlah pembelian pertama
            $table->integer('quantity'); // Jumlah bahan dalam satuan tertentu (kg, liter, pcs)
            $table->string('satuan'); // Satuan (kg, liter, pcs, dll.)
            $table->string('supplier')->nullable(); // Nama supplier (untuk transaksi masuk)
            $table->string('receiver')->nullable(); // Nama penerima bahan (untuk transaksi keluar)
            $table->text('description')->nullable(); // Keterangan transaksi
            $table->string('image')->nullable(); // Bukti transaksi (foto nota, faktur, atau bahan)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengganti modul persediaan (bahans, inventories, inventory_outs) dengan expenses.
 *
 * Aman dijalankan pada kondisi apa pun:
 * - tabel lama sudah dihapus manual  -> langkah drop dilewati;
 * - tabel lama masih ada tapi kosong  -> dihapus;
 * - tabel lama masih berisi data      -> berhenti tanpa mengubah apa pun;
 * - expenses sudah ada                -> tidak dibuat ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['inventory_outs', 'inventories'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                throw new RuntimeException(
                    "Tabel {$table} masih berisi data. Backup/kosongkan dulu sebelum menjalankan migrasi ini."
                );
            }
        }

        // Urutan penting karena foreign key: inventory_outs -> inventories -> bahans.
        Schema::dropIfExists('inventory_outs');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('bahans');

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->string('description');
                $table->decimal('amount', 15, 2);
                $table->date('expense_date');
                $table->text('notes')->nullable();
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
                $table->timestamps();
                $table->index('category');
                $table->index('expense_date');
            });
        }

        // Bersihkan catatan migrasi lama yang filenya sudah dihapus,
        // agar `php artisan migrate:status` rapi dan rollback tidak mencari file yang hilang.
        if (Schema::hasTable('migrations')) {
            DB::table('migrations')->whereIn('migration', [
                '2025_03_11_211502_create_bahans_table',
                '2025_03_11_211622_create_inventories_table',
                '2025_03_11_211722_create_inventory_outs_table',
            ])->delete();
        }
    }

    public function down(): void
    {
        // Modul persediaan sudah dihapus permanen; rollback hanya menghapus expenses.
        Schema::dropIfExists('expenses');
    }
};

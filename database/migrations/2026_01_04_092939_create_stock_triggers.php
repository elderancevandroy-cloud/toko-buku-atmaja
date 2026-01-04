<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create trigger to increase stock when purchase is made
        DB::unprepared('
            CREATE TRIGGER tambah_stok 
            AFTER INSERT ON pembelian
            FOR EACH ROW
            BEGIN
                UPDATE buku 
                SET stok = stok + NEW.jumlah 
                WHERE id = NEW.buku_id;
            END
        ');

        // Create trigger to decrease stock when sale detail is created
        DB::unprepared('
            CREATE TRIGGER stok_berkurang 
            AFTER INSERT ON detail_penjualan
            FOR EACH ROW
            BEGIN
                UPDATE buku 
                SET stok = stok - NEW.jumlah 
                WHERE id = NEW.buku_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tambah_stok');
        DB::unprepared('DROP TRIGGER IF EXISTS stok_berkurang');
    }
};

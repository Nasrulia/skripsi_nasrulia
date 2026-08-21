<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->integer('berat_gram')->default(1000)->after('harga_jual');
            $table->enum('ukuran_packing', ['kecil', 'sedang', 'besar', 'ekstra_besar'])->default('kecil')->after('berat_gram');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['berat_gram', 'ukuran_packing']);
        });
    }
};

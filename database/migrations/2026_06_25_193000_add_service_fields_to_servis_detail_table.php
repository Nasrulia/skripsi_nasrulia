<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->foreignId('jasa_servis_id')->nullable()->change();
            $table->string('nama_barang')->nullable()->after('jasa_servis_id');
            $table->decimal('estimasi_biaya', 15, 2)->default(0)->after('keluhan');
            $table->string('estimasi_waktu')->nullable()->after('estimasi_biaya');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['transfer', 'cash'])->default('transfer')->after('total_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });

        Schema::table('servis_detail', function (Blueprint $table) {
            $table->foreignId('jasa_servis_id')->nullable(false)->change();
            $table->dropColumn(['nama_barang', 'estimasi_biaya', 'estimasi_waktu']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('layanan_ekspedisi')->nullable()->after('ekspedisi_id');
            $table->decimal('biaya_packing', 15, 2)->default(0)->after('ongkir');
            $table->string('provinsi_tujuan')->nullable()->after('alamat_pengiriman');
            $table->string('kota_tujuan')->nullable()->after('provinsi_tujuan');
            $table->integer('berat_total_gram')->default(1000)->after('jarak_km');
            $table->string('estimasi_pengiriman')->nullable()->after('layanan_ekspedisi');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn([
                'layanan_ekspedisi',
                'biaya_packing',
                'provinsi_tujuan',
                'kota_tujuan',
                'berat_total_gram',
                'estimasi_pengiriman',
            ]);
        });
    }
};

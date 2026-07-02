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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dateTime('estimasi_diambil')->nullable()->after('metode_pengambilan');
            $table->decimal('nominal_dp', 15, 2)->default(0)->after('estimasi_diambil');
            $table->dateTime('batas_waktu_pengambilan')->nullable()->after('nominal_dp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['estimasi_diambil', 'nominal_dp', 'batas_waktu_pengambilan']);
        });
    }
};

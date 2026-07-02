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
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->decimal('upah_teknisi', 15, 2)->default(0)->after('estimasi_waktu');
            $table->decimal('keuntungan_toko', 15, 2)->default(0)->after('upah_teknisi');
        });

        // Initialize existing records with 50/50 split
        \DB::statement('UPDATE servis_detail SET upah_teknisi = estimasi_biaya * 0.5, keuntungan_toko = estimasi_biaya * 0.5');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->dropColumn(['upah_teknisi', 'keuntungan_toko']);
        });
    }
};

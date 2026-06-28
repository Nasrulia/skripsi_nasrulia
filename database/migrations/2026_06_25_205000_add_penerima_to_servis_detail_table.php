<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('teknisi_id');
        });
    }

    public function down(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->dropColumn('penerima');
        });
    }
};

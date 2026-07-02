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
         Schema::create('komplain', function (Blueprint $table) {
             $table->id();
             $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->onDelete('cascade');
             $table->string('kode_transaksi');
             $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
             $table->string('nama_pelanggan');
             $table->string('no_whatsapp');
             $table->text('deskripsi');
             $table->enum('tipe', ['penjualan', 'servis']);
             $table->enum('status', ['pending', 'selesai'])->default('pending');
             $table->timestamps();
         });
     }
 
     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
         Schema::dropIfExists('komplain');
     }
 };

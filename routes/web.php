<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\AturanChatbotController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\TeknisiManageController;
use App\Http\Controllers\RajaOngkirController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

// Public Service Status Check
Route::get('/cek-servis', [TeknisiController::class, 'cekStatusPublic'])->name('cek-servis.public');
Route::post('/cek-servis', [TeknisiController::class, 'prosesCekStatusPublic'])->name('cek-servis.post');
Route::post('/cek-servis/upload-pembayaran/{id}', [TeknisiController::class, 'uploadPembayaranPublic'])->name('cek-servis.upload-pembayaran');
Route::post('/cek-servis/ubah-metode/{id}', [TeknisiController::class, 'ubahMetodePembayaranPublic'])->name('cek-servis.ubah-metode');
Route::get('/cek-servis/nota/{id}', [TeknisiController::class, 'unduhNotaServisPublic'])->name('cek-servis.nota');

// Public Chatbot AI
Route::get('/konsultasi', function () { return view('chatbot.index'); })->name('konsultasi');
Route::post('/api/chat', [ChatbotController::class, 'getResponse'])->name('api.chat');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/statistiks', [App\Http\Controllers\DashboardController::class, 'getStatistiks'])->name('dashboard.statistiks');


    // 1. AKSES KHUSUS ADMIN (Master Data)
    Route::middleware(['peran:admin'])->group(function () {
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('ekspedisi', EkspedisiController::class);
        Route::resource('aturan-chatbot', AturanChatbotController::class);
        Route::resource('data-teknisi', TeknisiManageController::class);
    });

    // 1.1 AKSES LAPORAN TOKO (Admin, Pimpinan & Kasir)
    Route::middleware(['peran:admin,pimpinan,kasir'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [App\Http\Controllers\LaporanController::class, 'index'])->name('index');
        Route::get('/preview/{tipe}', [App\Http\Controllers\LaporanController::class, 'preview'])->name('preview');
        Route::get('/cetak/{tipe}', [App\Http\Controllers\LaporanController::class, 'cetakPDF'])->name('cetak');
    });

    // 2. AKSES ADMIN, KASIR & TEKNISI (Transaksi & Servis)
    Route::middleware(['peran:admin,kasir'])->group(function () {
        Route::get('/transaksi', [App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi/konfirmasi/{id}', [App\Http\Controllers\TransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
        Route::get('/transaksi/invoice/{id}', [App\Http\Controllers\TransaksiController::class, 'invoice'])->name('transaksi.invoice');
        Route::post('/transaksi/update-resi/{id}', [App\Http\Controllers\TransaksiController::class, 'updateResi'])->name('transaksi.update-resi');

        // Notifikasi Admin & Kasir
        Route::post('/notifikasi/read/{id}', [App\Http\Controllers\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [App\Http\Controllers\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');
    });

    // 3. TEKNISI: Manajemen Servis
    Route::middleware(['peran:admin,kasir,teknisi'])->group(function () {
        Route::get('/teknisi/servis', [TeknisiController::class, 'index'])->name('teknisi.servis');
        Route::post('/teknisi/servis/store', [TeknisiController::class, 'store'])->name('teknisi.servis.store');
        Route::get('/teknisi/semua-servis', [TeknisiController::class, 'daftarServis'])->name('teknisi.semua-servis');
        Route::post('/teknisi/ambil/{id}', [TeknisiController::class, 'ambilServis'])->name('teknisi.ambil');
        Route::post('/teknisi/update-status/{id}', [TeknisiController::class, 'updateStatus'])->name('teknisi.update-status');
        Route::get('/teknisi/servis/tanda-terima/{id}', [TeknisiController::class, 'unduhTandaTerima'])->name('teknisi.servis.tanda-terima');
        Route::get('/teknisi/servis/nota/{id}', [TeknisiController::class, 'unduhNotaServis'])->name('teknisi.servis.nota');

        // Fitur Komplain Pelanggan
        Route::get('/komplain', [App\Http\Controllers\KomplainController::class, 'index'])->name('komplain.index');
        Route::post('/komplain/selesai/{id}', [App\Http\Controllers\KomplainController::class, 'selesai'])->name('komplain.selesai');
    });



    // 5. AKSES ADMIN, KASIR & PELANGGAN (Katalog & Transaksi Pelanggan)
    Route::middleware(['peran:admin,kasir,pelanggan'])->group(function () {
        // Fitur Katalog & Keranjang Checkout
        Route::get('/katalog', [KatalogController::class, 'index'])->name('pelanggan.katalog');
        Route::post('/keranjang/tambah/{id}', [KatalogController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
        Route::get('/keranjang', [KatalogController::class, 'tampilkanKeranjang'])->name('keranjang.index');
        Route::delete('/keranjang/hapus/{id}', [KatalogController::class, 'hapusItem'])->name('keranjang.hapus');
        Route::post('/checkout', [KatalogController::class, 'checkout'])->name('checkout');
        Route::get('/pesanan-saya', [App\Http\Controllers\TransaksiController::class, 'pesananSaya'])->name('pesanan.saya');

        // RajaOngkir API Routes
        Route::prefix('api/rajaongkir')->name('rajaongkir.')->group(function () {
            Route::get('/provinces', [RajaOngkirController::class, 'getProvinces'])->name('provinces');
            Route::get('/cities/{provinceId}', [RajaOngkirController::class, 'getCities'])->name('cities');
            Route::post('/check-ongkir', [RajaOngkirController::class, 'checkOngkir'])->name('check-ongkir');
        });

        // Pembayaran
        Route::get('/pembayaran/{id}', [KatalogController::class, 'formPembayaran'])->name('pembayaran.form');
        Route::post('/pembayaran/upload/{id}', [KatalogController::class, 'uploadPembayaran'])->name('pembayaran.upload');

        // Invoice untuk pelanggan
        Route::get('/pesanan-saya/invoice/{id}', [App\Http\Controllers\TransaksiController::class, 'invoice'])->name('pesanan.invoice');
        Route::post('/pesanan-saya/diterima/{id}', [App\Http\Controllers\TransaksiController::class, 'konfirmasiDiterima'])->name('transaksi.diterima');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

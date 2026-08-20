<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\AturanChatbot;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotGuestTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    /**
     * Test that guest users can access the chatbot page.
     */
    public function test_guest_can_access_chatbot_page(): void
    {
        $response = $this->get('/konsultasi');

        $response->assertStatus(200);
        $response->assertViewIs('chatbot.index');
        $response->assertSee('NJK Assistant');
        $response->assertSee('Kembali ke Halaman Login');
    }

    /**
     * Test that guest users asking to purchase are intercepted and required to login/register.
     */
    public function test_guest_purchase_intent_is_intercepted(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'saya mau beli laptop'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jawaban', 'rekomendasi_produk', 'rekomendasi_jasa']);
        
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('transaksi pembelian barang', $jawaban);
        $this->assertStringContainsString('Daftar Akun Baru', $jawaban);
        $this->assertStringContainsString('Masuk ke Akun Anda', $jawaban);
    }

    /**
     * Test that guest users asking standard questions are answered normally (without purchase interception).
     */
    public function test_guest_standard_questions_are_not_intercepted(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah toko buka hari minggu'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jawaban', 'rekomendasi_produk', 'rekomendasi_jasa']);
        
        $jawaban = $response->json('jawaban');
        // Standard response should not contain the login warning
        $this->assertStringNotContainsString('transaksi pembelian barang', $jawaban);
    }

    /**
     * Test that chatbot dynamically suggests products based on extracted keywords and categories.
     */
    public function test_chatbot_suggests_products_and_handles_stock_notifications(): void
    {
        // 1. Setup Categories and Products
        $catPrinter = \App\Models\Kategori::create(['nama_kategori' => 'Printer']);
        $catTinta = \App\Models\Kategori::create(['nama_kategori' => 'Tinta']);

        $printer = Produk::create([
            'kategori_id' => $catPrinter->id,
            'nama_produk' => 'Epson L3210 Printer',
            'merk' => 'Epson',
            'stok' => 5,
            'harga_beli' => 2000000,
            'harga_jual' => 2500000,
            'deskripsi' => 'Printer serbaguna Epson L3210.',
        ]);

        $tinta = Produk::create([
            'kategori_id' => $catTinta->id,
            'nama_produk' => 'Tinta Epson 003 Black',
            'merk' => 'Epson',
            'stok' => 0, // out of stock
            'harga_beli' => 70000,
            'harga_jual' => 90000,
            'deskripsi' => 'Tinta isi ulang original Epson 003 hitam untuk printer L3110 L3210.',
        ]);

        // 2. Query for Printer: "apakah ada printer L3210"
        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah ada printer L3210'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Epson L3210 Printer', $jawaban);
        $this->assertStringContainsString('Ready Stock', $jawaban);
        // Compatibility info (the ink should be suggested since description matches L3210)
        $this->assertStringContainsString('Tinta yang digunakan', $jawaban);
        $this->assertStringContainsString('Tinta Epson 003 Black', $jawaban);

        // 3. Query for Ink: "Tinta Printer Epson L3210"
        // This should prioritize the Tinta product which has stock = 0
        $responseInk = $this->postJson('/api/chat', [
            'pesan' => 'Tinta Printer Epson L3210'
        ]);

        $responseInk->assertStatus(200);
        $jawabanInk = $responseInk->json('jawaban');
        // It should match the Tinta product
        $this->assertStringContainsString('Tinta Epson 003 Black', $jawabanInk);
        $this->assertStringContainsString('Habis dan akan segera direstock', $jawabanInk);
        // Since the user is a guest, it should guide them to register/login for WhatsApp notification
        $this->assertStringContainsString('Daftar Akun Baru', $jawabanInk);
        $this->assertStringContainsString('Masuk ke Akun Anda', $jawabanInk);

        // 4. Query for Ink when logged in
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '081234567890',
            'aktifkan_notifikasi' => true,
        ]);
        
        $responseLoggedIn = $this->actingAs($user)->postJson('/api/chat', [
            'pesan' => 'Tinta Printer Epson L3210'
        ]);

        $responseLoggedIn->assertStatus(200);
        $jawabanLoggedIn = $responseLoggedIn->json('jawaban');
        $this->assertStringContainsString('Notifikasi Restock Aktif', $jawabanLoggedIn);
        $this->assertStringContainsString('081234567890', $jawabanLoggedIn);
    }

    /**
     * Test that asking about offline store returns maps link and whatsapp numbers.
     */
    public function test_chatbot_returns_offline_store_location_and_whatsapp_numbers(): void
    {
        if (AturanChatbot::count() === 0) {
            $this->seed(\Database\Seeders\AturanChatbotSeeder::class);
        }

        $response = $this->postJson('/api/chat', [
            'pesan' => 'dimana lokasi toko offlinenya'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('https://share.google/xrwq12yHe0uMzcoFv', $jawaban);
        $this->assertStringContainsString('0851-8239-2525', $jawaban);
        $this->assertStringContainsString('0851-8239-2526', $jawaban);
    }
}



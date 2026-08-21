<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
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
        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key');
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Toko kami buka dari hari Senin sampai Sabtu pukul 09.00 - 17.00 WITA. Hari Minggu libur.'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah toko buka hari minggu'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jawaban', 'rekomendasi_produk', 'rekomendasi_jasa']);
        
        $jawaban = $response->json('jawaban');
        $this->assertStringNotContainsString('transaksi pembelian barang', $jawaban);
        $this->assertStringContainsString('Senin sampai Sabtu', $jawaban);
    }

    /**
     * Test chatbot inquiry with Grok AI recommendations.
     */
    public function test_chatbot_suggests_products_via_grok_ai(): void
    {
        $catPrinter = Kategori::create(['nama_kategori' => 'Printer']);

        $printer = Produk::create([
            'kategori_id' => $catPrinter->id,
            'nama_produk' => 'Epson L3210 Printer',
            'merk' => 'Epson',
            'stok' => 5,
            'harga_beli' => 2000000,
            'harga_jual' => 2500000,
            'deskripsi' => 'Printer serbaguna Epson L3210.',
        ]);

        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key');
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Untuk printer serbaguna, kami memiliki **Epson L3210 Printer** dengan harga **Rp 2.500.000** dan status Ready Stock.'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah ada printer L3210'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Epson L3210 Printer', $jawaban);
        $this->assertStringContainsString('2.500.000', $jawaban);

        // Recommendations should extract the matched product
        $rekomendasi = $response->json('rekomendasi_produk');
        $this->assertNotEmpty($rekomendasi);
    }

    /**
     * Test that asking about offline store or technician returns contact and address.
     */
    public function test_chatbot_returns_offline_store_location_and_whatsapp_numbers(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'dimana lokasi toko dan nomor kontak teknisi'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('https://share.google/xrwq12yHe0uMzcoFv', $jawaban);
        $this->assertStringContainsString('0851-8239-2525', $jawaban);
        $this->assertStringContainsString('0851-8239-2526', $jawaban);
    }
}

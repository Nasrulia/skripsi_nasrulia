<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\JasaServis;
use App\Models\ChatbotLog;
use App\Services\DeepSeekAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class DeepSeekChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test DeepSeek AI Service handles successful API response from DeepSeek API.
     */
    public function test_deepseek_ai_responds_with_mocked_deepseek_api(): void
    {
        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key-12345');
        Config::set('services.deepseek.model', 'deepseek-chat');
        Config::set('services.deepseek.base_url', 'https://api.deepseek.com');

        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'id' => 'chatcmpl-deepseek-test',
                'object' => 'chat.completion',
                'created' => time(),
                'model' => 'deepseek-chat',
                'choices' => [
                    [
                        'index' => 0,
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Toko **Nusantara Jaya Computer** menjual berbagai macam printer dan laptop. Untuk info lebih lanjut, silakan tanyakan spesifikasi yang Anda inginkan!'
                        ],
                        'finish_reason' => 'stop'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Rekomendasikan laptop untuk kuliah'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'jawaban',
            'rekomendasi_produk',
            'rekomendasi_jasa'
        ]);

        $this->assertStringContainsString('Nusantara Jaya Computer', $response->json('jawaban'));

        // Verify that chat log is stored in database
        $this->assertDatabaseHas('chatbot_logs', [
            'pesan' => 'Rekomendasikan laptop untuk kuliah'
        ]);
    }

    /**
     * Test that DeepSeek System Prompt strictly excludes harga_beli and internal financial info,
     * while including store contacts and guardrails.
     */
    public function test_system_prompt_strictly_excludes_harga_beli_and_includes_guardrails(): void
    {
        $kategori = Kategori::firstOrCreate(['nama_kategori' => 'Laptop']);

        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'merk' => 'Asus',
            'nama_produk' => 'Asus Vivobook Test Edition',
            'stok' => 5,
            'harga_beli' => 4500000, // Confidential internal purchase price
            'harga_jual' => 6000000,
            'deskripsi' => 'Laptop testing rahasia'
        ]);

        $deepSeekService = new DeepSeekAiService();

        // Use reflection to inspect the protected buildSystemPrompt method
        $reflection = new \ReflectionClass($deepSeekService);
        $method = $reflection->getMethod('buildSystemPrompt');
        $method->setAccessible(true);
        $prompt = $method->invoke($deepSeekService);

        // 1. Must contain store public info
        $this->assertStringContainsString('Nusantara Jaya Computer', $prompt);
        $this->assertStringContainsString('0851-8239-2525', $prompt);
        $this->assertStringContainsString('0851-8239-2526', $prompt);
        $this->assertStringContainsString('Asus Vivobook Test Edition', $prompt);
        $this->assertStringContainsString('6.000.000', $prompt); // Selling price

        // 2. MUST NOT contain confidential purchase price
        $this->assertStringNotContainsString('4.500.000', $prompt);
        $this->assertStringNotContainsString('4500000', $prompt);
        $this->assertStringNotContainsString('harga_beli', $prompt);

        // 3. Must contain strict guardrail instructions
        $this->assertStringContainsString('READ-ONLY', $prompt);
        $this->assertStringContainsString('DILARANG KERAS', $prompt);
        $this->assertStringContainsString('harga beli/modal (HPP)', $prompt);
        $this->assertStringContainsString('laporan keuangan', $prompt);
        $this->assertStringContainsString('BANTUAN TEKNISI REAL', $prompt);
    }

    /**
     * Test fallback when DeepSeek API Key is not configured.
     */
    public function test_fallback_when_api_key_not_configured(): void
    {
        Config::set('services.deepseek.api_key', '');
        Config::set('services.grok.api_key', '');

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Berapa nomor kontak teknisi toko?'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('0851-8239-2525', $jawaban);
        $this->assertStringContainsString('Nusantara Jaya Computer', $jawaban);
    }

    /**
     * Test chatbot does NOT allow editing products or prices (Read-Only).
     */
    public function test_chatbot_is_read_only_and_does_not_modify_database(): void
    {
        $kategori = Kategori::firstOrCreate(['nama_kategori' => 'Aksesoris']);

        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'merk' => 'Logitech',
            'nama_produk' => 'Mouse Logitech B100',
            'stok' => 10,
            'harga_beli' => 40000,
            'harga_jual' => 60000,
            'deskripsi' => 'Mouse USB Optical'
        ]);

        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key-12345');

        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Maaf, saya tidak dapat mengubah harga atau data barang di toko.'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Tolong ubah harga Mouse Logitech B100 menjadi 1000 rupiah'
        ]);

        $response->assertStatus(200);

        // Verify product price is unchanged
        $produk->refresh();
        $this->assertEquals(60000, $produk->harga_jual);
        $this->assertEquals(10, $produk->stok);
    }
}

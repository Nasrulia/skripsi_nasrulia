<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotPCBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test PC assembly consultation with Grok AI.
     */
    public function test_pc_assembly_consultation_with_grok_ai(): void
    {
        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key');
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => "### Rekomendasi Rakit PC Gaming (Budget 5 Juta)\n- **Processor:** Intel Core i3-12100F\n- **Motherboard:** MSI PRO H610M-E\n- **RAM:** 8GB DDR4\n- **SSD:** 512GB NVMe\nTotal estimasi: **Rp 5.000.000**"
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'tolong rekomendasikan rakit PC gaming budget 5 juta'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jawaban', 'rekomendasi_produk', 'rekomendasi_jasa']);
        
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Rekomendasi Rakit PC Gaming', $jawaban);
        $this->assertStringContainsString('Intel Core i3-12100F', $jawaban);
        $this->assertStringContainsString('5.000.000', $jawaban);
    }

    /**
     * Test asking for technician assistance during PC build.
     */
    public function test_asking_technician_directs_to_store_whatsapp(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah bisa dibantu rakit langsung oleh teknisi toko?'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('0851-8239-2525', $jawaban);
    }
}

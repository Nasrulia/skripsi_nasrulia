<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotProductConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test laptop consultation using Grok AI.
     */
    public function test_laptop_consultation_with_grok_ai(): void
    {
        $catLaptop = Kategori::create(['nama_kategori' => 'Laptop']);

        $laptop = Produk::create([
            'kategori_id' => $catLaptop->id,
            'nama_produk' => 'Asus Vivobook Go 14',
            'merk' => 'Asus',
            'stok' => 3,
            'harga_beli' => 4500000,
            'harga_jual' => 5800000,
            'deskripsi' => 'Laptop Asus Vivobook Go 14 Ryzen 3 7320U RAM 8GB SSD 512GB.',
        ]);

        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key');
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => "Untuk kebutuhan kuliah dan kerja dengan budget sekitar 5 jutaan, kami merekomendasikan **Asus Vivobook Go 14** dengan harga **Rp 5.800.000** (Ready Stock). Spesifikasi Ryzen 3, RAM 8GB, SSD 512GB."
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Rekomendasikan laptop Asus budget 5-6 juta untuk kuliah'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Asus Vivobook Go 14', $jawaban);
        $this->assertStringContainsString('5.800.000', $jawaban);

        // Product recommendation card
        $recommended = $response->json('rekomendasi_produk');
        $this->assertNotEmpty($recommended);
    }
}

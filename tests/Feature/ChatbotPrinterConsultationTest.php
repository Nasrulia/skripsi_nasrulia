<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotPrinterConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test printer consultation with Grok AI.
     */
    public function test_printer_consultation_with_grok_ai(): void
    {
        $catPrinter = Kategori::create(['nama_kategori' => 'Printer']);
        $catTinta = Kategori::create(['nama_kategori' => 'Tinta']);

        $printer = Produk::create([
            'kategori_id' => $catPrinter->id,
            'nama_produk' => 'Brother DCP-T230',
            'merk' => 'Brother',
            'stok' => 4,
            'harga_beli' => 1700000,
            'harga_jual' => 2100000,
            'deskripsi' => 'Printer Brother DCP-T230 multifungsi print scan copy.',
        ]);

        Config::set('services.deepseek.api_key', 'sk-deepseek-test-key');
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => "Untuk kebutuhan printer scan dan copy, kami merekomendasikan **Brother DCP-T230** dengan harga **Rp 2.100.000** (Ready Stock)."
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Rekomendasi printer Brother untuk scan dan copy'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Brother DCP-T230', $jawaban);
        $this->assertStringContainsString('2.100.000', $jawaban);

        // Product recommendation card
        $recommended = $response->json('rekomendasi_produk');
        $this->assertNotEmpty($recommended);
    }
}

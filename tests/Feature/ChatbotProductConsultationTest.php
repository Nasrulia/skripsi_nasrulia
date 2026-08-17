<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ChatbotLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotProductConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed Asus products for testing
        $this->seed(\Database\Seeders\ProdukAsusSeeder::class);
    }

    /**
     * Test full product consultation flow for laptop/goods inquiries.
     */
    public function test_product_consultation_flow_success(): void
    {
        // 1. Trigger flow by asking laptop price
        $response = $this->postJson('/api/chat', [
            'pesan' => 'berapa harga laptop'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('product_consult_waiting_budget', session('chatbot_flow'));
        $this->assertStringContainsString('kisaran budget / harga maksimal', $response->json('jawaban'));

        // 2. Submit invalid budget format
        $response = $this->postJson('/api/chat', [
            'pesan' => 'tidak ada'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('product_consult_waiting_budget', session('chatbot_flow'));
        $this->assertStringContainsString('Format budget tidak valid', $response->json('jawaban'));

        // 3. Submit valid budget (8.5 juta)
        $response = $this->postJson('/api/chat', [
            'pesan' => '8.5 juta'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('product_consult_waiting_purpose', session('chatbot_flow'));
        $this->assertEquals(8500000, session('product_consult_budget'));
        $this->assertStringContainsString('penggunaan / kebutuhan utama', $response->json('jawaban'));

        // 4. Submit purpose Gaming
        $response = $this->postJson('/api/chat', [
            'pesan' => 'gaming'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('product_consult_waiting_specs', session('chatbot_flow'));
        $this->assertStringContainsString('preferensi spesifikasi atau merk tertentu', $response->json('jawaban'));

        // 5. Submit specs & brand preference (ASUS RAM 8GB)
        $response = $this->postJson('/api/chat', [
            'pesan' => 'ASUS RAM 8GB'
        ]);
        $response->assertStatus(200);
        
        // Session should be cleared
        $this->assertNull(session('chatbot_flow'));
        $this->assertNull(session('product_consult_budget'));

        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('REKOMENDASI PRODUK HASIL KONSULTASI', $jawaban);
        $this->assertStringContainsString('8.500.000', $jawaban);
        
        // Check recommended products
        $recommended = $response->json('rekomendasi_produk');
        $this->assertNotEmpty($recommended);
    }

    /**
     * Test cancelling the product consultation flow.
     */
    public function test_product_consultation_flow_cancellation(): void
    {
        // Start flow
        $this->postJson('/api/chat', [
            'pesan' => 'rekomendasi laptop'
        ]);
        $this->assertEquals('product_consult_waiting_budget', session('chatbot_flow'));

        // Cancel flow
        $response = $this->postJson('/api/chat', [
            'pesan' => 'batal'
        ]);

        $response->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));
        $this->assertStringContainsString('dibatalkan', $response->json('jawaban'));
    }

    /**
     * Test asking "apakah ada list laptop" triggers consultation flow.
     */
    public function test_asking_list_laptop_triggers_consultation_flow(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah ada list laptop'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('product_consult_waiting_budget', session('chatbot_flow'));
        $this->assertStringContainsString('kisaran budget / harga maksimal', $response->json('jawaban'));
    }
}

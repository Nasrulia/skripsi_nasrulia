<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ChatbotLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotPCBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed rakitan components dan aturan chatbot
        $this->seed(\Database\Seeders\KomponenRakitanSeeder::class);
        $this->seed(\Database\Seeders\AturanChatbotSeeder::class);
    }

    /**
     * Test full PC assembly chatbot conversation flow.
     */
    public function test_pc_assembly_flow_success(): void
    {
        // 1. Trigger flow
        $response = $this->postJson('/api/chat', [
            'pesan' => 'tolong rakit PC'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_budget', session('chatbot_flow'));
        $response->assertJsonFragment([
            'rekomendasi_produk' => []
        ]);
        $this->assertStringContainsString('budget maksimal', $response->json('jawaban'));

        // 2. Submit invalid budget
        $response = $this->postJson('/api/chat', [
            'pesan' => 'gratis'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_budget', session('chatbot_flow'));
        $this->assertStringContainsString('Format budget tidak valid', $response->json('jawaban'));

        // 3. Submit too low budget
        $response = $this->postJson('/api/chat', [
            'pesan' => 'Rp 2.500.000'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_budget', session('chatbot_flow'));
        $this->assertStringContainsString('budget minimal yang disarankan', $response->json('jawaban'));

        // 4. Submit valid budget (5 Juta)
        $response = $this->postJson('/api/chat', [
            'pesan' => '5.5 juta'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_brand', session('chatbot_flow'));
        $this->assertEquals(5500000, session('rakit_pc_budget'));
        $this->assertStringContainsString('preferensi merk Processor', $response->json('jawaban'));

        // 5. Submit brand Intel
        $response = $this->postJson('/api/chat', [
            'pesan' => 'Intel'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_purpose', session('chatbot_flow'));
        $this->assertEquals('Intel', session('rakit_pc_brand'));
        $this->assertStringContainsString('kebutuhan utama', $response->json('jawaban'));

        // 6. Submit purpose Gaming
        $response = $this->postJson('/api/chat', [
            'pesan' => 'untuk gaming'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('rakit_pc_waiting_optional', session('chatbot_flow'));
        $this->assertEquals('Gaming', session('rakit_pc_purpose'));
        $this->assertStringContainsString('perangkat tambahan', $response->json('jawaban'));

        // 7. Submit optional components: Monitor dan Wifi
        $response = $this->postJson('/api/chat', [
            'pesan' => 'monitor saja'
        ]);
        $response->assertStatus(200);
        
        // Session should be cleared
        $this->assertNull(session('chatbot_flow'));
        $this->assertNull(session('rakit_pc_budget'));
        
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('REKOMENDASI SPESIFIKASI CPU RAKITAN', $jawaban);
        $this->assertStringContainsString('Intel Core i3-12100F', $jawaban);
        $this->assertStringContainsString('MSI PRO H610M-E DDR4', $jawaban);
        $this->assertStringContainsString('Monitor', $jawaban);
        $this->assertStringContainsString('Total Estimasi', $jawaban);
        
        // Check that products are recommended
        $recommended = $response->json('rekomendasi_produk');
        $this->assertNotEmpty($recommended);
    }

    /**
     * Test cancel flow.
     */
    public function test_pc_assembly_flow_cancellation(): void
    {
        // Start flow
        $this->postJson('/api/chat', [
            'pesan' => 'merakit komputer'
        ]);
        $this->assertEquals('rakit_pc_waiting_budget', session('chatbot_flow'));

        // Cancel
        $response = $this->postJson('/api/chat', [
            'pesan' => 'batal'
        ]);
        $response->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));
        $this->assertStringContainsString('dibatalkan', $response->json('jawaban'));
    }

    /**
     * Test asking for assembly duration.
     */
    public function test_asking_for_assembly_duration(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'berapa lama proses pengerjaannya?'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('4-5 jam', $jawaban);
        $this->assertStringContainsString('7-10 hari', $jawaban);
        $this->assertStringContainsString('Banjarmasin', $jawaban);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ChatbotLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotPrinterConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed printer products for testing
        $this->seed(\Database\Seeders\ProdukPrinterSeeder::class);
    }

    /**
     * Test asking for printer recommendation triggers the consultation flow.
     */
    public function test_asking_printer_triggers_consultation_flow(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'rekomendasi printer'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('printer_consult_waiting_preference', session('chatbot_flow'));
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('Scan & Copy', $jawaban);
        $this->assertStringContainsString('Khusus Print Saja', $jawaban);
        $this->assertStringContainsString('Epson', $jawaban);
        $this->assertStringContainsString('Canon', $jawaban);
        $this->assertStringContainsString('Brother', $jawaban);
    }

    /**
     * Test step-by-step printer consultation: selecting usage first, then brand.
     */
    public function test_printer_consultation_step_by_step_usage_then_brand(): void
    {
        // 1. Trigger flow
        $this->postJson('/api/chat', [
            'pesan' => 'cari printer'
        ]);
        $this->assertEquals('printer_consult_waiting_preference', session('chatbot_flow'));

        // 2. Select usage: Scan & Copy
        $responseUsage = $this->postJson('/api/chat', [
            'pesan' => 'scan copy'
        ]);
        $responseUsage->assertStatus(200);
        $this->assertEquals('printer_consult_waiting_brand', session('chatbot_flow'));
        $this->assertEquals('Scan & Copy', session('printer_consult_usage'));
        $this->assertStringContainsString('merk printer', $responseUsage->json('jawaban'));

        // 3. Select brand: Epson
        $responseBrand = $this->postJson('/api/chat', [
            'pesan' => 'epson'
        ]);
        $responseBrand->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));

        $jawaban = $responseBrand->json('jawaban');
        $this->assertStringContainsString('REKOMENDASI PRINTER PILIHAN', $jawaban);
        $this->assertStringContainsString('Scan & Copy', $jawaban);
        $this->assertStringContainsString('Epson', $jawaban);
        $this->assertStringContainsString('L3211', $jawaban);
        
        $recommended = $responseBrand->json('rekomendasi_produk');
        $this->assertNotEmpty($recommended);
    }

    /**
     * Test step-by-step printer consultation: selecting brand first, then usage.
     */
    public function test_printer_consultation_step_by_step_brand_then_usage(): void
    {
        // 1. Trigger flow
        $this->postJson('/api/chat', [
            'pesan' => 'tanya printer'
        ]);
        $this->assertEquals('printer_consult_waiting_preference', session('chatbot_flow'));

        // 2. Select brand: Canon
        $responseBrand = $this->postJson('/api/chat', [
            'pesan' => 'canon'
        ]);
        $responseBrand->assertStatus(200);
        $this->assertEquals('printer_consult_waiting_usage', session('chatbot_flow'));
        $this->assertEquals('Canon', session('printer_consult_brand'));
        $this->assertStringContainsString('apakah Anda membutuhkan fungsi', $responseBrand->json('jawaban'));

        // 3. Select usage: Khusus Print Saja
        $responseUsage = $this->postJson('/api/chat', [
            'pesan' => 'khusus print saja'
        ]);
        $responseUsage->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));

        $jawaban = $responseUsage->json('jawaban');
        $this->assertStringContainsString('REKOMENDASI PRINTER PILIHAN', $jawaban);
        $this->assertStringContainsString('Khusus Print Saja', $jawaban);
        $this->assertStringContainsString('Canon', $jawaban);
        $this->assertStringContainsString('G1010', $jawaban);
    }

    /**
     * Test direct printer consultation when user specifies both usage and brand in initial query.
     */
    public function test_printer_consultation_direct_usage_and_brand(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'rekomendasi printer scan copy brother'
        ]);

        $response->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));

        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('REKOMENDASI PRINTER PILIHAN', $jawaban);
        $this->assertStringContainsString('Brother', $jawaban);
        $this->assertStringContainsString('T230', $jawaban);
    }

    /**
     * Test cancelling the printer consultation flow.
     */
    public function test_printer_consultation_flow_cancellation(): void
    {
        // 1. Trigger flow
        $this->postJson('/api/chat', [
            'pesan' => 'cari printer'
        ]);
        $this->assertEquals('printer_consult_waiting_preference', session('chatbot_flow'));

        // 2. Cancel
        $response = $this->postJson('/api/chat', [
            'pesan' => 'batal'
        ]);
        $response->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));
        $this->assertStringContainsString('dibatalkan', $response->json('jawaban'));
    }

    /**
     * Test specific model query bypasses consultation flow and directly returns product details.
     */
    public function test_specific_printer_model_query_bypasses_consultation(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'apakah ada printer L3211 ready'
        ]);

        $response->assertStatus(200);
        $this->assertNull(session('chatbot_flow'));
        $jawaban = $response->json('jawaban');
        $this->assertStringContainsString('PRINTER EPSON L3211', $jawaban);
        $this->assertStringContainsString('Ready Stock', $jawaban);
    }
}

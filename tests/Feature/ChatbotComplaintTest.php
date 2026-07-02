<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\Komplain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class ChatbotComplaintTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test initiating complaint flow when keyword is matched.
     */
    public function test_initiating_complaint_flow(): void
    {
        $response = $this->postJson('/api/chat', [
            'pesan' => 'Saya mau melakukan komplain'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jawaban']);
        $this->assertStringContainsString('nomor transaksi', $response->json('jawaban'));
        $this->assertEquals('complaint_waiting_trx', session('chatbot_flow'));
    }

    /**
     * Test complaint flow when invalid transaction code is entered.
     */
    public function test_complaint_flow_invalid_transaction(): void
    {
        // Set session state
        session(['chatbot_flow' => 'complaint_waiting_trx']);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'TRX-INVALIDCODE'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('tidak ditemukan', $response->json('jawaban'));
        $this->assertEquals('complaint_waiting_trx', session('chatbot_flow'));
    }

    /**
     * Test complaint flow when valid transaction code is entered.
     */
    public function test_complaint_flow_valid_transaction(): void
    {
        // 1. Create a user and transaction
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '081234567890',
            'peran' => 'pelanggan'
        ]);

        $transaction = Transaksi::create([
            'kode_transaksi' => 'TRX-1234567890',
            'user_id' => $customer->id,
            'nama_pelanggan' => 'John Doe',
            'tipe' => 'penjualan',
            'total_bayar' => 150000,
            'status' => 'Lunas'
        ]);

        // 2. Set session state
        session(['chatbot_flow' => 'complaint_waiting_trx']);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'TRX-1234567890'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('rincian detail komplain', $response->json('jawaban'));
        $this->assertEquals('complaint_waiting_desc', session('chatbot_flow'));
        $this->assertEquals($transaction->id, session('complaint_trx_id'));
        $this->assertEquals('TRX-1234567890', session('complaint_trx_code'));
    }

    /**
     * Test complaint flow submitting the complaint description.
     */
    public function test_complaint_flow_submitting_description(): void
    {
        // 1. Create customer and transaction
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '081234567890',
            'peran' => 'pelanggan'
        ]);

        // Create admin/cashier to receive WhatsApp notifications
        $admin = User::create([
            'name' => 'Admin NJK',
            'email' => 'adminnjk@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '085211223344',
            'peran' => 'admin'
        ]);

        $transaction = Transaksi::create([
            'kode_transaksi' => 'TRX-1234567890',
            'user_id' => $customer->id,
            'nama_pelanggan' => 'John Doe',
            'tipe' => 'penjualan',
            'total_bayar' => 150000,
            'status' => 'Lunas'
        ]);

        // 2. Set session variables
        session([
            'chatbot_flow' => 'complaint_waiting_desc',
            'complaint_trx_id' => $transaction->id,
            'complaint_trx_code' => 'TRX-1234567890',
            'complaint_trx_type' => 'penjualan',
        ]);

        // 3. Submit description
        $response = $this->postJson('/api/chat', [
            'pesan' => 'Barang yang saya beli rusak layar pecah'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('resmi tercatat', $response->json('jawaban') ?: '');
        $this->assertNull(session('chatbot_flow'));

        // 4. Verify DB record
        $this->assertDatabaseHas('komplain', [
            'kode_transaksi' => 'TRX-1234567890',
            'nama_pelanggan' => 'John Doe',
            'deskripsi' => 'Barang yang saya beli rusak layar pecah',
            'tipe' => 'penjualan',
            'status' => 'pending'
        ]);
    }

    /**
     * Test cancellation of complaint flow.
     */
    public function test_cancellation_of_complaint_flow(): void
    {
        session(['chatbot_flow' => 'complaint_waiting_trx']);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'batal'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('dibatalkan', $response->json('jawaban'));
        $this->assertNull(session('chatbot_flow'));
    }
}

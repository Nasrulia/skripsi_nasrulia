<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotTransactionNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test chatbot response when customer enters transaction number.
     */
    public function test_customer_can_send_transaction_number_to_chatbot_and_gets_admin_processing_response(): void
    {
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '081234567890',
        ]);

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-999888777',
            'user_id' => $user->id,
            'nama_pelanggan' => $user->name,
            'tipe' => 'penjualan',
            'total_bayar' => 1500000,
            'status' => 'Pending',
            'metode_pengambilan' => 'diantar',
        ]);

        $response = $this->postJson('/api/chat', [
            'pesan' => 'Saya sudah checkout, ini nomor transaksi TRX-999888777'
        ]);

        $response->assertStatus(200);
        $jawaban = $response->json('jawaban');

        $this->assertStringContainsString('TRX-999888777', $jawaban);
        $this->assertStringContainsString('akan segera diproses oleh admin', $jawaban);

        // Verify In-App Notification created for Admin
        $this->assertDatabaseHas('notifikasi', [
            'tipe' => 'chatbot',
            'is_read' => false,
        ]);
    }

    /**
     * Test that checkout creates an In-App notification for Admin.
     */
    public function test_admin_receives_in_app_notification_when_customer_checkouts(): void
    {
        $user = User::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '089876543210',
            'peran' => 'pelanggan',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Aksesoris']);
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'nama_produk' => 'Mouse Wireless Logitech',
            'merk' => 'Logitech',
            'stok' => 10,
            'harga_beli' => 100000,
            'harga_jual' => 150000,
            'deskripsi' => 'Mouse wireless silent',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'keranjang' => [
                    $produk->id => [
                        'nama' => $produk->nama_produk,
                        'jumlah' => 1,
                        'harga' => $produk->harga_jual,
                    ]
                ]
            ])
            ->post('/checkout', [
                'metode_pengambilan' => 'diambil',
                'estimasi_diambil' => Carbon::tomorrow()->format('Y-m-d'),
                'metode_pembayaran' => 'cash',
            ]);

        $this->assertDatabaseHas('transaksi', [
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'tipe' => 'checkout',
            'is_read' => false,
        ]);
    }

    /**
     * Test 15-minute unprocessed transaction alert command.
     */
    public function test_unprocessed_transaction_after_15_minutes_triggers_whatsapp_alert_to_admin_number(): void
    {
        $user = User::create([
            'name' => 'Pelanggan Lambat',
            'email' => 'lambat@example.com',
            'password' => bcrypt('password'),
        ]);

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-LAMA-15M',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Pelanggan Lambat',
            'tipe' => 'penjualan',
            'total_bayar' => 500000,
            'status' => 'Pending',
            'metode_pengambilan' => 'diantar',
            'is_reminded_15m' => false,
        ]);

        \Illuminate\Support\Facades\DB::table('transaksi')->where('id', $trx->id)->update([
            'created_at' => Carbon::now()->subMinutes(20),
        ]);

        $this->artisan('transaksi:cek-unprocessed')
            ->assertExitCode(0);

        $this->assertDatabaseHas('transaksi', [
            'id' => $trx->id,
            'is_reminded_15m' => true,
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'tipe' => 'warning',
        ]);
    }

    /**
     * Test admin can mark notification as read.
     */
    public function test_admin_can_mark_notification_as_read(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'peran' => 'admin',
        ]);

        $notif = Notifikasi::create([
            'judul' => 'Test Notif',
            'pesan' => 'Pesan Notifikasi',
            'is_read' => false,
            'tipe' => 'checkout',
        ]);

        $response = $this->actingAs($admin)
            ->post("/notifikasi/read/{$notif->id}");

        $this->assertDatabaseHas('notifikasi', [
            'id' => $notif->id,
            'is_read' => true,
        ]);
    }
}

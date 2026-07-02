<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\ServisDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceStatusCheckTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test that the public status check page is accessible.
     */
    public function test_public_status_check_page_is_accessible(): void
    {
        $response = $this->get('/cek-servis');

        $response->assertStatus(200);
        $response->assertViewIs('teknisi.cek-status');
        $response->assertSee('Lacak Progres Servis Anda');
        $response->assertSee('Masukkan Nomor Transaksi Anda');
    }

    /**
     * Test tracking service status with a valid transaction code.
     */
    public function test_can_track_service_with_valid_transaction_code(): void
    {
        // 1. Create a customer user
        $pelanggan = User::create([
            'name' => 'Pelanggan Uji',
            'email' => 'pelangganuji@example.com',
            'password' => bcrypt('password'),
            'no_whatsapp' => '081299998888',
            'peran' => 'pelanggan',
        ]);

        // 2. Create a service transaction
        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-SRV-TEST101',
            'user_id' => $pelanggan->id,
            'nama_pelanggan' => 'Pelanggan Uji',
            'tipe' => 'servis',
            'total_bayar' => 150000,
            'metode_pembayaran' => 'cash',
            'status' => 'Pending',
            'metode_pengambilan' => 'diambil',
        ]);

        // 3. Create service detail
        $servis = ServisDetail::create([
            'transaksi_id' => $transaksi->id,
            'nama_barang' => 'Laptop ASUS ROG',
            'no_seri' => 'ROG12345',
            'keluhan' => 'Layar bergaris-garis',
            'estimasi_biaya' => 150000,
            'estimasi_waktu' => '3 hari',
            'upah_teknisi' => 75000,
            'keuntungan_toko' => 75000,
            'status' => 'proses',
            'penerima' => 'Kasir Toko',
        ]);

        // 4. Submit status check form with valid transaction code
        $response = $this->post('/cek-servis', [
            'kode_transaksi' => 'TRX-SRV-TEST101',
        ]);

        $response->assertStatus(200);
        $response->assertSee('TRX-SRV-TEST101');
        $response->assertSee('Laptop ASUS ROG');
        $response->assertSee('Layar bergaris-garis');
        $response->assertSee('Sedang Diproses');
    }

    /**
     * Test tracking service status with an invalid/non-existent transaction code.
     */
    public function test_cannot_track_service_with_invalid_transaction_code(): void
    {
        $response = $this->post('/cek-servis', [
            'kode_transaksi' => 'TRX-SRV-INVALID',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Data Servis Tidak Ditemukan');
        $response->assertSee('TRX-SRV-INVALID');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\ServisDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PimpinanAndLaporanFilterPreviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_pimpinan_can_login_and_is_redirected_to_dashboard(): void
    {
        $pimpinan = User::where('peran', 'pimpinan')->first();
        if (!$pimpinan) {
            $pimpinan = User::factory()->create([
                'email' => 'pimpinan_test@gmail.com',
                'password' => bcrypt('password'),
                'peran' => 'pimpinan'
            ]);
        }

        $response = $this->post('/login', [
            'email' => $pimpinan->email,
            'password' => 'password'
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($pimpinan);
    }

    public function test_pimpinan_can_access_dashboard_and_statistics_api(): void
    {
        $pimpinan = User::where('peran', 'pimpinan')->first() ?? User::factory()->create(['peran' => 'pimpinan']);

        $response = $this->actingAs($pimpinan)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat datang kembali');
        $response->assertSee('Grafik & Ringkasan Keuangan', false);

        $apiResponse = $this->actingAs($pimpinan)->getJson('/api/dashboard/statistiks?filter=hari');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonStructure([
            '*' => ['label', 'penjualan', 'keuntungan']
        ]);
    }

    public function test_pimpinan_cannot_access_master_data_or_cashier(): void
    {
        $pimpinan = User::where('peran', 'pimpinan')->first() ?? User::factory()->create(['peran' => 'pimpinan']);

        $responseProduk = $this->actingAs($pimpinan)->get('/produk');
        $responseProduk->assertStatus(403);

        $responseKategori = $this->actingAs($pimpinan)->get('/kategori');
        $responseKategori->assertStatus(403);

        $responseTransaksi = $this->actingAs($pimpinan)->get('/transaksi');
        $responseTransaksi->assertStatus(403);
    }

    public function test_pimpinan_admin_and_kasir_can_access_laporan_index(): void
    {
        $admin = User::where('peran', 'admin')->first() ?? User::factory()->create(['peran' => 'admin']);
        $kasir = User::where('peran', 'kasir')->first() ?? User::factory()->create(['peran' => 'kasir']);
        $pimpinan = User::where('peran', 'pimpinan')->first() ?? User::factory()->create(['peran' => 'pimpinan']);
        $pelanggan = User::where('peran', 'pelanggan')->first() ?? User::factory()->create(['peran' => 'pelanggan']);

        $this->actingAs($admin)->get('/laporan')->assertStatus(200)->assertSee('Laporan & Pratinjau Toko', false);
        $this->actingAs($kasir)->get('/laporan')->assertStatus(200)->assertSee('Laporan & Pratinjau Toko', false);
        $this->actingAs($pimpinan)->get('/laporan')->assertStatus(200)->assertSee('Laporan & Pratinjau Toko', false);

        // Pelanggan cannot access laporan
        $this->actingAs($pelanggan)->get('/laporan')->assertStatus(403);
    }

    public function test_laporan_preview_with_harian_bulanan_tahunan_and_custom_filters(): void
    {
        $pimpinan = User::where('peran', 'pimpinan')->first() ?? User::factory()->create(['peran' => 'pimpinan']);

        // 1. Preview default (semua)
        $resDefault = $this->actingAs($pimpinan)->get('/laporan/preview/transaksi-penjualan');
        $resDefault->assertStatus(200);
        $resDefault->assertSee('Laporan Transaksi Penjualan (Lunas)');
        $resDefault->assertSee('Semua Waktu');

        // 2. Preview filter harian
        $resHarian = $this->actingAs($pimpinan)->get('/laporan/preview/transaksi-penjualan?filter_type=harian&tanggal=' . date('Y-m-d'));
        $resHarian->assertStatus(200);
        $resHarian->assertSee('Harian:');

        // 3. Preview filter bulanan
        $resBulanan = $this->actingAs($pimpinan)->get('/laporan/preview/transaksi-penjualan?filter_type=bulanan&bulan=' . date('n') . '&tahun=' . date('Y'));
        $resBulanan->assertStatus(200);
        $resBulanan->assertSee('Bulan:');

        // 4. Preview filter tahunan
        $resTahunan = $this->actingAs($pimpinan)->get('/laporan/preview/transaksi-penjualan?filter_type=tahunan&tahun=' . date('Y'));
        $resTahunan->assertStatus(200);
        $resTahunan->assertSee('Tahun: ' . date('Y'));

        // 5. Preview filter custom
        $resCustom = $this->actingAs($pimpinan)->get('/laporan/preview/transaksi-penjualan?filter_type=custom&tgl_awal=' . date('Y-01-01') . '&tgl_akhir=' . date('Y-m-d'));
        $resCustom->assertStatus(200);
        $resCustom->assertSee('s/d');
    }

    public function test_all_laporan_preview_types_render_successfully(): void
    {
        $pimpinan = User::where('peran', 'pimpinan')->first() ?? User::factory()->create(['peran' => 'pimpinan']);

        $types = [
            'transaksi-penjualan',
            'transaksi-servis',
            'produk-stok',
            'produk-terlaris',
            'servis-ringkasan',
            'servis-rekap',
            'komplain',
            'chatbot-analitik',
            'keuangan',
            'metode-pembayaran',
            'margin'
        ];

        foreach ($types as $type) {
            $response = $this->actingAs($pimpinan)->get('/laporan/preview/' . $type);
            $response->assertStatus(200);
            $response->assertSee('NUSANTARA JAYA COMPUTER');
            $response->assertSee('Unduh / Cetak PDF');
        }
    }

    public function test_laporan_cetak_pdf_streams_successfully(): void
    {
        $kasir = User::where('peran', 'kasir')->first() ?? User::factory()->create(['peran' => 'kasir']);

        $response = $this->actingAs($kasir)->get('/laporan/cetak/transaksi-penjualan?filter_type=bulanan&bulan=' . date('n') . '&tahun=' . date('Y'));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}

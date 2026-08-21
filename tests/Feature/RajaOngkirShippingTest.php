<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Ekspedisi;
use App\Services\RajaOngkirService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RajaOngkirShippingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Kategori $kategori;
    protected Produk $produkTinta;
    protected Produk $produkJasa;
    protected Produk $produkLaptop;
    protected Produk $produkRakitan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'customer_test@example.com',
            'peran' => 'pelanggan',
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin_test@example.com',
            'peran' => 'admin',
        ]);

        $this->kategori = Kategori::create(['nama_kategori' => 'LAPTOP']);
        $katTinta = Kategori::create(['nama_kategori' => 'TINTA']);
        $katRakitan = Kategori::create(['nama_kategori' => 'PC RAKITAN']);

        $this->produkTinta = Produk::create([
            'kategori_id' => $katTinta->id,
            'nama_produk' => 'Tinta Botol Epson 003 Black',
            'merk' => 'EPSON',
            'stok' => 20,
            'harga_beli' => 70000,
            'harga_jual' => 85000,
            'berat_gram' => 150,
            'ukuran_packing' => 'kecil',
        ]);

        $this->produkLaptop = Produk::create([
            'kategori_id' => $this->kategori->id,
            'nama_produk' => 'Laptop Asus Vivobook 14',
            'merk' => 'ASUS',
            'stok' => 5,
            'harga_beli' => 6000000,
            'harga_jual' => 6750000,
            'berat_gram' => 2200,
            'ukuran_packing' => 'besar',
        ]);

        $this->produkRakitan = Produk::create([
            'kategori_id' => $katRakitan->id,
            'nama_produk' => 'PC Gaming Full Set Intel i5',
            'merk' => 'Custom',
            'stok' => 2,
            'harga_beli' => 8000000,
            'harga_jual' => 9500000,
            'berat_gram' => 8500,
            'ukuran_packing' => 'ekstra_besar',
        ]);

        Ekspedisi::firstOrCreate(['nama_ekspedisi' => 'JNE']);
    }

    public function test_rajaongkir_service_calculates_packing_fee_tiers(): void
    {
        $service = app(RajaOngkirService::class);

        // Tier Kecil (Rp 15.000)
        $cartKecil = [
            $this->produkTinta->id => ['jumlah' => 2, 'harga' => 85000],
        ];
        $packingKecil = $service->calculatePackingCost($cartKecil);
        $this->assertEquals('kecil', $packingKecil['tier']);
        $this->assertEquals(15000, $packingKecil['biaya']);

        // Tier Besar (Rp 40.000)
        $cartBesar = [
            $this->produkTinta->id => ['jumlah' => 1, 'harga' => 85000],
            $this->produkLaptop->id => ['jumlah' => 1, 'harga' => 6750000],
        ];
        $packingBesar = $service->calculatePackingCost($cartBesar);
        $this->assertEquals('besar', $packingBesar['tier']);
        $this->assertEquals(40000, $packingBesar['biaya']);

        // Tier Ekstra Besar (Rp 50.000)
        $cartEkstra = [
            $this->produkRakitan->id => ['jumlah' => 1, 'harga' => 9500000],
        ];
        $packingEkstra = $service->calculatePackingCost($cartEkstra);
        $this->assertEquals('ekstra_besar', $packingEkstra['tier']);
        $this->assertEquals(50000, $packingEkstra['biaya']);
    }

    public function test_api_provinces_and_cities_endpoints(): void
    {
        $responseProv = $this->actingAs($this->user)->getJson(route('rajaongkir.provinces'));
        $responseProv->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertNotEmpty($responseProv->json('data'));

        // Test Kalimantan Selatan (ID: 13)
        $responseCities = $this->actingAs($this->user)->getJson(route('rajaongkir.cities', 13));
        $responseCities->assertStatus(200)
            ->assertJson(['status' => 'success']);
        
        $this->assertNotEmpty($responseCities->json('data'));
    }

    public function test_api_check_ongkir_with_cart_session(): void
    {
        $cart = [
            $this->produkTinta->id => [
                'nama' => $this->produkTinta->nama_produk,
                'harga' => $this->produkTinta->harga_jual,
                'jumlah' => 2,
                'foto' => null,
            ],
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['keranjang' => $cart])
            ->postJson(route('rajaongkir.check-ongkir'), [
                'destination_city_id' => 36, // Banjarmasin
                'courier' => 'jne',
            ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'subtotal',
                'total_weight_gram',
                'total_weight_kg',
                'packing' => ['tier', 'nama', 'biaya', 'deskripsi'],
                'shipping' => ['services'],
            ]);

        $this->assertEquals(170000, $response->json('subtotal'));
        $this->assertEquals(15000, $response->json('packing.biaya'));
    }

    public function test_customer_checkout_with_expedition_shipping_and_packing_fee(): void
    {
        $cart = [
            $this->produkLaptop->id => [
                'nama' => $this->produkLaptop->nama_produk,
                'harga' => $this->produkLaptop->harga_jual,
                'jumlah' => 1,
                'foto' => null,
            ],
        ];

        $subtotal = 6750000;
        $ongkir = 35000;
        $biayaPacking = 40000;
        $totalExpected = $subtotal + $ongkir + $biayaPacking;

        $response = $this->actingAs($this->user)
            ->withSession(['keranjang' => $cart])
            ->post(route('checkout'), [
                'metode_pengambilan' => 'diantar',
                'nama_ekspedisi' => 'JNE',
                'layanan_ekspedisi' => 'REG',
                'estimasi_pengiriman' => '2-3 hari',
                'provinsi_tujuan' => 'Jawa Timur',
                'kota_tujuan' => 'Kota Surabaya',
                'alamat_pengiriman' => 'Jl. Pemuda No. 45 Surabaya',
                'ongkir' => $ongkir,
                'biaya_packing' => $biayaPacking,
            ]);

        $this->assertDatabaseHas('transaksi', [
            'user_id' => $this->user->id,
            'metode_pengambilan' => 'diantar',
            'layanan_ekspedisi' => 'REG',
            'ongkir' => $ongkir,
            'biaya_packing' => $biayaPacking,
            'total_bayar' => $totalExpected,
            'kota_tujuan' => 'Kota Surabaya',
            'provinsi_tujuan' => 'Jawa Timur',
        ]);

        $trx = Transaksi::latest()->first();
        $this->assertNotNull($trx);
        $response->assertRedirect(route('pembayaran.form', $trx->id));
    }
}

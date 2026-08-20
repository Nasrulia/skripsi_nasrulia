<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Kategori;
use Database\Seeders\ProdukTintaSeeder;
use Database\Seeders\ProdukPrinterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TintaProductTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProdukPrinterSeeder::class);
        $this->seed(ProdukTintaSeeder::class);
    }

    /**
     * Test all 24 ink products are properly seeded.
     */
    public function test_all_ink_products_exist_in_database(): void
    {
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'TINTA']);

        // Check Epson 664 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 664 BLACK (T6641)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 664 CYAN (T6642)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 664 MAGENTA (T6643)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 664 YELLOW (T6644)']);

        // Check Epson 003 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 003 BLACK (003 BK)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 003 CYAN (003 C)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 003 MAGENTA (003 M)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA EPSON 003 YELLOW (003 Y)']);

        // Check Canon GI-790 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-790 BLACK (GI-790 BK)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-790 CYAN (GI-790 C)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-790 MAGENTA (GI-790 M)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-790 YELLOW (GI-790 Y)']);

        // Check Canon GI-71 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-71 BLACK (GI-71 BK)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-71 CYAN (GI-71 C)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-71 MAGENTA (GI-71 M)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA CANON GI-71 YELLOW (GI-71 Y)']);

        // Check Brother BTD100 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BTD100 BLACK (D100 BK)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BTD100 CYAN (D100 C)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BTD100 MAGENTA (D100 M)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BTD100 YELLOW (D100 Y)']);

        // Check Brother BTD60 & BT5000 series
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BTD60 BLACK (BTD60BK)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BT5000 CYAN (BT5000C)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BT5000 MAGENTA (BT5000M)']);
        $this->assertDatabaseHas('produk', ['nama_produk' => 'TINTA BROTHER BT5000 YELLOW (BT5000Y)']);

        $count = Produk::where('nama_produk', 'LIKE', '%TINTA%')->count();
        $this->assertEquals(24, $count);
    }

    /**
     * Test ink compatibility descriptions contain the right printer models.
     */
    public function test_ink_descriptions_have_correct_printer_models(): void
    {
        $epson664 = Produk::where('nama_produk', 'LIKE', '%EPSON 664%')->first();
        $this->assertStringContainsString('L121', $epson664->deskripsi);

        $epson003 = Produk::where('nama_produk', 'LIKE', '%EPSON 003%')->first();
        $this->assertStringContainsString('L3210', $epson003->deskripsi);
        $this->assertStringContainsString('L3211', $epson003->deskripsi);
        $this->assertStringContainsString('L3250', $epson003->deskripsi);
        $this->assertStringContainsString('L3251', $epson003->deskripsi);
        $this->assertStringContainsString('L5290', $epson003->deskripsi);

        $canon790 = Produk::where('nama_produk', 'LIKE', '%CANON GI-790%')->first();
        $this->assertStringContainsString('G1010', $canon790->deskripsi);
        $this->assertStringContainsString('G2010', $canon790->deskripsi);

        $canon71 = Produk::where('nama_produk', 'LIKE', '%CANON GI-71%')->first();
        $this->assertStringContainsString('G3730', $canon71->deskripsi);

        $brotherD60 = Produk::where('nama_produk', 'LIKE', '%BROTHER BTD60%')->first();
        $this->assertStringContainsString('T230', $brotherD60->deskripsi);
    }

    /**
     * Test searching ink on katalog.
     */
    public function test_katalog_search_ink(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'pelanggan@test.com'],
            ['name' => 'Pelanggan Test', 'password' => bcrypt('password'), 'peran' => 'pelanggan', 'email_verified_at' => now()]
        );

        $response = $this->actingAs($user)->get('/katalog?search=tinta+epson+003');
        $response->assertStatus(200);
        $response->assertSee('TINTA EPSON 003 BLACK');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukTintaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriTinta = Kategori::firstOrCreate(['nama_kategori' => 'TINTA']);

        $products = [
            // ==========================================
            // EPSON 664 SERIES (T6641 - T6644)
            // Harga Jual: Rp 90.000 | Harga Beli: Rp 85.000
            // Kompatibel: L121, L100, L110, L120, L200, L210, L220, L300, L310, L350, L355, L360, L365, L380, L385, L405, L455, L485, L550, L555, L565, L655, L1300, L1455, dll.
            // ==========================================
            [
                'nama_produk' => 'TINTA EPSON 664 BLACK (T6641)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 20,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_664_black.png',
                'deskripsi'   => 'Tinta botol original Epson 664 Black (Hitam / T6641) kapasitas 70ml. Diformulasikan khusus untuk printer Epson EcoTank seri L121, L100, L110, L120, L200, L210, L220, L300, L310, L350, L355, L360, L365, L380, L385, L405, L455, L485, L550, L555, L565, L655, L1300, L1455 dan seri printer tangki Epson L-Series generasi lama lainnya. Menghasilkan cetakan dokumen teks tajam pekat, cepat kering, dan menjaga printhead tetap awet.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 664 CYAN (T6642)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_664_cyan.png',
                'deskripsi'   => 'Tinta botol original Epson 664 Cyan (Biru / T6642) kapasitas 70ml. Kompatibel untuk printer Epson EcoTank seri L121, L100, L110, L120, L200, L210, L220, L300, L310, L350, L355, L360, L365, L380, L385, L405, L455, L485, L550, L555, L565, L655, L1300, L1455 dan seri printer tangki Epson L-Series lainnya. Memberikan warna cetak foto & grafis yang cerah dan tahan lama.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 664 MAGENTA (T6643)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_664_magenta.png',
                'deskripsi'   => 'Tinta botol original Epson 664 Magenta (Merah / T6643) kapasitas 70ml. Kompatibel untuk printer Epson EcoTank seri L121, L100, L110, L120, L200, L210, L220, L300, L310, L350, L355, L360, L365, L380, L385, L405, L455, L485, L550, L555, L565, L655, L1300, L1455 dan seri printer tangki Epson L-Series lainnya. Menghasilkan gradasi warna merah presisi dan tahan pudar.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 664 YELLOW (T6644)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_664_yellow.png',
                'deskripsi'   => 'Tinta botol original Epson 664 Yellow (Kuning / T6644) kapasitas 70ml. Kompatibel untuk printer Epson EcoTank seri L121, L100, L110, L120, L200, L210, L220, L300, L310, L350, L355, L360, L365, L380, L385, L405, L455, L485, L550, L555, L565, L655, L1300, L1455 dan seri printer tangki Epson L-Series lainnya. Menghadirkan warna kuning cerah dan hasil cetak berkualitas tinggi.',
            ],

            // ==========================================
            // EPSON 003 SERIES (003 BK, C, M, Y)
            // Harga Jual: Rp 90.000 | Harga Beli: Rp 85.000
            // Kompatibel: L3210, L3211, L3250, L3251, L5290, L1110, L1210, L1250, L3110, L3150, L5190, dll.
            // ==========================================
            [
                'nama_produk' => 'TINTA EPSON 003 BLACK (003 BK)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 20,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_003_black.png',
                'deskripsi'   => 'Tinta botol original Epson 003 Black (Hitam) kapasitas 65ml dengan desain tutup botol anti-tumpah (auto-stop). Dirancang untuk printer Epson EcoTank seri L3210, L3211, L3250, L3251, L5290, L1110, L1210, L1250, L3110, L3116, L3150, L3156, L5190, L5296, L4150, L4160, L6160, L6170, L6190 dan seri printer Epson EcoTank lainnya yang menggunakan tinta 003. Tinta pigmen berkualitas tinggi untuk teks tajam pekat dan tahan air.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 003 CYAN (003 C)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_003_cyan.png',
                'deskripsi'   => 'Tinta botol original Epson 003 Cyan (Biru) kapasitas 65ml dengan botol sistem kunci khusus (nozzle key). Kompatibel untuk printer Epson EcoTank L3210, L3211, L3250, L3251, L5290, L1110, L1210, L1250, L3110, L3150, L5190 dan seluruh printer Epson seri 003. Memberikan warna biru cemerlang dan hasil cetak foto tajam alami.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 003 MAGENTA (003 M)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_003_magenta.png',
                'deskripsi'   => 'Tinta botol original Epson 003 Magenta (Merah) kapasitas 65ml dengan sistem pengisian anti-tumpah. Kompatibel untuk printer Epson EcoTank L3210, L3211, L3250, L3251, L5290, L1110, L1210, L1250, L3110, L3150, L5190 dan seluruh printer Epson seri 003. Menghasilkan reproduksi warna merah yang hidup dan presisi.',
            ],
            [
                'nama_produk' => 'TINTA EPSON 003 YELLOW (003 Y)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'EPSON',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 90000,
                'foto'        => 'produk/tinta_epson_003_yellow.png',
                'deskripsi'   => 'Tinta botol original Epson 003 Yellow (Kuning) kapasitas 65ml dengan botol no-spill anti tumpah. Kompatibel untuk printer Epson EcoTank L3210, L3211, L3250, L3251, L5290, L1110, L1210, L1250, L3110, L3150, L5190 dan seluruh printer Epson seri 003. Menjamin warna kuning yang bersih, jernih, dan awet pada dokumen serta foto.',
            ],

            // ==========================================
            // CANON GI-790 SERIES (CANON 790 BK, C, M, Y)
            // Harga Jual: Rp 125.000 | Harga Beli: Rp 110.000
            // Kompatibel: G1010, G2010, G3010, G4010, G1000, G2000, G3000, G4000.
            // ==========================================
            [
                'nama_produk' => 'TINTA CANON GI-790 BLACK (GI-790 BK)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 20,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi790_black.png',
                'deskripsi'   => 'Tinta botol original Canon GI-790 Black (Hitam) kapasitas ekstra besar 135ml. Diformulasikan khusus untuk printer ink tank Canon Pixma G1010, G2010, G3010, G4010, G1000, G2000, G3000, G4000. Memberikan volume cetak dokumen hingga 6.000 halaman dengan teks hitam pekat, tajam, dan anti luntur.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-790 CYAN (GI-790 C)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi790_cyan.png',
                'deskripsi'   => 'Tinta botol original Canon GI-790 Cyan (Biru) kapasitas 70ml. Kompatibel untuk printer Canon Pixma seri G1010, G2010, G3010, G4010, G1000, G2000, G3000, G4000. Menghasilkan cetakan foto dan grafis berwarna biru cerah dengan ketahanan warna yang prima.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-790 MAGENTA (GI-790 M)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi790_magenta.png',
                'deskripsi'   => 'Tinta botol original Canon GI-790 Magenta (Merah) kapasitas 70ml. Kompatibel untuk printer Canon Pixma seri G1010, G2010, G3010, G4010, G1000, G2000, G3000, G4000. Memberikan saturasi warna merah yang kaya dan presisi pada setiap lembar cetakan.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-790 YELLOW (GI-790 Y)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi790_yellow.png',
                'deskripsi'   => 'Tinta botol original Canon GI-790 Yellow (Kuning) kapasitas 70ml. Kompatibel untuk printer Canon Pixma seri G1010, G2010, G3010, G4010, G1000, G2000, G3000, G4000. Menghasilkan spektrum warna kuning alami dan tajam untuk foto maupun dokumen presentasi.',
            ],

            // ==========================================
            // CANON GI-71 SERIES (CANON 71 BK, C, M, Y)
            // Harga Jual: Rp 125.000 | Harga Beli: Rp 110.000
            // Kompatibel: G3730, G1020, G2020, G3020, G3060, G1730, G2730, G2770, G3770, G4770.
            // ==========================================
            [
                'nama_produk' => 'TINTA CANON GI-71 BLACK (GI-71 BK)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 20,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi71_black.png',
                'deskripsi'   => 'Tinta botol original Canon GI-71 Black (Hitam) kapasitas 135ml dengan desain botol khusus anti-tumpah. Dirancang khusus untuk printer ink tank Canon Pixma G3730, G1020, G2020, G3020, G3060, G1730, G2730, G2770, G3770, G4770. Memberikan kualitas cetak teks hitam pekat setara cetak laser dan hemat pemakaian.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-71 CYAN (GI-71 C)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi71_cyan.png',
                'deskripsi'   => 'Tinta botol original Canon GI-71 Cyan (Biru) kapasitas 70ml dengan sistem pengisian no-squeeze. Kompatibel untuk printer Canon Pixma G3730, G1020, G2020, G3020, G3060, G1730, G2730, G2770, G3770, G4770. Menghasilkan cetakan grafis & foto beresolusi tinggi dengan ketajaman warna maksimal.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-71 MAGENTA (GI-71 M)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi71_magenta.png',
                'deskripsi'   => 'Tinta botol original Canon GI-71 Magenta (Merah) kapasitas 70ml. Kompatibel untuk printer Canon Pixma G3730, G1020, G2020, G3020, G3060, G1730, G2730, G2770, G3770, G4770. Formula tinta dye berkualitas tinggi untuk warna magenta yang pekat dan tahan lama.',
            ],
            [
                'nama_produk' => 'TINTA CANON GI-71 YELLOW (GI-71 Y)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'CANON',
                'stok'        => 15,
                'harga_beli'  => 110000,
                'harga_jual'  => 125000,
                'foto'        => 'produk/tinta_canon_gi71_yellow.png',
                'deskripsi'   => 'Tinta botol original Canon GI-71 Yellow (Kuning) kapasitas 70ml. Kompatibel untuk printer Canon Pixma G3730, G1020, G2020, G3020, G3060, G1730, G2730, G2770, G3770, G4770. Memberikan kecerahan warna kuning optimal dan menjaga printhead Canon tetap bersih serta lancar.',
            ],

            // ==========================================
            // BROTHER BTD100 / D100 SERIES (BK, C, M, Y)
            // Harga Jual: Rp 110.000 | Harga Beli: Rp 98.000
            // Kompatibel: HL-T4000DW, MFC-T4500DW, DCP-T720DW, MFC-T920DW, dll.
            // ==========================================
            [
                'nama_produk' => 'TINTA BROTHER BTD100 BLACK (D100 BK)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 20,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_btd100_black.png',
                'deskripsi'   => 'Tinta botol original Brother BTD100 Black (Hitam / D100 BK) ultra high yield kapasitas 108ml. Diformulasikan khusus untuk printer Brother Ink Tank format besar dan heavy-duty seperti Brother HL-T4000DW, MFC-T4500DW, DCP-T720DW, MFC-T920DW dan seri printer Brother Ink Tank System pendukung lainnya. Mampu mencetak ribuan halaman dengan teks hitam pekat, tajam, dan tidak mudah luntur.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BTD100 CYAN (D100 C)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_btd100_cyan.png',
                'deskripsi'   => 'Tinta botol original Brother BTD100 Cyan (Biru / D100 C) kapasitas 48.8ml. Kompatibel untuk printer Brother Ink Tank seri HL-T4000DW, MFC-T4500DW, DCP-T720DW, MFC-T920DW dan seri printer Brother terkait lainnya. Menghasilkan cetakan warna biru tajam dan akurat untuk gambar teknis, poster, dan dokumen warna.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BTD100 MAGENTA (D100 M)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_btd100_magenta.png',
                'deskripsi'   => 'Tinta botol original Brother BTD100 Magenta (Merah / D100 M) kapasitas 48.8ml. Kompatibel untuk printer Brother Ink Tank seri HL-T4000DW, MFC-T4500DW, DCP-T720DW, MFC-T920DW dan seri printer Brother terkait lainnya. Menghasilkan warna magenta yang solid, kaya gradasi, dan awet.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BTD100 YELLOW (D100 Y)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_btd100_yellow.png',
                'deskripsi'   => 'Tinta botol original Brother BTD100 Yellow (Kuning / D100 Y) kapasitas 48.8ml. Kompatibel untuk printer Brother Ink Tank seri HL-T4000DW, MFC-T4500DW, DCP-T720DW, MFC-T920DW dan seri printer Brother terkait lainnya. Menghadirkan warna kuning cerah cemerlang dengan formula ramah printhead.',
            ],

            // ==========================================
            // BROTHER BTD60BK & BT5000 COLOUR SERIES
            // Harga Jual: Rp 110.000 | Harga Beli: Rp 98.000
            // Kompatibel: T220, T230, T310, T420W, T430W, T510W, T520W, T710W, T720DW, T730W, T820DW, T910DW, T920DW.
            // ==========================================
            [
                'nama_produk' => 'TINTA BROTHER BTD60 BLACK (BTD60BK)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 20,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_btd60_black.png',
                'deskripsi'   => 'Tinta botol original Brother BTD60BK Black (Hitam) kapasitas besar 108ml (mampu cetak hingga 6.500-7.500 halaman). Dirancang untuk printer Brother Ink Tank DCP-T220, DCP-T230, DCP-T310, DCP-T420W, DCP-T430W, DCP-T510W, DCP-T520W, DCP-T710W, DCP-T720DW, DCP-T730W, DCP-T820DW, MFC-T910DW, MFC-T920DW. Memberikan hasil cetak dokumen hitam pekat, tajam, dan efisien biaya.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BT5000 CYAN (BT5000C)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_bt5000_cyan.png',
                'deskripsi'   => 'Tinta botol original Brother BT5000 Cyan (Biru / BT5000C) kapasitas 48.8ml (mampu cetak hingga 5.000 halaman warna). Kompatibel untuk printer Brother Ink Tank DCP-T220, DCP-T230, DCP-T310, DCP-T420W, DCP-T430W, DCP-T510W, DCP-T520W, DCP-T710W, DCP-T720DW, DCP-T730W, DCP-T820DW, MFC-T910DW, MFC-T920DW, HL-T4000DW, MFC-T4500DW. Warna biru cerah, tajam, dan tahan lama.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BT5000 MAGENTA (BT5000M)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_bt5000_magenta.png',
                'deskripsi'   => 'Tinta botol original Brother BT5000 Magenta (Merah / BT5000M) kapasitas 48.8ml (mampu cetak hingga 5.000 halaman warna). Kompatibel untuk printer Brother Ink Tank DCP-T220, DCP-T230, DCP-T310, DCP-T420W, DCP-T430W, DCP-T510W, DCP-T520W, DCP-T710W, DCP-T720DW, DCP-T730W, DCP-T820DW, MFC-T910DW, MFC-T920DW, HL-T4000DW, MFC-T4500DW. Menghasilkan cetakan foto dan grafis merah presisi tanpa clogging.',
            ],
            [
                'nama_produk' => 'TINTA BROTHER BT5000 YELLOW (BT5000Y)',
                'kategori_id' => $kategoriTinta->id,
                'merk'        => 'BROTHER',
                'stok'        => 15,
                'harga_beli'  => 98000,
                'harga_jual'  => 110000,
                'foto'        => 'produk/tinta_brother_bt5000_yellow.png',
                'deskripsi'   => 'Tinta botol original Brother BT5000 Yellow (Kuning / BT5000Y) kapasitas 48.8ml (mampu cetak hingga 5.000 halaman warna). Kompatibel untuk printer Brother Ink Tank DCP-T220, DCP-T230, DCP-T310, DCP-T420W, DCP-T430W, DCP-T510W, DCP-T520W, DCP-T710W, DCP-T720DW, DCP-T730W, DCP-T820DW, MFC-T910DW, MFC-T920DW, HL-T4000DW, MFC-T4500DW. Menghasilkan warna kuning jernih dengan perlindungan optimal untuk printhead.',
            ],
        ];

        foreach ($products as $item) {
            Produk::updateOrCreate(
                ['nama_produk' => $item['nama_produk']],
                $item
            );
        }
    }
}

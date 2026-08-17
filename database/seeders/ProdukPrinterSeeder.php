<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukPrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriPrinter = Kategori::firstOrCreate(['nama_kategori' => 'PRINTER']);

        $products = [
            [
                'model_code'  => 'EPSON LQ-310',
                'nama_produk' => 'PRINTER EPSON LQ-310',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'EPSON',
                'stok'        => 5,
                'harga_jual'  => 3175000,
                'harga_beli'  => 3100000, // Margin Rp 75.000
                'foto'        => 'produk/epson_lq310.png',
                'deskripsi'   => 'Printer Dot Matrix Epson LQ-310 24-Pin, kecepatan cetak tinggi dan handal.',
            ],
            [
                'model_code'  => 'EPSON L121',
                'nama_produk' => 'PRINTER EPSON L121 (PRINT)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'EPSON',
                'stok'        => 5,
                'harga_jual'  => 1515000,
                'harga_beli'  => 1440000, // Margin Rp 75.000
                'foto'        => 'produk/epson_l121.png',
                'deskripsi'   => 'Printer Epson EcoTank L121 Single Function Print.',
            ],
            [
                'model_code'  => 'EPSON L3211',
                'nama_produk' => 'PRINTER EPSON L3211 (PRINT, SCAN, FOTOCOPY, 3THN TKDN)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'EPSON',
                'stok'        => 5,
                'harga_jual'  => 2135000,
                'harga_beli'  => 2060000, // Margin Rp 75.000
                'foto'        => 'produk/epson_l3211.png',
                'deskripsi'   => 'Printer All-in-One Epson EcoTank L3211 Print, Scan, Fotocopy, Garansi 3 Tahun TKDN.',
            ],
            [
                'model_code'  => 'EPSON L3251',
                'nama_produk' => 'PRINTER EPSON L3251 (PRINT, SCAN, FOTOCOPY, WIFI, 3THN TKDN)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'EPSON',
                'stok'        => 5,
                'harga_jual'  => 2525000,
                'harga_beli'  => 2450000, // Margin Rp 75.000
                'foto'        => 'produk/epson_l3251.png',
                'deskripsi'   => 'Printer All-in-One Epson EcoTank L3251 Print, Scan, Fotocopy, Wi-Fi Direct, Garansi 3 Tahun TKDN.',
            ],
            [
                'model_code'  => 'EPSON L5290',
                'nama_produk' => 'PRINTER EPSON L5290 (PRINT, SCAN, FOTOCOPY UP TO F4, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'EPSON',
                'stok'        => 5,
                'harga_jual'  => 3875000,
                'harga_beli'  => 3800000, // Margin Rp 75.000
                'foto'        => 'produk/epson_l5290.png',
                'deskripsi'   => 'Printer All-in-One Epson EcoTank L5290 Print, Scan, Copy up to F4, Fax, Wi-Fi Direct & Ethernet.',
            ],
            [
                'model_code'  => 'CANON E470',
                'nama_produk' => 'PRINTER CANON E470 (PRINT, SCAN, FOTOCOPY, WIFI, CATRIDGE)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 995000,
                'harga_beli'  => 920000, // Margin Rp 75.000
                'foto'        => 'produk/canon_e470.png',
                'deskripsi'   => 'Printer Multifungsi Canon Pixma E470 Print, Scan, Copy dengan Wi-Fi & Catridge Hemat.',
            ],
            [
                'model_code'  => 'CANON G1010',
                'nama_produk' => 'PRINTER CANON G1010 (PRINT)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 1115000,
                'harga_beli'  => 1040000, // Margin Rp 75.000
                'foto'        => 'produk/canon_g1010.png',
                'deskripsi'   => 'Printer Canon Pixma G1010 Single Function Ink Tank Print.',
            ],
            [
                'model_code'  => 'CANON G2010',
                'nama_produk' => 'PRINTER CANON G2010 (PRINT, SCAN, FOTOCOPY)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 1675000,
                'harga_beli'  => 1600000, // Margin Rp 75.000
                'foto'        => 'produk/canon_g2010.png',
                'deskripsi'   => 'Printer All-in-One Canon Pixma G2010 Ink Tank Print, Scan, Copy.',
            ],
            [
                'model_code'  => 'CANON G3010',
                'nama_produk' => 'PRINTER CANON G3010 (PRINT, SCAN, FOTOCOPY, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 2150000,
                'harga_beli'  => 2075000, // Margin Rp 75.000
                'foto'        => 'produk/canon_g3010.png',
                'deskripsi'   => 'Printer Wireless All-in-One Canon Pixma G3010 Ink Tank Print, Scan, Copy, Wi-Fi.',
            ],
            [
                'model_code'  => 'CANON G3730',
                'nama_produk' => 'PRINTER CANON G3730 (PRINT, SCAN, FOTOCOPY, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 2150000,
                'harga_beli'  => 2075000, // Margin Rp 75.000
                'foto'        => 'produk/canon_g3730.png',
                'deskripsi'   => 'Printer Wireless All-in-One Canon Pixma G3730 Ink Tank Print, Scan, Copy, Wi-Fi.',
            ],
            [
                'model_code'  => 'CANON G4010',
                'nama_produk' => 'PRINTER CANON G4010 (PRINT, SCAN, FOTOCOPY UP TO F4, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'CANON',
                'stok'        => 5,
                'harga_jual'  => 3200000,
                'harga_beli'  => 3125000, // Margin Rp 75.000
                'foto'        => 'produk/canon_g4010.png',
                'deskripsi'   => 'Printer Wireless All-in-One Canon Pixma G4010 Ink Tank Print, Scan, Copy F4, Fax, Wi-Fi, ADF.',
            ],
            [
                'model_code'  => 'BROTHER T230',
                'nama_produk' => 'PRINTER BROTHER T230 (PRINT, SCAN, FOTOCOPY)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'BROTHER',
                'stok'        => 5,
                'harga_jual'  => 1775000,
                'harga_beli'  => 1700000, // Margin Rp 75.000
                'foto'        => 'produk/brother_t230.png',
                'deskripsi'   => 'Printer All-in-One Brother DCP-T220 / DCP-T230 Ink Tank Print, Scan, Copy.',
            ],
            [
                'model_code'  => 'BROTHER T430W',
                'nama_produk' => 'PRINTER BROTHER T430W (PRINT, SCAN, FOTOCOPY, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'BROTHER',
                'stok'        => 5,
                'harga_jual'  => 2075000,
                'harga_beli'  => 2000000, // Margin Rp 75.000
                'foto'        => 'produk/brother_t430w.png',
                'deskripsi'   => 'Printer Wireless All-in-One Brother DCP-T420W / DCP-T430W Ink Tank Print, Scan, Copy, Wi-Fi.',
            ],
            [
                'model_code'  => 'BROTHER T730W',
                'nama_produk' => 'PRINTER BROTHER T730W (PRINT, SCAN, FOTOCOPY UP TO F4, WIFI)',
                'kategori_id' => $kategoriPrinter->id,
                'merk'        => 'BROTHER',
                'stok'        => 5,
                'harga_jual'  => 3825000,
                'harga_beli'  => 3750000, // Margin Rp 75.000
                'foto'        => 'produk/brother_t730w.png',
                'deskripsi'   => 'Printer Wireless All-in-One Brother DCP-T720DW / DCP-T730W Ink Tank Print, Scan, Copy F4, Wi-Fi, Auto Duplex, ADF.',
            ],
        ];

        foreach ($products as $item) {
            $modelCode = $item['model_code'];
            unset($item['model_code']);

            // Apabila sudah pernah diupload (berdasarkan model_code atau nama_produk), jangan dimasukkan lagi
            $exists = Produk::where('nama_produk', 'LIKE', '%' . $modelCode . '%')->exists();

            if (!$exists) {
                Produk::create($item);
            }
        }
    }
}

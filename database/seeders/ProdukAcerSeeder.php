<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukAcerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLaptop = Kategori::firstOrCreate(['nama_kategori' => 'LAPTOP']);

        $products = [
            [
                'nama_produk' => 'LAPTOP ACER LITE AL14-36P INTEL N4500 RAM 8GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 7300000,
                'harga_beli'  => 6950000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite.png',
                'deskripsi'   => 'Laptop ACER Lite AL14-36P Intel N4500, RAM 8GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER LITE AL14-53M INTEL CORE I3-1315U RAM 8GB SSD 512GB LAYAR 14" WUXGA WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 8700000,
                'harga_beli'  => 8350000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite.png',
                'deskripsi'   => 'Laptop ACER Lite AL14-53M Intel Core i3-1315U, RAM 8GB, SSD 512GB, Layar 14 Inch WUXGA, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER AL14 CORE 3-N355 RAM 8GB SSD 256GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 8100000,
                'harga_beli'  => 7750000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite.png',
                'deskripsi'   => 'Laptop ACER AL14 Intel Core 3-N355, RAM 8GB, SSD 256GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER AL14 CORE 3-N355 RAM 8GB SSD 512GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 8500000,
                'harga_beli'  => 8150000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite.png',
                'deskripsi'   => 'Laptop ACER AL14 Intel Core 3-N355, RAM 8GB, SSD 512GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER LITE AL14 AMD RYZEN 3-5300U RAM 8GB SSD 512GB LAYAR 14" WUXGA WIN 11 + OHS (PINK)',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 7725000,
                'harga_beli'  => 7375000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite_pink.png',
                'deskripsi'   => 'Laptop ACER Lite AL14 AMD Ryzen 3-5300U, RAM 8GB, SSD 512GB, Layar 14 Inch WUXGA, Warna Pink, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER LITE AL14-45P (PINK) AMD RYZEN 3-5400U RAM 8GB SSD 512GB LAYAR 14" WUXGA WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 7850000,
                'harga_beli'  => 7500000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite_pink.png',
                'deskripsi'   => 'Laptop ACER Lite AL14-45P AMD Ryzen 3-5400U, RAM 8GB, SSD 512GB, Layar 14 Inch WUXGA, Warna Pink, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER LITE AL15 AMD RYZEN 5-7430U RAM 8GB SSD 512GB LAYAR 15.6" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 9100000,
                'harga_beli'  => 8750000, // Margin Rp 350.000
                'foto'        => 'produk/acer_aspire_lite.png',
                'deskripsi'   => 'Laptop ACER Lite AL15 AMD Ryzen 5-7430U, RAM 8GB, SSD 512GB, Layar 15.6 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER TRAVELMATE P40 INTEL CORE I5-1335U RAM 16GB SSD 512GB LAYAR 14" WUXGA WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 9600000,
                'harga_beli'  => 9200000, // Margin Rp 400.000
                'foto'        => 'produk/acer_travelmate_p40.png',
                'deskripsi'   => 'Laptop ACER TravelMate P40 Intel Core i5-1335U, RAM 16GB, SSD 512GB, Layar 14 Inch WUXGA, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ACER NITRO LITE 16 NL16-71G INTEL CORE 5-210H RAM 16GB SSD 512GB NVIDIA RTX 3050 6GB LAYAR 15.6" WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ACER',
                'stok'        => 5,
                'harga_jual'  => 12800000,
                'harga_beli'  => 12150000, // Margin Rp 650.000
                'foto'        => 'produk/acer_nitro_16.png',
                'deskripsi'   => 'Laptop Gaming ACER Nitro Lite 16 NL16-71G Intel Core 5-210H, RAM 16GB, SSD 512GB, NVIDIA GeForce RTX 3050 6GB, Layar 15.6 Inch, Windows 11 + OHS.',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukAxiooSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLaptop = Kategori::firstOrCreate(['nama_kategori' => 'LAPTOP']);

        $products = [
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE X1 AMD ATHLON 3150C RAM 8GB SSD 256GB LAYAR 14" WIN',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 5500000,
                'harga_beli'  => 5250000, // Margin Rp 250.000
                'foto'        => 'produk/axioo_hype_1.png',
                'deskripsi'   => 'Laptop AXIOO Hype X1 AMD Athlon 3150C, RAM 8GB, SSD 256GB, Layar 14 Inch, Windows.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 1 INTEL N4020 RAM 8GB SSD 256GB LAYAR 14" WIN',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 4850000,
                'harga_beli'  => 4600000, // Margin Rp 250.000
                'foto'        => 'produk/axioo_hype_1.png',
                'deskripsi'   => 'Laptop AXIOO Hype 1 Intel N4020, RAM 8GB, SSD 256GB, Layar 14 Inch, Windows.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 1 INTEL N4020 RAM 4GB SSD 128GB LAYAR 14" WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 3550000,
                'harga_beli'  => 3300000, // Margin Rp 250.000
                'foto'        => 'produk/axioo_hype_1.png',
                'deskripsi'   => 'Laptop AXIOO Hype 1 Intel N4020, RAM 4GB, SSD 128GB, Layar 14 Inch, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 1 INTEL N4020 RAM 8GB SSD 128GB LAYAR 14" WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 4250000,
                'harga_beli'  => 4000000, // Margin Rp 250.000
                'foto'        => 'produk/axioo_hype_1.png',
                'deskripsi'   => 'Laptop AXIOO Hype 1 Intel N4020, RAM 8GB, SSD 128GB, Layar 14 Inch, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE X1 AMD ATHLON 3150C RAM 8GB SSD 128GB LAYAR 14" WINDOWS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 5150000,
                'harga_beli'  => 4900000, // Margin Rp 250.000
                'foto'        => 'produk/axioo_hype_1.png',
                'deskripsi'   => 'Laptop AXIOO Hype X1 AMD Athlon 3150C, RAM 8GB, SSD 128GB, Layar 14 Inch, Windows.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 5 X3 AMD RYZEN 5-3500U RAM 8GB SSD 256GB LAYAR 14" FHD IPS WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 6250000,
                'harga_beli'  => 5900000, // Margin Rp 350.000
                'foto'        => 'produk/axioo_hype_5.png',
                'deskripsi'   => 'Laptop AXIOO Hype 5 X3 AMD Ryzen 5-3500U, RAM 8GB, SSD 256GB, Layar 14 Inch FHD IPS, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 5 X5-2 AMD RYZEN 5-7430U RAM 8GB SSD 256GB LAYAR 14" FHD IPS WIN',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 6850000,
                'harga_beli'  => 6500000, // Margin Rp 350.000
                'foto'        => 'produk/axioo_hype_5.png',
                'deskripsi'   => 'Laptop AXIOO Hype 5 X5-2 AMD Ryzen 5-7430U, RAM 8GB, SSD 256GB, Layar 14 Inch FHD IPS, Windows.',
            ],
            [
                'nama_produk' => 'LAPTOP AXIOO HYPE 5 X6 AMD RYZEN 5-6600H RAM 16GB SSD 512GB LAYAR 14" FHD IPS WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'AXIOO',
                'stok'        => 5,
                'harga_jual'  => 8850000,
                'harga_beli'  => 8500000, // Margin Rp 350.000
                'foto'        => 'produk/axioo_hype_5.png',
                'deskripsi'   => 'Laptop AXIOO Hype 5 X6 AMD Ryzen 5-6600H, RAM 16GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11.',
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

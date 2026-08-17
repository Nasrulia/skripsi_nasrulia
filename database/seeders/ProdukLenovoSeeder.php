<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukLenovoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLaptop = Kategori::firstOrCreate(['nama_kategori' => 'LAPTOP']);

        $products = [
            [
                'nama_produk' => 'LAPTOP LENOVO V14 G4 AMD RYZEN 3-7320U RAM 8GB SSD 256GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 7250000,
                'harga_beli'  => 6900000, // Margin Rp 350.000
                'foto'        => 'produk/lenovo_v14.png',
                'deskripsi'   => 'Laptop LENOVO V14 G4 AMD Ryzen 3-7320U, RAM 8GB, SSD 256GB, Layar 14 Inch FHD, Windows 11 + Office.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO IP SLIM 1 15AMN7 AMD RYZEN 5-7520U RAM 8GB SSD 256GB LAYAR 15.6" WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 7750000,
                'harga_beli'  => 7400000, // Margin Rp 350.000
                'foto'        => 'produk/lenovo_ideapad_slim.png',
                'deskripsi'   => 'Laptop LENOVO IdeaPad Slim 1 15AMN7 AMD Ryzen 5-7520U, RAM 8GB, SSD 256GB, Layar 15.6 Inch, Windows 11 + Office.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO IP SLIM 3 INTEL CORE I3-1315U RAM 8GB SSD 256GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 8000000,
                'harga_beli'  => 7650000, // Margin Rp 350.000
                'foto'        => 'produk/lenovo_ideapad_slim.png',
                'deskripsi'   => 'Laptop LENOVO IdeaPad Slim 3 Intel Core i3-1315U, RAM 8GB, SSD 256GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO IP SLIM 3 INTEL CORE I3-1315U RAM 8GB SSD 512GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 8600000,
                'harga_beli'  => 8250000, // Margin Rp 350.000
                'foto'        => 'produk/lenovo_ideapad_slim.png',
                'deskripsi'   => 'Laptop LENOVO IdeaPad Slim 3 Intel Core i3-1315U, RAM 8GB, SSD 512GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO V14 G4 AMD RYZEN 5-7520U RAM 16GB SSD 512GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 10300000,
                'harga_beli'  => 9920000, // Margin Rp 380.000
                'foto'        => 'produk/lenovo_v14.png',
                'deskripsi'   => 'Laptop LENOVO V14 G4 AMD Ryzen 5-7520U, RAM 16GB, SSD 512GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO IP SLIM 3 INTEL CORE I5-13420H RAM 8GB SSD 512GB LAYAR 14" WUXGA WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 9775000,
                'harga_beli'  => 9375000, // Margin Rp 400.000
                'foto'        => 'produk/lenovo_ideapad_slim.png',
                'deskripsi'   => 'Laptop LENOVO IdeaPad Slim 3 Intel Core i5-13420H, RAM 8GB, SSD 512GB, Layar 14 Inch WUXGA, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP LENOVO LOQ 15 AMD RYZEN 7-170 RAM 16GB SSD 512GB NVIDIA RTX 3050 6GB LAYAR 15.6" 144HZ WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'LENOVO',
                'stok'        => 5,
                'harga_jual'  => 15100000,
                'harga_beli'  => 14450000, // Margin Rp 650.000
                'foto'        => 'produk/lenovo_loq_15.png',
                'deskripsi'   => 'Laptop Gaming LENOVO LOQ 15 AMD Ryzen 7-170, RAM 16GB, SSD 512GB, NVIDIA GeForce RTX 3050 6GB, Layar 15.6 Inch 144Hz FHD, Windows 11 + OHS.',
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

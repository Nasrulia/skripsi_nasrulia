<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukAsusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLaptop = Kategori::firstOrCreate(['nama_kategori' => 'LAPTOP']);

        $products = [
            [
                'nama_produk' => 'LAPTOP ASUS X1404VA (INTER) INTEL CORE I3-1315U RAM 8GB SSD 512GB LAYAR 14" WIN + OFFICE',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 7650000,
                'harga_beli'  => 7300000, // Margin Rp 350.000
                'foto'        => 'produk/asus_x1404.png',
                'deskripsi'   => 'Laptop ASUS X1404VA (INTER) Intel Core i3-1315U, RAM 8GB, SSD 512GB, Layar 14 Inch, Windows + Office.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS A1404VA-VIPS INTEL CORE I3-1315U RAM 8GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 9300000,
                'harga_beli'  => 8950000, // Margin Rp 350.000
                'foto'        => 'produk/asus_x1404.png',
                'deskripsi'   => 'Laptop ASUS A1404VA-VIPS Intel Core i3-1315U, RAM 8GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS E1404FA-FHD AMD RYZEN 3-7320U RAM 8GB SSD 256GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 8000000,
                'harga_beli'  => 7650000, // Margin Rp 350.000
                'foto'        => 'produk/asus_e1404.png',
                'deskripsi'   => 'Laptop ASUS E1404FA-FHD AMD Ryzen 3-7320U, RAM 8GB, SSD 256GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS E1404FA-FHD AMD RYZEN 3-7320U RAM 8GB SSD 512GB LAYAR 14" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 8300000,
                'harga_beli'  => 7950000, // Margin Rp 350.000
                'foto'        => 'produk/asus_e1404.png',
                'deskripsi'   => 'Laptop ASUS E1404FA-FHD AMD Ryzen 3-7320U, RAM 8GB, SSD 512GB, Layar 14 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS E1404FA AMD RYZEN 5-7520U RAM 8GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 9600000,
                'harga_beli'  => 9250000, // Margin Rp 350.000
                'foto'        => 'produk/asus_e1404.png',
                'deskripsi'   => 'Laptop ASUS E1404FA AMD Ryzen 5-7520U, RAM 8GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS E1404FA AMD RYZEN 5-7520U RAM 16GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 11150000,
                'harga_beli'  => 10770000, // Margin Rp 380.000
                'foto'        => 'produk/asus_e1404.png',
                'deskripsi'   => 'Laptop ASUS E1404FA AMD Ryzen 5-7520U, RAM 16GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS A1404VAP CORE I5-120U RAM 8GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 10650000,
                'harga_beli'  => 10250000, // Margin Rp 400.000
                'foto'        => 'produk/asus_a1404vap.png',
                'deskripsi'   => 'Laptop ASUS A1404VAP Intel Core i5-120U, RAM 8GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS A1404VAP CORE I5-120U RAM 16GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 11875000,
                'harga_beli'  => 11450000, // Margin Rp 425.000
                'foto'        => 'produk/asus_a1404vap.png',
                'deskripsi'   => 'Laptop ASUS A1404VAP Intel Core i5-120U, RAM 16GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS A1404VAP CORE I7-150U RAM 16GB SSD 512GB LAYAR 14" FHD IPS WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 14650000,
                'harga_beli'  => 14200000, // Margin Rp 450.000
                'foto'        => 'produk/asus_a1404vap.png',
                'deskripsi'   => 'Laptop ASUS A1404VAP Intel Core i7-150U, RAM 16GB, SSD 512GB, Layar 14 Inch FHD IPS, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS TUF GAMING A15 FA506NCQ AMD RYZEN 7-170 RAM 8GB SSD 512GB NVIDIA RTX 3050 4GB LAYAR 15,6" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 13100000,
                'harga_beli'  => 12450000, // Margin Rp 650.000
                'foto'        => 'produk/asus_tuf_a15.png',
                'deskripsi'   => 'Laptop Gaming ASUS TUF Gaming A15 FA506NCQ AMD Ryzen 7-170, RAM 8GB, SSD 512GB, NVIDIA GeForce RTX 3050 4GB, Layar 15.6 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS TUF GAMING A15 FA506NCQ AMD RYZEN 7-170 RAM 16GB SSD 512GB NVIDIA RTX 3050 4GB LAYAR 15,6" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 15100000,
                'harga_beli'  => 14450000, // Margin Rp 650.000
                'foto'        => 'produk/asus_tuf_a15.png',
                'deskripsi'   => 'Laptop Gaming ASUS TUF Gaming A15 FA506NCQ AMD Ryzen 7-170, RAM 16GB, SSD 512GB, NVIDIA GeForce RTX 3050 4GB, Layar 15.6 Inch FHD, Windows 11 + OHS.',
            ],
            [
                'nama_produk' => 'LAPTOP ASUS TUF GAMING A15 FA506NCG AMD RYZEN 7-7445HS RAM 8GB SSD 512GB NVIDIA RTX 3050 4GB LAYAR 15,6" FHD WIN 11 + OHS',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ASUS',
                'stok'        => 5,
                'harga_jual'  => 12900000,
                'harga_beli'  => 12250000, // Margin Rp 650.000
                'foto'        => 'produk/asus_tuf_a15.png',
                'deskripsi'   => 'Laptop Gaming ASUS TUF Gaming A15 FA506NCG AMD Ryzen 7-7445HS, RAM 8GB, SSD 512GB, NVIDIA GeForce RTX 3050 4GB, Layar 15.6 Inch FHD, Windows 11 + OHS.',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class KomponenRakitanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Kategori
        $catCpu = Kategori::firstOrCreate(['nama_kategori' => 'Processor']);
        $catMobo = Kategori::firstOrCreate(['nama_kategori' => 'Motherboard']);
        $catRam = Kategori::firstOrCreate(['nama_kategori' => 'RAM']);
        $catSsd = Kategori::firstOrCreate(['nama_kategori' => 'SSD']);
        $catCase = Kategori::firstOrCreate(['nama_kategori' => 'Casing']);
        $catWifi = Kategori::firstOrCreate(['nama_kategori' => 'Wifi Adapter']);
        $catKbMouse = Kategori::firstOrCreate(['nama_kategori' => 'Keyboard Mouse']);
        $catVga = Kategori::firstOrCreate(['nama_kategori' => 'VGA']);

        // 2. Buat Produk
        $components = [
            // Processors
            [
                'kategori_id' => $catCpu->id,
                'nama_produk' => 'Intel Core i3-12100F Processor',
                'merk' => 'Intel',
                'stok' => 10,
                'harga_beli' => 1000000,
                'harga_jual' => 1250000,
                'deskripsi' => 'Processor Intel Core i3-12100F LGA1700 4 Cores 8 Threads. Butuh GPU diskrit.',
            ],
            [
                'kategori_id' => $catCpu->id,
                'nama_produk' => 'Intel Core i5-12400F Processor',
                'merk' => 'Intel',
                'stok' => 8,
                'harga_beli' => 1500000,
                'harga_jual' => 1800000,
                'deskripsi' => 'Processor Intel Core i5-12400F LGA1700 6 Cores 12 Threads. Butuh GPU diskrit.',
            ],
            [
                'kategori_id' => $catCpu->id,
                'nama_produk' => 'AMD Ryzen 3 4100 Processor',
                'merk' => 'AMD',
                'stok' => 5,
                'harga_beli' => 800000,
                'harga_jual' => 950000,
                'deskripsi' => 'Processor AMD Ryzen 3 4100 AM4 4 Cores 8 Threads. Butuh GPU diskrit.',
            ],
            [
                'kategori_id' => $catCpu->id,
                'nama_produk' => 'AMD Ryzen 5 5600G Processor',
                'merk' => 'AMD',
                'stok' => 7,
                'harga_beli' => 1600000,
                'harga_jual' => 1900000,
                'deskripsi' => 'Processor AMD Ryzen 5 5600G AM4 6 Cores 12 Threads dengan Integrated Radeon Graphics.',
            ],

            // Motherboards
            [
                'kategori_id' => $catMobo->id,
                'nama_produk' => 'MSI PRO H610M-E DDR4 Motherboard',
                'merk' => 'MSI',
                'stok' => 6,
                'harga_beli' => 850000,
                'harga_jual' => 980000,
                'deskripsi' => 'Motherboard Socket LGA1700 DDR4, cocok untuk Intel Gen 12th/13th/14th.',
            ],
            [
                'kategori_id' => $catMobo->id,
                'nama_produk' => 'ASRock B550M-HDV Motherboard',
                'merk' => 'ASRock',
                'stok' => 4,
                'harga_beli' => 1100000,
                'harga_jual' => 1350000,
                'deskripsi' => 'Motherboard Socket AM4 DDR4, chipset B550 cocok untuk AMD Ryzen.',
            ],
            [
                'kategori_id' => $catMobo->id,
                'nama_produk' => 'ASRock A320M-HDV Motherboard',
                'merk' => 'ASRock',
                'stok' => 5,
                'harga_beli' => 650000,
                'harga_jual' => 780000,
                'deskripsi' => 'Motherboard Socket AM4 DDR4 hemat, chipset A320 untuk Ryzen 3/5.',
            ],

            // RAM
            [
                'kategori_id' => $catRam->id,
                'nama_produk' => 'Kingston Fury Beast DDR4 8GB RAM',
                'merk' => 'Kingston',
                'stok' => 20,
                'harga_beli' => 300000,
                'harga_jual' => 350000,
                'deskripsi' => 'Memory RAM DDR4 8GB 3200MHz Single Channel PC4-25600.',
            ],
            [
                'kategori_id' => $catRam->id,
                'nama_produk' => 'Corsair Vengeance LPX DDR4 16GB RAM',
                'merk' => 'Corsair',
                'stok' => 15,
                'harga_beli' => 580000,
                'harga_jual' => 680000,
                'deskripsi' => 'Memory RAM DDR4 16GB (2x8GB) 3200MHz Dual Channel PC4-25600.',
            ],

            // SSD
            [
                'kategori_id' => $catSsd->id,
                'nama_produk' => 'Kingston NV2 NVMe 512GB SSD',
                'merk' => 'Kingston',
                'stok' => 12,
                'harga_beli' => 480000,
                'harga_jual' => 580000,
                'deskripsi' => 'M.2 NVMe PCIe Gen 4.0 SSD 512GB read up to 3500MB/s.',
            ],
            [
                'kategori_id' => $catSsd->id,
                'nama_produk' => 'Samsung 980 NVMe 1TB SSD',
                'merk' => 'Samsung',
                'stok' => 8,
                'harga_beli' => 950000,
                'harga_jual' => 1150000,
                'deskripsi' => 'M.2 NVMe PCIe Gen 3.0 SSD 1TB read up to 3500MB/s premium storage.',
            ],

            // Casing
            [
                'kategori_id' => $catCase->id,
                'nama_produk' => 'Casing Simbadda Standard + PSU 450W',
                'merk' => 'Simbadda',
                'stok' => 10,
                'harga_beli' => 350000,
                'harga_jual' => 450000,
                'deskripsi' => 'Casing PC Office standard lengkap dengan Power Supply 450W bawaan.',
            ],
            [
                'kategori_id' => $catCase->id,
                'nama_produk' => 'Casing Gaming VenomRX + PSU 500W',
                'merk' => 'VenomRX',
                'stok' => 6,
                'harga_beli' => 650000,
                'harga_jual' => 800000,
                'deskripsi' => 'Casing PC Gaming tempered glass dengan RGB Fan dan PSU 500W 80 Plus.',
            ],

            // Wifi Adapter
            [
                'kategori_id' => $catWifi->id,
                'nama_produk' => 'USB Wifi Adapter TP-Link TL-WN725N',
                'merk' => 'TP-Link',
                'stok' => 25,
                'harga_beli' => 90000,
                'harga_jual' => 120000,
                'deskripsi' => 'Wireless USB Adapter Wifi Dongle 150Mbps untuk PC/Laptop.',
            ],

            // Keyboard Mouse
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'Logitech MK120 Keyboard Mouse Combo',
                'merk' => 'Logitech',
                'stok' => 18,
                'harga_beli' => 140000,
                'harga_jual' => 180000,
                'deskripsi' => 'Keyboard dan Mouse Combo kabel Logitech MK120 awet dan nyaman untuk kerja.',
            ],

            // VGA (Discrete GPU)
            [
                'kategori_id' => $catVga->id,
                'nama_produk' => 'Radeon RX 580 8GB Graphic Card',
                'merk' => 'AMD',
                'stok' => 5,
                'harga_beli' => 900000,
                'harga_jual' => 1100000,
                'deskripsi' => 'Kartu grafis discrete VGA RX 580 8GB DDR5 cocok untuk budget gaming.',
            ],
        ];

        foreach ($components as $component) {
            Produk::firstOrCreate(
                ['nama_produk' => $component['nama_produk']],
                $component
            );
        }
    }
}

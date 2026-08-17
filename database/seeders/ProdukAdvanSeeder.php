<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukAdvanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLaptop = Kategori::firstOrCreate(['nama_kategori' => 'LAPTOP']);

        $products = [
            [
                'nama_produk' => 'LAPTOP ADVAN SOULMATE X2 AMD 3020E RAM 8GB SSD 128GB LAYAR 14" WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ADVAN',
                'stok'        => 5,
                'harga_jual'  => 4400000,
                'harga_beli'  => 4150000, // Margin Rp 250.000
                'foto'        => 'produk/advan_soulmate.png',
                'deskripsi'   => 'Laptop ADVAN Soulmate X2 AMD 3020e, RAM 8GB, SSD 128GB, Layar 14 Inch, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP ADVAN SOULMATE X2 (3050) AMD 3050 RAM 8GB SSD 128GB LAYAR 14" WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ADVAN',
                'stok'        => 5,
                'harga_jual'  => 4600000,
                'harga_beli'  => 4350000, // Margin Rp 250.000
                'foto'        => 'produk/advan_soulmate.png',
                'deskripsi'   => 'Laptop ADVAN Soulmate X2 (3050) AMD 3050, RAM 8GB, SSD 128GB, Layar 14 Inch, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP ADVAN WORKMATE AMD RYZEN 5-3500U RAM 8GB SSD 256GB LAYAR 14" WUXGA WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ADVAN',
                'stok'        => 5,
                'harga_jual'  => 6075000,
                'harga_beli'  => 5725000, // Margin Rp 350.000
                'foto'        => 'produk/advan_workpro.png',
                'deskripsi'   => 'Laptop ADVAN Workmate AMD Ryzen 5-3500U, RAM 8GB, SSD 256GB, Layar 14 Inch WUXGA, Windows 11.',
            ],
            [
                'nama_produk' => 'LAPTOP ADVAN WORKPRO LITE INTEL CORE I5-1235 RAM 8GB SSD 256GB LAYAR 14" WIN 11',
                'kategori_id' => $kategoriLaptop->id,
                'merk'        => 'ADVAN',
                'stok'        => 5,
                'harga_jual'  => 6975000,
                'harga_beli'  => 6575000, // Margin Rp 400.000
                'foto'        => 'produk/advan_workpro.png',
                'deskripsi'   => 'Laptop ADVAN Workpro Lite Intel Core i5-1235, RAM 8GB, SSD 256GB, Layar 14 Inch, Windows 11.',
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

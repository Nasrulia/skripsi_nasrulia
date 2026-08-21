<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class UpdateExistingProductsShippingSeeder extends Seeder
{
    public function run(): void
    {
        $products = Produk::with('kategori')->get();

        foreach ($products as $p) {
            $catName = strtoupper($p->kategori->nama_kategori ?? '');
            $prodName = strtoupper($p->nama_produk);

            $berat = 1000;
            $packing = 'sedang';

            if (str_contains($catName, 'TINTA') || str_contains($prodName, 'TINTA')) {
                $berat = 200;
                $packing = 'kecil';
            } elseif (str_contains($catName, 'MOUSE') || str_contains($prodName, 'MOUSE') || str_contains($prodName, 'FLASHDISK') || str_contains($prodName, 'SSD') || str_contains($prodName, 'RAM')) {
                $berat = 300;
                $packing = 'kecil';
            } elseif (str_contains($catName, 'KEYBOARD') || str_contains($prodName, 'KEYBOARD') || str_contains($prodName, 'HEADSET') || str_contains($prodName, 'HEADPHONE')) {
                $berat = 800;
                $packing = 'sedang';
            } elseif (str_contains($catName, 'LAPTOP') || str_contains($prodName, 'LAPTOP') || str_contains($prodName, 'NOTEBOOK') || str_contains($prodName, 'ASUS') || str_contains($prodName, 'LENOVO') || str_contains($prodName, 'ACER') || str_contains($prodName, 'AXIOO') || str_contains($prodName, 'ADVAN')) {
                $berat = 2500;
                $packing = 'besar';
            } elseif (str_contains($catName, 'PRINTER') || str_contains($prodName, 'PRINTER') || str_contains($prodName, 'EPSON') || str_contains($prodName, 'CANON') || str_contains($prodName, 'BROTHER')) {
                $berat = 4500;
                $packing = 'besar';
            } elseif (str_contains($catName, 'RAKITAN') || str_contains($prodName, 'PC RAKITAN') || str_contains($prodName, 'FULL SET')) {
                $berat = 8000;
                $packing = 'ekstra_besar';
            }

            $p->update([
                'berat_gram' => $berat,
                'ukuran_packing' => $packing,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukMouseKeyboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catMouse    = Kategori::firstOrCreate(['nama_kategori' => 'MOUSE']);
        $catKeyboard = Kategori::firstOrCreate(['nama_kategori' => 'KEYBOARD']);
        $catKbMouse  = Kategori::firstOrCreate(['nama_kategori' => 'Keyboard Mouse']);

        $products = [
            // --- MOUSE ---
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE LOGITECH B100 OPTICAL (WIRED USB)',
                'merk'        => 'Logitech',
                'stok'        => 15,
                'harga_beli'  => 45000,
                'harga_jual'  => 65000,
                'foto'        => 'produk/logitech_b100.png',
                'deskripsi'   => 'Mouse optik kabel USB Logitech B100 hitam ergonomis, nyaman, dan presisi.',
            ],
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE LOGITECH M170 WIRELESS 2.4GHZ',
                'merk'        => 'Logitech',
                'stok'        => 12,
                'harga_beli'  => 110000,
                'harga_jual'  => 135000,
                'foto'        => 'produk/logitech_m170.png',
                'deskripsi'   => 'Mouse wireless 2.4GHz Logitech M170 koneksi kuat hingga 10 meter dan baterai tahan 12 bulan.',
            ],
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE LOGITECH M220 SILENT WIRELESS',
                'merk'        => 'Logitech',
                'stok'        => 10,
                'harga_beli'  => 150000,
                'harga_jual'  => 185000,
                'foto'        => 'produk/logitech_m220.png',
                'deskripsi'   => 'Mouse wireless silent click Logitech M220 mengurangi kebisingan klik hingga 90%.',
            ],
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE ROBOT M210 SILENT WIRELESS',
                'merk'        => 'ROBOT',
                'stok'        => 20,
                'harga_beli'  => 50000,
                'harga_jual'  => 75000,
                'foto'        => 'produk/robot_m210.png',
                'deskripsi'   => 'Mouse wireless Robot M210 2.4GHz silent click ergonomis dan hemat konsumsi daya.',
            ],
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE REXUS Q20 WIRELESS ERGONOMIC',
                'merk'        => 'Rexus',
                'stok'        => 15,
                'harga_beli'  => 60000,
                'harga_jual'  => 85000,
                'foto'        => 'produk/rexus_q20.png',
                'deskripsi'   => 'Mouse wireless Rexus Q20 dengan sensitivitas DPI tinggi dan kontur telapak tangan yang nyaman.',
            ],
            [
                'kategori_id' => $catMouse->id,
                'nama_produk' => 'MOUSE GAMING FANTECH VX7 CRYPTO RGB',
                'merk'        => 'Fantech',
                'stok'        => 10,
                'harga_beli'  => 85000,
                'harga_jual'  => 115000,
                'foto'        => 'produk/fantech_vx7.png',
                'deskripsi'   => 'Mouse gaming RGB Fantech VX7 Crypto 8000 DPI 6 tombol macro programmable.',
            ],

            // --- KEYBOARD ---
            [
                'kategori_id' => $catKeyboard->id,
                'nama_produk' => 'KEYBOARD LOGITECH K120 ERGONOMIC (WIRED USB)',
                'merk'        => 'Logitech',
                'stok'        => 15,
                'harga_beli'  => 85000,
                'harga_jual'  => 115000,
                'foto'        => 'produk/logitech_k120.png',
                'deskripsi'   => 'Keyboard kabel USB Logitech K120 tahan cipratan air dengan tombol empuk dan ramah pengguna.',
            ],
            [
                'kategori_id' => $catKeyboard->id,
                'nama_produk' => 'KEYBOARD LOGITECH K380 MULTI-DEVICE BLUETOOTH',
                'merk'        => 'Logitech',
                'stok'        => 8,
                'harga_beli'  => 380000,
                'harga_jual'  => 435000,
                'foto'        => 'produk/logitech_k380.png',
                'deskripsi'   => 'Keyboard wireless Bluetooth Logitech K380 dapat terhubung ke 3 perangkat sekaligus (PC, Tablet, HP).',
            ],
            [
                'kategori_id' => $catKeyboard->id,
                'nama_produk' => 'KEYBOARD GAMING REXUS BATTLEFIRE K9T RGB',
                'merk'        => 'Rexus',
                'stok'        => 10,
                'harga_beli'  => 135000,
                'harga_jual'  => 175000,
                'foto'        => 'produk/rexus_k9t.png',
                'deskripsi'   => 'Keyboard gaming Rexus Battlefire K9T RGB backlit dengan 19 tombol anti-ghosting.',
            ],
            [
                'kategori_id' => $catKeyboard->id,
                'nama_produk' => 'KEYBOARD GAMING FANTECH K613 FIGHTER TKL RGB',
                'merk'        => 'Fantech',
                'stok'        => 10,
                'harga_beli'  => 150000,
                'harga_jual'  => 195000,
                'foto'        => 'produk/fantech_k613.png',
                'deskripsi'   => 'Keyboard gaming Fantech K613 Fighter TKL bodi aluminium solid dengan lampu RGB.',
            ],
            [
                'kategori_id' => $catKeyboard->id,
                'nama_produk' => 'KEYBOARD ROBOT KB100 STANDARD USB',
                'merk'        => 'ROBOT',
                'stok'        => 20,
                'harga_beli'  => 55000,
                'harga_jual'  => 79000,
                'foto'        => 'produk/robot_kb100.png',
                'deskripsi'   => 'Keyboard USB standar Robot KB100 awet dan sangat cocok untuk penggunaan perkantoran.',
            ],

            // --- KEYBOARD & MOUSE COMBO (SEPAKET) ---
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'KEYBOARD MOUSE COMBO LOGITECH MK120 (WIRED USB)',
                'merk'        => 'Logitech',
                'stok'        => 18,
                'harga_beli'  => 140000,
                'harga_jual'  => 180000,
                'foto'        => 'produk/logitech_mk120.png',
                'deskripsi'   => 'Paket bundling keyboard dan mouse kabel Logitech MK120 awet dan nyaman untuk kerja.',
            ],
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'KEYBOARD MOUSE COMBO LOGITECH MK240 NANO WIRELESS',
                'merk'        => 'Logitech',
                'stok'        => 10,
                'harga_beli'  => 290000,
                'harga_jual'  => 345000,
                'foto'        => 'produk/logitech_mk240.png',
                'deskripsi'   => 'Paket keyboard dan mouse wireless ringkas Logitech MK240 Nano desain warna-warni modern.',
            ],
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'KEYBOARD MOUSE COMBO LOGITECH MK270 WIRELESS',
                'merk'        => 'Logitech',
                'stok'        => 12,
                'harga_beli'  => 320000,
                'harga_jual'  => 375000,
                'foto'        => 'produk/logitech_mk270.png',
                'deskripsi'   => 'Combo keyboard full-size dan mouse wireless 2.4GHz Logitech MK270 koneksi stabil tanpa lag.',
            ],
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'KEYBOARD MOUSE COMBO ROBOT KM3000 WIRELESS',
                'merk'        => 'ROBOT',
                'stok'        => 15,
                'harga_beli'  => 120000,
                'harga_jual'  => 165000,
                'foto'        => 'produk/robot_km3000.png',
                'deskripsi'   => 'Paket set wireless keyboard dan mouse Robot KM3000 2.4G hemat energi dan desain minimalis.',
            ],
            [
                'kategori_id' => $catKbMouse->id,
                'nama_produk' => 'KEYBOARD MOUSE COMBO GAMING REXUS WARFACTION VR2',
                'merk'        => 'Rexus',
                'stok'        => 10,
                'harga_beli'  => 185000,
                'harga_jual'  => 235000,
                'foto'        => 'produk/rexus_vr2.png',
                'deskripsi'   => 'Combo keyboard gaming RGB dan mouse gaming 2400 DPI Rexus Warfaction VR2 tangguh.',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AturanChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('aturan_chatbot')->insert([
            [
                'kata_kunci' => 'lcd, elcedi, layar, screen',
                'jawaban' => 'Layar laptop Anda bergaris, blank, atau pecah? Tenang, kami menyediakan layanan penggantian LCD (sudah termasuk part dan pemasangan) oleh teknisi profesional. Silakan cek layanan berikut:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'install, instal, instal ulang, windows',
                'jawaban' => 'Laptop terasa lambat, kena virus, atau mau upgrade Windows? Layanan instal ulang kami sudah lengkap dengan driver dan aplikasi standar lho. Cek estimasi biayanya di sini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'laptop, leptop, netbook, notebook',
                'jawaban' => 'Sedang mencari laptop baru untuk kebutuhan kuliah, kerja kantoran, atau gaming? Kami punya rekomendasi laptop terbaik dengan harga bersaing. Silakan cek produk di bawah ini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'lemot, lambat, lag, lelet',
                'jawaban' => 'Laptop atau PC Anda lemot? Solusi terbaiknya adalah dengan upgrade penyimpanan ke SSD, menambah kapasitas RAM, atau melakukan cleaning sistem. Berikut rekomendasi produk dan jasanya:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'printer, print, cetak',
                'jawaban' => 'Butuh printer irit untuk cetak tugas atau skripsi? Kami sangat merekomendasikan printer All-in-One (Print, Scan, Copy) yang sangat efisien dan awet tinta. Lihat detail produknya di sini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'keyboard, kibot, tombol',
                'jawaban' => 'Tombol keyboard error, mengetik sendiri, atau kamu lagi cari keyboard mechanical yang empuk buat ngetik? Kami punya sparepart dan aksesorisnya. Cek rekomendasi berikut:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'mati total, matot, mati, gak nyala',
                'jawaban' => 'Perangkat mati total atau tidak bisa menyala sama sekali? Kami melayani pengecekan dan diagnosa awal untuk mengetahui kerusakan pastinya pada motherboard atau komponen lain. Silakan bawa unitnya ke toko!',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'baterai, batre, batrei, batry',
                'jawaban' => 'Baterai bocor, cepat drop, atau laptop harus selalu dicolok charger? Segera ganti baterai Anda sebelum merusak komponen power. Cek ketersediaan produk dan jasanya di bawah ini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'panas, overheat, termal, kipas',
                'jawaban' => 'Laptop kamu cepat panas atau sering mati sendiri (overheat)? Itu tandanya butuh pembersihan debu di kipas dan penggantian thermal paste agar sirkulasi udara lancar kembali. Cek layanannya:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'jaringan, wifi, internet, router',
                'jawaban' => 'Ada masalah dengan koneksi internet, wifi putus-nyambung, atau butuh router tambahan untuk memperluas jangkauan sinyal? Kami punya solusi perangkat networking terbaik untuk Anda:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'data, file, berkas, recovery',
                'jawaban' => 'Kehilangan file penting atau butuh media penyimpanan eksternal untuk backup? Kami menyediakan flashdisk original dan juga layanan recovery data dari harddisk/flashdisk yang bermasalah. Cek infonya:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'engsel, casing, engsel patah',
                'jawaban' => 'Layar laptop susah ditutup karena engsel patah atau casing mangap? Jangan dipaksa karena bisa merusak kabel LCD! Segera bawa ke kami untuk perbaikan dudukan engsel yang kokoh kembali:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'monitor, layar monitor, led',
                'jawaban' => 'Ingin tampilan layar yang lebih luas untuk kerja atau main game? Kami menyediakan berbagai pilihan monitor LED/IPS dengan resolusi tajam dan warna akurat. Intip rekomendasinya di sini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'desain, editing, autocad, photoshop',
                'jawaban' => 'Membutuhkan perangkat atau software untuk kebutuhan desain grafis, arsitektur, atau editing video? Kami bisa bantu instalkan aplikasinya, atau merekomendasikan monitor dengan warna yang pas:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'gaming, game, rog',
                'jawaban' => 'Lagi cari perlengkapan tempur buat gaming? Dari laptop spek dewa, monitor refresh rate tinggi, sampai keyboard mechanical RGB ada di sini. Sikat rekomendasi gear gaming ini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'skripsi, kuliah, mahasiswa',
                'jawaban' => 'Lagi kejar target skripsi? Biar ngetik makin lancar, pastikan laptop kamu responsif, data ter-backup aman, dan punya printer sendiri biar nggak bolak-balik warnet. Cek gear tempur mahasiswa ini:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'lokasi, alamat, peta, map, jalan',
                'jawaban' => 'Toko Nusantara Jaya Computer berlokasi strategis di Banjarmasin. Silakan mampir langsung ke toko kami untuk melihat produk atau konsultasi servis langsung dengan teknisi kami ya!',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'buka, jam buka, hari buka, operasional, jadwal',
                'jawaban' => 'Kami siap melayani Anda setiap hari Senin hingga Sabtu, mulai pukul 09.00 sampai 17.00 WITA. Untuk hari Minggu kami libur ya. Ditunggu kedatangannya!',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'garansi, klaim garansi',
                'jawaban' => 'Tentu saja! Setiap pembelian barang baru maupun jasa servis di Nusantara Jaya Computer selalu dilengkapi dengan garansi toko. Pastikan nota transaksi Anda tidak hilang ya untuk proses klaim.',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'biaya, harga, tarif, ongkos',
                'jawaban' => 'Untuk rincian estimasi biaya servis atau harga komponen, NJK Assistant akan mencoba mencarikan yang paling sesuai dengan keluhan Anda. Berikut beberapa layanan yang mungkin terkait:',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'berapa lama, lama proses, lama pengerjaan, durasi, berapa hari, berapa jam',
                'jawaban' => 'Apabila barang ready di supplier Banjarmasin prosesnya kurang lebih sekitar 4-5 jam, tetapi apabila ada komponen yang indent prosesnya kurang lebih 7-10 hari (tergantung pengiriman expedisi).',
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
        ]);
    }
}
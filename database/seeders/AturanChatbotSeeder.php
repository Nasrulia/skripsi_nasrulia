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
                'kata_kunci' => 'keyboard, kibot, tombol, mouse, tetikus, mos, sepaket keyboard mouse',
                'jawaban' => 'Lagi cari keyboard, mouse ergonomis/silent, atau paket combo keyboard & mouse? Kami menyediakan berbagai aksesoris berkualitas dari Logitech, Robot, Rexus, dan Fantech. Cek rekomendasi berikut:',
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
                'jawaban' => "Membutuhkan laptop atau perangkat untuk kebutuhan desain grafis, arsitektur, atau editing video?\n\n💡 **Rekomendasi Spesifikasi:**\n- **Processor:** Sangat disarankan menggunakan laptop dengan processor minimal **AMD Ryzen 3** atau **Intel Core i3** ke atas (seperti Ryzen 3/5/7 atau Core i3/i5/i7).\n- **Catatan:** Processor di bawah Core i3 (seperti Celeron/Pentium/N4020) **kurang disarankan** untuk kebutuhan editing atau desain grafis yang berat.\n- **RAM & Storage:** Minimal RAM 8GB dan SSD 512GB agar pengerjaan desain/render lancar.\n\nKami juga menyediakan layanan instalasi software dan berbagai pilihan laptop/PC yang cocok untuk kebutuhan Anda. Berikut beberapa rekomendasi produk dan layanan terkait:",
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
                'kata_kunci' => 'lokasi, alamat, peta, map, maps, toko offline, offline, google maps, gmaps, cabang, letak toko, tempat',
                'jawaban' => "Anda dapat mengunjungi toko offline kami **Nusantara Jaya Computer** di lokasi berikut:\n\n📍 **Google Maps Toko Offline:**\nhttps://share.google/xrwq12yHe0uMzcoFv\n\n📱 **Nomor WhatsApp Toko:**\n- 0851-8239-2525\n- 0851-8239-2526\n\nSilakan mampir langsung ke toko kami atau hubungi kami melalui WhatsApp jika ada pertanyaan lebih lanjut!",
                'created_at' => '2026-03-19 16:20:12',
                'updated_at' => '2026-03-19 16:20:12',
            ],
            [
                'kata_kunci' => 'whatsapp, wa, kontak, nomor wa, no wa, nomor whatsapp, hubungi, contact, telepon, no hp',
                'jawaban' => "Anda dapat menghubungi toko offline kami **Nusantara Jaya Computer** melalui nomor WhatsApp berikut:\n\n📱 **Nomor WhatsApp Toko:**\n- 0851-8239-2525\n- 0851-8239-2526\n\n📍 **Lokasi Toko Offline (Google Maps):**\nhttps://share.google/xrwq12yHe0uMzcoFv\n\nKami siap membantu Anda!",
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
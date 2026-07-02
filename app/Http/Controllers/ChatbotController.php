<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AturanChatbot;
use App\Models\Produk;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function getResponse(Request $request)
    {
        $pesan_user = strtolower(trim($request->pesan));
        $kata_input = explode(' ', $pesan_user);

        // --- 0. CEK SESSION FLOW ---
        $flow = session('chatbot_flow');

        // --- 0.0 CEK SESSION FLOW UNTUK RAKIT PC ---
        if ($flow && str_starts_with($flow, 'rakit_pc_')) {
            if ($pesan_user === 'batal' || $pesan_user === 'cancel' || $pesan_user === 'keluar') {
                session()->forget([
                    'chatbot_flow',
                    'rakit_pc_budget',
                    'rakit_pc_brand',
                    'rakit_pc_purpose',
                ]);
                $jawaban_bot = "Proses konsultasi PC rakitan telah dibatalkan. Ada hal lain yang bisa saya bantu?";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }

            if ($flow === 'rakit_pc_waiting_budget') {
                $budget = $this->parseBudget($request->pesan);
                if ($budget <= 0) {
                    $jawaban_bot = "Format budget tidak valid. Silakan masukkan angka nominal budget Anda (contoh: **5000000** atau **5 juta**), atau ketik **batal** untuk keluar.";
                    return response()->json([
                        'jawaban' => $jawaban_bot,
                        'rekomendasi_produk' => collect(),
                        'rekomendasi_jasa' => collect(),
                    ]);
                }
                
                if ($budget < 3000000) {
                    $jawaban_bot = "Mohon maaf, budget minimal yang disarankan untuk merakit CPU standar adalah **Rp 3.000.000** agar mendapatkan komponen yang memadai dan bergaransi.\n\nSilakan masukkan budget baru (minimal **3.000.000**) atau ketik **batal** untuk keluar.";
                    return response()->json([
                        'jawaban' => $jawaban_bot,
                        'rekomendasi_produk' => collect(),
                        'rekomendasi_jasa' => collect(),
                    ]);
                }

                session([
                    'rakit_pc_budget' => $budget,
                    'chatbot_flow' => 'rakit_pc_waiting_brand'
                ]);

                $budget_formatted = number_format($budget, 0, ',', '.');
                $jawaban_bot = "Budget Anda sebesar **Rp {$budget_formatted}** telah dicatat. 👍\n\nSelanjutnya, apakah Anda memiliki preferensi merk Processor?\n1. **Intel**\n2. **AMD**\n3. **Bebas** (Kami pilihkan yang terbaik)\n\nSilakan ketik pilihan Anda (Intel / AMD / Bebas).";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }

            if ($flow === 'rakit_pc_waiting_brand') {
                $brand = 'Bebas';
                if (str_contains($pesan_user, 'intel') || $pesan_user === '1') {
                    $brand = 'Intel';
                } elseif (str_contains($pesan_user, 'amd') || str_contains($pesan_user, 'ryzen') || $pesan_user === '2') {
                    $brand = 'AMD';
                } elseif (str_contains($pesan_user, 'bebas') || str_contains($pesan_user, 'tidak') || $pesan_user === '3') {
                    $brand = 'Bebas';
                } else {
                    $jawaban_bot = "Pilihan tidak valid. Silakan ketik **Intel**, **AMD**, atau **Bebas**.";
                    return response()->json([
                        'jawaban' => $jawaban_bot,
                        'rekomendasi_produk' => collect(),
                        'rekomendasi_jasa' => collect(),
                    ]);
                }

                session([
                    'rakit_pc_brand' => $brand,
                    'chatbot_flow' => 'rakit_pc_waiting_purpose'
                ]);

                $jawaban_bot = "Pilihan merk: **{$brand}**.\n\nUntuk kebutuhan utama apa PC rakitan ini digunakan?\n1. **Office / Kerja Standar** (Admin toko, belajar, pengetikan, browsing)\n2. **Gaming** (Bermain game online/offline)\n3. **Editing / Rendering** (Desain grafis, coding, edit video)\n\nSilakan ketik pilihan Anda (Office / Gaming / Editing).";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }

            if ($flow === 'rakit_pc_waiting_purpose') {
                $purpose = '';
                if (str_contains($pesan_user, 'office') || str_contains($pesan_user, 'kerja') || str_contains($pesan_user, 'tulis') || str_contains($pesan_user, 'admin') || $pesan_user === '1') {
                    $purpose = 'Office';
                } elseif (str_contains($pesan_user, 'game') || str_contains($pesan_user, 'gaming') || $pesan_user === '2') {
                    $purpose = 'Gaming';
                } elseif (str_contains($pesan_user, 'edit') || str_contains($pesan_user, 'render') || str_contains($pesan_user, 'desain') || str_contains($pesan_user, 'coding') || str_contains($pesan_user, 'program') || $pesan_user === '3') {
                    $purpose = 'Editing';
                } else {
                    $jawaban_bot = "Pilihan tidak valid. Silakan ketik **Office**, **Gaming**, atau **Editing**.";
                    return response()->json([
                        'jawaban' => $jawaban_bot,
                        'rekomendasi_produk' => collect(),
                        'rekomendasi_jasa' => collect(),
                    ]);
                }

                session([
                    'rakit_pc_purpose' => $purpose,
                    'chatbot_flow' => 'rakit_pc_waiting_optional'
                ]);

                $jawaban_bot = "Kebutuhan utama: **{$purpose}**.\n\nApakah Anda memerlukan aksesoris/perangkat tambahan berikut?\n- **Monitor**\n- **Keyboard & Mouse**\n- **Wifi Adapter**\n\nSilakan ketik komponen tambahan yang Anda inginkan (contoh: 'monitor dan wifi', atau ketik 'tidak' jika hanya unit CPU saja).";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }

            if ($flow === 'rakit_pc_waiting_optional') {
                $include_monitor = false;
                $include_kb_mouse = false;
                $include_wifi = false;

                if (!str_contains($pesan_user, 'tidak') && !str_contains($pesan_user, 'no') && !str_contains($pesan_user, 'hanya cpu')) {
                    if (str_contains($pesan_user, 'monitor') || str_contains($pesan_user, 'layar') || str_contains($pesan_user, 'led') || str_contains($pesan_user, 'semua')) {
                        $include_monitor = true;
                    }
                    if (str_contains($pesan_user, 'keyboard') || str_contains($pesan_user, 'mouse') || str_contains($pesan_user, 'kb') || str_contains($pesan_user, 'semua')) {
                        $include_kb_mouse = true;
                    }
                    if (str_contains($pesan_user, 'wifi') || str_contains($pesan_user, 'adapter') || str_contains($pesan_user, 'dongle') || str_contains($pesan_user, 'semua')) {
                        $include_wifi = true;
                    }
                }

                $budget = session('rakit_pc_budget');
                $brand = session('rakit_pc_brand');
                $purpose = session('rakit_pc_purpose');

                $build_result = $this->generatePCBuild($budget, $brand, $purpose, $include_monitor, $include_kb_mouse, $include_wifi);

                session()->forget([
                    'chatbot_flow',
                    'rakit_pc_budget',
                    'rakit_pc_brand',
                    'rakit_pc_purpose',
                ]);

                try {
                    ChatbotLog::create([
                        'user_id' => Auth::id(),
                        'pesan' => "Rakit PC selesai: Budget {$budget}, Brand {$brand}, Purpose {$purpose}",
                        'jawaban' => $build_result['jawaban'],
                        'kategori' => 'rakit_pc',
                    ]);
                } catch (\Exception $e) {
                    // Abaikan
                }

                return response()->json([
                    'jawaban' => $build_result['jawaban'],
                    'rekomendasi_produk' => $build_result['rekomendasi_produk'],
                    'rekomendasi_jasa' => collect(),
                ]);
            }
        }

        // --- 0. CEK SESSION FLOW UNTUK KOMPLAIN ---
        $flow = session('chatbot_flow');
        
        if ($flow === 'complaint_waiting_trx') {
            if ($pesan_user === 'batal' || $pesan_user === 'cancel' || $pesan_user === 'keluar') {
                session()->forget(['chatbot_flow', 'complaint_trx_id', 'complaint_trx_code', 'complaint_trx_type']);
                $jawaban_bot = "Proses komplain telah dibatalkan. Ada hal lain yang bisa saya bantu?";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
            
            $trxCode = strtoupper(trim($request->pesan));
            $transaction = \App\Models\Transaksi::with('user')->where('kode_transaksi', $trxCode)->first();
            
            if (!$transaction) {
                $jawaban_bot = "Maaf, nomor transaksi **{$trxCode}** tidak ditemukan di sistem kami.\n\nSilakan masukkan kembali nomor transaksi yang valid (contoh: **TRX-XXXXXXXXXX**), atau ketik **batal** untuk membatalkan.";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
            
            session([
                'chatbot_flow' => 'complaint_waiting_desc',
                'complaint_trx_id' => $transaction->id,
                'complaint_trx_code' => $transaction->kode_transaksi,
                'complaint_trx_type' => $transaction->tipe,
            ]);
            
            $tipeTrx = $transaction->tipe === 'penjualan' ? 'Penjualan Barang' : 'Layanan Servis';
            $jawaban_bot = "Nomor transaksi **{$transaction->kode_transaksi}** ({$tipeTrx}) ditemukan atas nama **{$transaction->nama_pelanggan}**.\n\nSilakan ketikkan **rincian detail komplain/keluhan** yang Anda alami secara lengkap.";
            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }
        
        if ($flow === 'complaint_waiting_desc') {
            if ($pesan_user === 'batal' || $pesan_user === 'cancel' || $pesan_user === 'keluar') {
                session()->forget(['chatbot_flow', 'complaint_trx_id', 'complaint_trx_code', 'complaint_trx_type']);
                $jawaban_bot = "Proses komplain telah dibatalkan. Ada hal lain yang bisa saya bantu?";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
            
            $desc = $request->pesan;
            $trxId = session('complaint_trx_id');
            $trxCode = session('complaint_trx_code');
            $trxType = session('complaint_trx_type');
            
            $transaction = \App\Models\Transaksi::with('user')->find($trxId);
            $customerId = Auth::id() ?: ($transaction ? $transaction->user_id : null);
            $customerName = Auth::check() ? Auth::user()->name : ($transaction ? $transaction->nama_pelanggan : 'Guest');
            
            // Dapatkan nomor whatsapp pelanggan
            $customerPhone = '';
            if (Auth::check() && !empty(Auth::user()->no_whatsapp)) {
                $customerPhone = Auth::user()->no_whatsapp;
            } elseif ($transaction && $transaction->user && !empty($transaction->user->no_whatsapp)) {
                $customerPhone = $transaction->user->no_whatsapp;
            } else {
                $customerPhone = '-';
            }
            
            // Simpan komplain ke database
            $komplain = \App\Models\Komplain::create([
                'transaksi_id' => $trxId,
                'kode_transaksi' => $trxCode,
                'user_id' => $customerId,
                'nama_pelanggan' => $customerName,
                'no_whatsapp' => $customerPhone,
                'deskripsi' => $desc,
                'tipe' => $trxType,
                'status' => 'pending',
            ]);
            
            // Cari staf penerima notifikasi
            $recipients = collect();
            if ($trxType === 'penjualan') {
                $recipients = \App\Models\User::whereIn('peran', ['admin', 'kasir'])
                    ->whereNotNull('no_whatsapp')
                    ->where('no_whatsapp', '!=', '')
                    ->get();
            } else {
                $recipients = \App\Models\User::whereIn('peran', ['admin', 'teknisi'])
                    ->whereNotNull('no_whatsapp')
                    ->where('no_whatsapp', '!=', '')
                    ->get();
            }
            
            // Kirim notifikasi via WhatsAppService
            $waService = new \App\Services\WhatsAppService();
            $waMessage = "*NUSANTARA JAYA COMPUTER - LAPORAN KOMPLAIN BARU*\n";
            $waMessage .= "---------------------------------------------\n";
            $waMessage .= "Pelanggan baru saja mengajukan komplain:\n\n";
            $waMessage .= "📋 Kode Transaksi: *$trxCode*\n";
            $waMessage .= "👤 Nama Pelanggan: *$customerName*\n";
            $waMessage .= "📞 No. WhatsApp: *{$customerPhone}*\n";
            $waMessage .= "⚡ Kategori: *" . ($trxType === 'penjualan' ? 'Penjualan Barang' : 'Servis') . "*\n";
            $waMessage .= "📝 Deskripsi Keluhan:\n\"$desc\"\n\n";
            $waMessage .= "Silakan login ke dashboard sistem untuk merespons dan menyelesaikan kendala ini via WhatsApp pelanggan.\n";
            $waMessage .= "---------------------------------------------";
            
            foreach ($recipients as $recipient) {
                $waService->send($recipient->no_whatsapp, $waMessage);
            }
            
            // Bersihkan session komplain
            session()->forget(['chatbot_flow', 'complaint_trx_id', 'complaint_trx_code', 'complaint_trx_type']);
            
            // Simpan ChatbotLog dengan kategori komplain agar tercatat di Laporan PDF
            try {
                ChatbotLog::create([
                    'user_id' => Auth::id(),
                    'pesan' => "Komplain transaksi {$trxCode}: {$desc}",
                    'jawaban' => "Terima kasih, laporan komplain Anda telah berhasil kami catat dan diteruskan ke tim terkait.",
                    'kategori' => 'komplain',
                ]);
            } catch (\Exception $e) {
                // Abaikan
            }
            
            $tipeStaf = $trxType === 'penjualan' ? 'Admin/Kasir' : 'Admin/Teknisi';
            $jawaban_bot = "Terima kasih. Laporan komplain Anda telah resmi tercatat di sistem kami dengan Kode Transaksi **{$trxCode}**.\n\nTim kami ({$tipeStaf}) telah dinotifikasi dan akan segera menghubungi Anda melalui nomor WhatsApp Anda (**{$customerPhone}**) untuk membantu penyelesaian kendala ini. Mohon ditunggu.";
            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }
        
        // --- 0.2 CEK APAKAH INPUT BARU MEMICU ALUR RAKIT PC ---
        $keywords_rakit = ['rakit', 'rakitan', 'merakit', 'build pc', 'build komputer', 'rekomendasi pc', 'spesifikasi pc', 'cpu rakitan'];
        $is_rakit = false;
        foreach ($keywords_rakit as $kr) {
            if (str_contains($pesan_user, $kr)) {
                $is_rakit = true;
                break;
            }
        }
        
        if ($is_rakit) {
            session(['chatbot_flow' => 'rakit_pc_waiting_budget']);
            $jawaban_bot = "Halo! Saya siap membantu Anda menghitung spesifikasi dan merekomendasikan CPU rakitan terbaik yang kompatibel.\n\nSilakan masukkan **budget maksimal** yang Anda miliki (contoh: **5000000** atau **5 juta**):";
            
            try {
                ChatbotLog::create([
                    'user_id' => Auth::id(),
                    'pesan' => $request->pesan,
                    'jawaban' => $jawaban_bot,
                    'kategori' => 'rakit_pc',
                ]);
            } catch (\Exception $e) {
                // Abaikan
            }
            
            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }

        // --- 0.1 CEK APAKAH INPUT BARU MEMICU ALUR KOMPLAIN ---
        $keywords_komplain = ['komplain', 'kecewa', 'rusak', 'salah', 'cacat', 'retur', 'pecah', 'komplen', 'complain', 'masalah', 'kendala'];
        $is_komplain = false;
        foreach ($keywords_komplain as $kk) {
            if (str_contains($pesan_user, $kk)) {
                $is_komplain = true;
                break;
            }
        }
        
        if ($is_komplain) {
            session(['chatbot_flow' => 'complaint_waiting_trx']);
            $jawaban_bot = "Saya prihatin mendengar kendala Anda. Mohon maaf atas ketidaknyamanan ini.\n\nUntuk memproses laporan komplain Anda, mohon masukkan **nomor transaksi** Anda (contoh: **TRX-XXXXXXXXXX** atau kode transaksi penjualan/servis Anda).";
            
            try {
                ChatbotLog::create([
                    'user_id' => Auth::id(),
                    'pesan' => $request->pesan,
                    'jawaban' => $jawaban_bot,
                    'kategori' => 'komplain',
                ]);
            } catch (\Exception $e) {
                // Abaikan
            }
            
            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }

        // --- Cek apakah user adalah guest dan berniat membeli/transaksi ---
        if (!Auth::check()) {
            $kata_kunci_beli = ['beli', 'pesan', 'order', 'checkout', 'bayar', 'keranjang', 'transaksi', 'ambil', 'pembelian', 'pemesanan', 'payment', 'purchase'];
            $ingin_beli = false;
            foreach ($kata_kunci_beli as $kk) {
                if (str_contains($pesan_user, $kk)) {
                    $ingin_beli = true;
                    break;
                }
            }

            if ($ingin_beli) {
                $jawaban_bot = "Untuk melakukan transaksi pembelian barang secara aman dan terverifikasi, silakan buat akun atau masuk terlebih dahulu demi keamanan transaksi Anda.\n\nSilakan **[Daftar Akun Baru](" . route('register') . ")** atau **[Masuk ke Akun Anda](" . route('login') . ")** untuk melanjutkan pemesanan.";
                
                try {
                    ChatbotLog::create([
                        'user_id' => null,
                        'pesan' => $request->pesan,
                        'jawaban' => $jawaban_bot,
                        'kategori' => 'transaksi_guest',
                    ]);
                } catch (\Exception $e) {
                    // Abaikan
                }

                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
        }

        $jawaban_bot = "Maaf, NJK Assistant belum mengerti maksud Anda. Silakan ketik kata kunci lain seperti 'laptop', 'lcd', atau 'install'. Anda juga bisa langsung ke menu Katalog kami.";

        $rekomendasi_produk = collect();
        $rekomendasi_jasa = collect();

        $aturan_ditemukan = false;
        $kata_kunci_cocok = '';
        $kategori_pertanyaan = null;

        // --- 1. DETEKSI PRODUK SPESIFIK SECARA DINAMIS ---
        $stopWords = [
            'dan', 'yang', 'dengan', 'untuk', 'dari', 'atau', 'di', 'ke', 'ini', 'itu', 
            'ready', 'stok', 'ada', 'apakah', 'saya', 'mau', 'cari', 'beli', 'jual', 
            'bisa', 'tolong', 'toko', 'punya', 'apakah', 'adakah', 'bagaimana', 'bagaimanakah', 
            'apa', 'permisi', 'halo', 'hallo', 'kak', 'selamat', 'pagi', 'siang', 
            'sore', 'malam', 'tanya', 'bertanya', 'gan', 'sis', 'boss', 'bos', 'min',
            'admin', 'apakah', 'adakah', 'apakah ada', 'apakah ready', 'apakah jual', 
            'menjual', 'menyediakan', 'adakah', 'kah', 'saja', 'tersebut', 'tentang', 
            'mengenai', 'yaitu', 'yakni', 'adalah', 'merupakan', 'seperti', 
            'contoh', 'contohnya', 'misalkan', 'misalnya'
        ];

        $userKeywords = $this->extractKeywords($pesan_user, $stopWords);
        $matchedProducts = collect();
        $allProducts = Produk::with('kategori')->get();

        $isQueryForTinta = false;
        $tintaKeywords = ['tinta', 'ink', 'catridge', 'cartridge', 'isi ulang'];
        foreach ($tintaKeywords as $tk) {
            if (str_contains($pesan_user, $tk)) {
                $isQueryForTinta = true;
                break;
            }
        }

        $isQueryForPrinter = str_contains($pesan_user, 'printer');
        $scoredProducts = collect();

        if (count($userKeywords) > 0) {
            foreach ($allProducts as $product) {
                // Ekstraksi kata kunci produk
                $productKeywords = [];
                if ($product->merk) {
                    $productKeywords = array_merge($productKeywords, $this->extractKeywords($product->merk, $stopWords));
                }
                if ($product->kategori && $product->kategori->nama_kategori) {
                    $productKeywords = array_merge($productKeywords, $this->extractKeywords($product->kategori->nama_kategori, $stopWords));
                }
                $productKeywords = array_merge($productKeywords, $this->extractKeywords($product->nama_produk, $stopWords));
                $productKeywords = array_unique($productKeywords);

                $matchedCount = 0;
                foreach ($userKeywords as $uk) {
                    $matched = false;
                    foreach ($productKeywords as $pk) {
                        if ($uk === $pk || $this->isWordFuzzyMatch($uk, $pk) || (strlen($uk) >= 3 && str_contains($pk, $uk)) || (strlen($pk) >= 3 && str_contains($uk, $pk))) {
                            $matched = true;
                            break;
                        }
                    }
                    if ($matched) {
                        $matchedCount++;
                    }
                }

                if ($matchedCount > 0) {
                    $matchRatio = $matchedCount / count($userKeywords);
                    $score = $matchRatio * 100;

                    // Logika Penalti & Boost Kategori
                    $kategoriName = strtolower($product->kategori->nama_kategori ?? '');
                    $namaProdukLower = strtolower($product->nama_produk);
                    
                    $isTintaProduct = str_contains($kategoriName, 'tinta') || str_contains($namaProdukLower, 'tinta') || str_contains($namaProdukLower, 'ink') || str_contains($namaProdukLower, 'cartridge') || str_contains($namaProdukLower, 'catridge');
                    $isPrinterProduct = str_contains($kategoriName, 'printer') || str_contains($namaProdukLower, 'printer');

                    if ($isQueryForTinta) {
                        if ($isTintaProduct) {
                            $score += 50; // Boost tinta
                        } elseif ($isPrinterProduct) {
                            $score -= 40; // Penalti printer
                        }
                    } elseif ($isQueryForPrinter) {
                        if ($isPrinterProduct) {
                            $score += 50; // Boost printer
                        } elseif ($isTintaProduct) {
                            $score -= 40; // Penalti tinta
                        }
                    }

                    $scoredProducts->push([
                        'product' => $product,
                        'score' => $score,
                        'matched_count' => $matchedCount,
                    ]);
                }
            }
        }

        $scoredProducts = $scoredProducts->sortByDesc('score');
        $topMatch = $scoredProducts->first();

        if ($topMatch && $topMatch['score'] >= 30) {
            $topScore = $topMatch['score'];
            // Ambil semua produk yang skornya mendekati skor tertinggi (maks selisih 20 poin)
            $matchedProducts = $scoredProducts->filter(function($sp) use ($topScore) {
                return $sp['score'] >= ($topScore - 20) && $sp['score'] >= 30;
            })->map(function($sp) {
                return $sp['product'];
            })->take(5)->values();
        }

        if ($matchedProducts->isNotEmpty()) {
            $product = $matchedProducts->first();

            // Jika hanya ada 1 produk yang sangat cocok
            if ($matchedProducts->count() === 1) {
                $stok = $product->stok;
                $harga = number_format($product->harga_jual, 0, ',', '.');
                
                if ($stok > 0) {
                    $statusStok = "Ready Stock";
                    $jawaban_bot = "Mengenai produk **{$product->nama_produk}**, barang tersebut saat ini {$statusStok} dengan harga **Rp {$harga}**.\n\n";
                    if ($product->deskripsi) {
                        $jawaban_bot .= "*Deskripsi Produk:* \n{$product->deskripsi}\n\n";
                    }
                } else {
                    $jawaban_bot = "Mengenai produk **{$product->nama_produk}**, saat ini stok barang tersebut **Habis dan akan segera direstock**.\n\n";
                    if ($product->deskripsi) {
                        $jawaban_bot .= "*Deskripsi Produk:* \n{$product->deskripsi}\n\n";
                    }
                    
                    if (Auth::check()) {
                        $user = Auth::user();
                        if ($user->aktifkan_notifikasi) {
                            $noWa = $user->no_whatsapp ?? 'Belum ada nomor WhatsApp';
                            $jawaban_bot .= "🔔 **Notifikasi Restock Aktif:** Karena Anda sudah masuk, kami akan mengirimkan notifikasi otomatis ke nomor WhatsApp Anda yang terdaftar (**{$noWa}**) ketika barang ini sudah restock.\n\n";
                        } else {
                            $jawaban_bot .= "🔔 **Info Notifikasi:** Jika Anda ingin menerima notifikasi otomatis ketika barang ini ready kembali, silakan aktifkan notifikasi WhatsApp melalui menu edit profil Anda.\n\n";
                        }
                    } else {
                        $jawaban_bot .= "🔔 **Info Notifikasi:** Jika Anda ingin mendapatkan notifikasi secara otomatis ketika barang ini ready kembali, silakan **[Daftar Akun Baru](" . route('register') . ")** atau **[Masuk ke Akun Anda](" . route('login') . ")** dan aktifkan notifikasi WhatsApp pada profil Anda agar kami bisa memberi tahu saat barang sudah restock. Kami akan menghubungi Anda melalui nomor WhatsApp yang didaftarkan.\n\n";
                    }
                }

                // --- DETAIL KOMPATIBILITAS PRINTER ---
                $kategoriName = strtolower($product->kategori->nama_kategori ?? '');
                $namaProdukLower = strtolower($product->nama_produk);
                if (str_contains($kategoriName, 'printer') || str_contains($namaProdukLower, 'printer')) {
                    $nama_produk_bersih = preg_replace('/[^\w\s]/', '', strtolower($product->nama_produk));
                    $kata_produk = explode(' ', $nama_produk_bersih);
                    $modelName = null;
                    foreach ($kata_produk as $word) {
                        if (preg_match('/[0-9]/', $word) && strlen($word) >= 3) {
                            $modelName = $word;
                            break;
                        }
                    }

                    if ($modelName) {
                        $brand = null;
                        $brands = ['canon', 'epson', 'hp', 'brother'];
                        foreach ($brands as $b) {
                            if (str_contains($namaProdukLower, $b)) {
                                $brand = $b;
                                break;
                            }
                        }

                        // Cari produk tinta kompatibel
                        $tintaKompatibel = Produk::where('id', '!=', $product->id)
                            ->where(function($query) {
                                $query->where('nama_produk', 'LIKE', '%tinta%')
                                      ->orWhere('nama_produk', 'LIKE', '%ink%');
                            })
                            ->where(function($query) use ($modelName, $brand) {
                                $query->where('nama_produk', 'LIKE', '%' . $modelName . '%')
                                      ->orWhere('deskripsi', 'LIKE', '%' . $modelName . '%');
                                if ($brand) {
                                    $query->orWhere('nama_produk', 'LIKE', '%' . $brand . '%');
                                }
                            })
                            ->take(2)
                            ->get();

                        // Cari sparepart kompatibel
                        $komponenKompatibel = Produk::where('id', '!=', $product->id)
                            ->where(function($query) {
                                $query->where('nama_produk', 'LIKE', '%cartridge%')
                                      ->orWhere('nama_produk', 'LIKE', '%catridge%')
                                      ->orWhere('nama_produk', 'LIKE', '%sparepart%')
                                      ->orWhere('nama_produk', 'LIKE', '%head%')
                                      ->orWhere('nama_produk', 'LIKE', '%roller%')
                                      ->orWhere('nama_produk', 'LIKE', '%mainboard%')
                                      ->orWhere('nama_produk', 'LIKE', '%sensor%');
                            })
                            ->where(function($query) use ($modelName) {
                                $query->where('nama_produk', 'LIKE', '%' . $modelName . '%')
                                      ->orWhere('deskripsi', 'LIKE', '%' . $modelName . '%');
                            })
                            ->take(3)
                            ->get();

                        if ($tintaKompatibel->isNotEmpty() || $komponenKompatibel->isNotEmpty()) {
                            $jawaban_bot .= "🔧 **Kompatibilitas & Suku Cadang:**\n";
                            if ($tintaKompatibel->isNotEmpty()) {
                                $jawaban_bot .= "- **Tinta yang digunakan:** ";
                                $tintaList = [];
                                foreach ($tintaKompatibel as $tk) {
                                    $tintaList[] = "**{$tk->nama_produk}** (Rp " . number_format($tk->harga_jual, 0, ',', '.') . ")";
                                }
                                $jawaban_bot .= implode(', ', $tintaList) . "\n";
                            }
                            if ($komponenKompatibel->isNotEmpty()) {
                                $jawaban_bot .= "- **Komponen yang dapat diganti:** ";
                                $komponenList = [];
                                foreach ($komponenKompatibel as $kk) {
                                    $komponenList[] = "**{$kk->nama_produk}** (Rp " . number_format($kk->harga_jual, 0, ',', '.') . ")";
                                }
                                $jawaban_bot .= implode(', ', $komponenList) . "\n";
                            }
                            $jawaban_bot .= "\n";
                        }
                    }
                }

                // Cari rekomendasi pendukung umum
                $brand = null;
                $brands = ['canon', 'epson', 'hp', 'brother', 'asus', 'acer', 'lenovo', 'logitech', 'robot', 'fantech', 'rexus'];
                foreach ($brands as $b) {
                    if (str_contains(strtolower($product->nama_produk), $b)) {
                        $brand = $b;
                        break;
                    }
                }

                $rekomendasi_terkait = Produk::where('id', '!=', $product->id)
                    ->where(function($query) use ($brand) {
                        $query->where('nama_produk', 'LIKE', '%tinta%')
                              ->orWhere('nama_produk', 'LIKE', '%isi ulang%')
                              ->orWhere('nama_produk', 'LIKE', '%maintenance%')
                              ->orWhere('nama_produk', 'LIKE', '%cleaning%')
                              ->orWhere('nama_produk', 'LIKE', '%pembersih%')
                              ->orWhere('nama_produk', 'LIKE', '%aksesoris%')
                              ->orWhere('nama_produk', 'LIKE', '%perawatan%');
                        
                        if ($brand) {
                            $query->orWhere('nama_produk', 'LIKE', '%' . $brand . '%');
                        }
                    })
                    ->take(3)
                    ->get();

                if ($rekomendasi_terkait->isNotEmpty()) {
                    $jawaban_bot .= "Untuk mendukung perawatan & penggunaan perangkat Anda, kami menyarankan beberapa produk pendukung atau tinta isi ulang berikut:\n";
                    foreach ($rekomendasi_terkait as $rt) {
                        $rtStok = $rt->stok > 0 ? "Ready Stock" : "Stok Habis";
                        $jawaban_bot .= "- **{$rt->nama_produk}** &bull; Rp " . number_format($rt->harga_jual, 0, ',', '.') . " ({$rtStok})\n";
                    }
                    $jawaban_bot .= "\nAnda dapat memesan produk-produk di atas secara langsung melalui Katalog kami.";
                } else {
                    $jawaban_bot .= "Silakan kunjungi menu Katalog kami jika Anda ingin melihat detail produk lainnya atau melakukan pemesanan.";
                }

                $rekomendasi_produk = collect([$product])->merge($rekomendasi_terkait)->unique('id');
            } else {
                // Jika beberapa produk cocok
                $jawaban_bot = "Berikut adalah beberapa produk yang cocok dengan pencarian Anda:\n\n";
                foreach ($matchedProducts as $mp) {
                    $mpStok = $mp->stok > 0 ? "Ready Stock" : "Stok Habis";
                    $jawaban_bot .= "- **{$mp->nama_produk}** &bull; Rp " . number_format($mp->harga_jual, 0, ',', '.') . " ({$mpStok})\n";
                    if ($mp->stok <= 0) {
                        $jawaban_bot .= "  _(Dapatkan notifikasi otomatis saat restock dengan login & mengaktifkan notifikasi WhatsApp)_\n";
                    }
                }
                $jawaban_bot .= "\nSilakan kunjungi menu Katalog kami untuk melihat informasi detail produk di atas atau memesannya.";
                $rekomendasi_produk = $matchedProducts;
            }

            $kategori_pertanyaan = 'produk';
            $aturan_ditemukan = true;
        }

        // --- 2. FUZZY MATCHING (KATA KUNCI KNOWLEDGE BASE): Jika bukan menanyakan produk spesifik ---
        if (!$aturan_ditemukan) {
            $semua_aturan = AturanChatbot::all();
            $best_match = null;
            $best_score = 0.0;
            $threshold = 75.0; // Minimal kecocokan 75%

            foreach ($semua_aturan as $aturan) {
                // Mendukung multi-keyword/alias yang dipisahkan dengan tanda koma
                $aliases = explode(',', $aturan->kata_kunci);
                foreach ($aliases as $alias) {
                    $alias = trim($alias);
                    if (empty($alias)) continue;

                    $score = $this->calculateSimilarity($pesan_user, $alias);
                    if ($score > $best_score) {
                        $best_score = $score;
                        $best_match = $aturan;
                        $kata_kunci_cocok = strtolower(trim($alias));
                    }
                }
            }

            if ($best_match && $best_score >= $threshold) {
                $jawaban_bot = $best_match->jawaban;
                $kata_kunci_cocok = strtolower($best_match->kata_kunci); // Gunakan string kata_kunci lengkap untuk log / pencarian terkait
                $aturan_ditemukan = true;
                $kategori_pertanyaan = $kata_kunci_cocok;
            }
        }

        // --- Tentukan kata pencarian ke database ---
        $kata_pencarian = $aturan_ditemukan ? ($kategori_pertanyaan === 'produk' ? [] : [$kata_kunci_cocok]) : $kata_input;

        // --- Cari Produk & Jasa ---
        foreach ($kata_pencarian as $kata) {
            if (strlen($kata) > 2) {
                $cari_produk = Produk::with('kategori')
                    ->where('nama_produk', 'LIKE', '%' . $kata . '%')
                    ->orWhere('deskripsi', 'LIKE', '%' . $kata . '%')
                    ->orWhereHas('kategori', function ($q) use ($kata) {
                        $q->where('nama_kategori', 'LIKE', '%' . $kata . '%');
                    })
                    ->get();

                $rekomendasi_produk = $rekomendasi_produk->merge($cari_produk);
            }
        }

        $hasil_produk = $rekomendasi_produk->unique('id')->values();
        $hasil_jasa = $rekomendasi_jasa->unique('id')->values();

        if (!$aturan_ditemukan && ($hasil_produk->count() > 0)) {
            $jawaban_bot = "Berikut adalah beberapa produk atau layanan terkait yang berhasil saya temukan untuk Anda:";
            if ($hasil_produk->count() > 0) $kategori_pertanyaan = 'produk';
        }

        // --- Simpan Log Chatbot ---
        try {
            ChatbotLog::create([
                'user_id' => Auth::id(),
                'pesan' => $request->pesan,
                'jawaban' => $jawaban_bot,
                'kategori' => $kategori_pertanyaan,
            ]);
        } catch (\Exception $e) {
            // Abaikan error logging
        }

        return response()->json([
            'jawaban' => $jawaban_bot,
            'rekomendasi_produk' => $hasil_produk,
            'rekomendasi_jasa' => $hasil_jasa,
        ]);
    }

    /**
     * Mengecek apakah kata 1 dan kata 2 cocok secara fuzzy.
     */
    private function isWordFuzzyMatch(string $w1, string $w2): bool
    {
        if ($w1 === $w2) {
            return true;
        }

        $len1 = strlen($w1);
        $len2 = strlen($w2);

        // Abaikan kecocokan fuzzy jika kata terlalu pendek (menghindari false positive pada kata-kata 1-2 huruf)
        if ($len1 < 3 || $len2 < 3) {
            return false;
        }

        // Hitung jarak Levenshtein
        $lev = levenshtein($w1, $w2);
        if ($lev <= 1) {
            return true;
        }
        if ($lev <= 2 && ($len1 > 5 || $len2 > 5)) {
            return true;
        }

        // Hitung persentase kecocokan
        similar_text($w1, $w2, $percent);
        if ($percent >= 80.0) {
            return true;
        }

        return false;
    }

    /**
     * Menghitung tingkat kecocokan (persentase 0 - 100) antara pesan user dengan kata kunci alias.
     */
    private function calculateSimilarity(string $pesan_user, string $alias): float
    {
        $pesan_user = strtolower(trim($pesan_user));
        $alias = strtolower(trim($alias));

        if ($pesan_user === $alias) {
            return 100.0;
        }

        if (str_contains($pesan_user, $alias)) {
            return 95.0;
        }

        // Hapus tanda baca dan pecah menjadi array kata
        $words_user = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $pesan_user)));
        $words_alias = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $alias)));

        if (empty($words_alias) || empty($words_user)) {
            return 0.0;
        }

        $matched_count = 0;
        $total_similarity = 0.0;

        foreach ($words_alias as $wa) {
            $best_word_sim = 0.0;
            $best_word_match = false;
            foreach ($words_user as $wu) {
                if ($this->isWordFuzzyMatch($wa, $wu)) {
                    $best_word_match = true;
                    similar_text($wa, $wu, $percent);
                    if ($percent > $best_word_sim) {
                        $best_word_sim = $percent;
                    }
                }
            }

            if ($best_word_match) {
                $matched_count++;
                $total_similarity += ($best_word_sim > 0 ? $best_word_sim : 80.0);
            }
        }

        // Jika semua kata kunci alias dapat ditemukan/dicocokkan secara fuzzy di pesan user
        $alias_count = count($words_alias);
        if ($matched_count > 0) {
            $average_sim = $total_similarity / $matched_count;
            // Berikan penalti berdasarkan proporsi kata alias yang cocok
            return $average_sim * ($matched_count / $alias_count);
        }

        return 0.0;
    }

    /**
     * Mengekstrak kata kunci unik dari teks setelah membersihkan tanda baca dan kata henti.
     */
    private function extractKeywords(string $text, array $stopWords): array
    {
        $cleaned = preg_replace('/[^\w\s]/', '', strtolower($text));
        $words = explode(' ', $cleaned);
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 1 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        return array_unique($keywords);
    }

    /**
     * Memformat input nominal budget ke integer.
     */
    private function parseBudget(string $input): int
    {
        $input = str_replace(['rp', ' '], '', strtolower($input));
        $input = str_replace(',', '.', $input);
        
        if (str_contains($input, 'juta') || str_contains($input, 'jt')) {
            $numPart = str_replace(['juta', 'jt'], '', $input);
            $val = floatval($numPart);
            return intval($val * 1000000);
        }
        
        $input = str_replace('.', '', $input);
        return intval($input);
    }

    /**
     * Menghasilkan rekomendasi PC rakitan yang kompatibel.
     */
    private function generatePCBuild($budget, $brand, $purpose, $include_monitor, $include_kb_mouse, $include_wifi)
    {
        $dbProducts = Produk::with('kategori')->get();
        $recommendedProducts = collect();
        $components = [];

        // 1. Hitung harga aksesoris opsional & cari di DB atau fallback
        $monitorPrice = 0;
        $kbMousePrice = 0;
        $wifiPrice = 0;

        if ($include_monitor) {
            $dbMonitor = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'monitor';
            });
            if ($dbMonitor) {
                $recommendedProducts->push($dbMonitor);
                $monitorName = $dbMonitor->nama_produk;
                $monitorPrice = intval($dbMonitor->harga_jual);
                $monitorDb = true;
            } else {
                $monitorName = "Monitor LED 24 Inch IPS Standard";
                $monitorPrice = 1350000;
                $monitorDb = false;
            }
            $components['Monitor'] = [
                'name' => $monitorName,
                'price' => $monitorPrice,
                'db' => $monitorDb
            ];
        }

        if ($include_kb_mouse) {
            $dbKb = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'keyboard mouse';
            });
            if ($dbKb) {
                $recommendedProducts->push($dbKb);
                $kbName = $dbKb->nama_produk;
                $kbPrice = intval($dbKb->harga_jual);
                $kbDb = true;
            } else {
                $kbName = "Logitech MK120 Keyboard Mouse Combo";
                $kbPrice = 180000;
                $kbDb = false;
            }
            $components['Keyboard & Mouse'] = [
                'name' => $kbName,
                'price' => $kbPrice,
                'db' => $kbDb
            ];
        }

        if ($include_wifi) {
            $dbWifi = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'wifi adapter';
            });
            if ($dbWifi) {
                $recommendedProducts->push($dbWifi);
                $wifiName = $dbWifi->nama_produk;
                $wifiPrice = intval($dbWifi->harga_jual);
                $wifiDb = true;
            } else {
                $wifiName = "USB Wifi Adapter TP-Link TL-WN725N";
                $wifiPrice = 120000;
                $wifiDb = false;
            }
            $components['Wifi Adapter'] = [
                'name' => $wifiName,
                'price' => $wifiPrice,
                'db' => $wifiDb
            ];
        }

        $optionsCost = $monitorPrice + $kbMousePrice + $wifiPrice;
        $remBudget = $budget - $optionsCost;

        // 2. Tentukan Tier berdasarkan sisa budget CPU
        $tier = 'Low';
        if ($remBudget >= 8500000) {
            $tier = 'High';
        } elseif ($remBudget >= 4500000) {
            $tier = 'Mid';
        }

        // Tentukan Brand CPU jika Bebas
        $actualBrand = $brand;
        if ($brand === 'Bebas') {
            if ($remBudget < 5000000 || $purpose === 'Office') {
                $actualBrand = 'AMD';
            } else {
                $actualBrand = 'Intel';
            }
        }

        // 3. Cari komponen inti CPU
        $cpuName = ''; $cpuPrice = 0; $cpuDb = false;
        $moboName = ''; $moboPrice = 0; $moboDb = false;
        $ramName = ''; $ramPrice = 0; $ramDb = false;
        $ssdName = ''; $ssdPrice = 0; $ssdDb = false;
        $caseName = ''; $casePrice = 0; $caseDb = false;
        $vgaName = ''; $vgaPrice = 0; $vgaDb = false;

        // RAM & SSD & Case Selection
        if ($tier === 'High') {
            $dbRam = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ram' && str_contains(strtolower($p->nama_produk), '16gb');
            });
            if ($dbRam) {
                $recommendedProducts->push($dbRam);
                $ramName = $dbRam->nama_produk;
                $ramPrice = intval($dbRam->harga_jual);
                $ramDb = true;
            } else {
                $ramName = "Corsair Vengeance LPX DDR4 16GB (2x8GB) 3200MHz";
                $ramPrice = 680000;
                $ramDb = false;
            }

            $dbSsd = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ssd' && str_contains(strtolower($p->nama_produk), '1tb');
            });
            if ($dbSsd) {
                $recommendedProducts->push($dbSsd);
                $ssdName = $dbSsd->nama_produk;
                $ssdPrice = intval($dbSsd->harga_jual);
                $ssdDb = true;
            } else {
                $ssdName = "Samsung 980 NVMe M.2 SSD 1TB";
                $ssdPrice = 1150000;
                $ssdDb = false;
            }

            $dbCase = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'casing' && str_contains(strtolower($p->nama_produk), 'venomrx');
            });
            if ($dbCase) {
                $recommendedProducts->push($dbCase);
                $caseName = $dbCase->nama_produk;
                $casePrice = intval($dbCase->harga_jual);
                $caseDb = true;
            } else {
                $caseName = "Casing Gaming VenomRX + PSU 500W 80 Plus";
                $casePrice = 800000;
                $caseDb = false;
            }
        } elseif ($tier === 'Mid') {
            $dbRam = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ram' && str_contains(strtolower($p->nama_produk), '16gb');
            });
            if ($dbRam) {
                $recommendedProducts->push($dbRam);
                $ramName = $dbRam->nama_produk;
                $ramPrice = intval($dbRam->harga_jual);
                $ramDb = true;
            } else {
                $ramName = "Corsair Vengeance LPX DDR4 16GB (2x8GB) 3200MHz";
                $ramPrice = 680000;
                $ramDb = false;
            }

            $dbSsd = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ssd' && str_contains(strtolower($p->nama_produk), '512gb');
            });
            if ($dbSsd) {
                $recommendedProducts->push($dbSsd);
                $ssdName = $dbSsd->nama_produk;
                $ssdPrice = intval($dbSsd->harga_jual);
                $ssdDb = true;
            } else {
                $ssdName = "Kingston NV2 NVMe M.2 SSD 512GB";
                $ssdPrice = 580000;
                $ssdDb = false;
            }

            $dbCase = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'casing' && str_contains(strtolower($p->nama_produk), 'venomrx');
            });
            if ($dbCase) {
                $recommendedProducts->push($dbCase);
                $caseName = $dbCase->nama_produk;
                $casePrice = intval($dbCase->harga_jual);
                $caseDb = true;
            } else {
                $caseName = "Casing Gaming VenomRX + PSU 500W 80 Plus";
                $casePrice = 800000;
                $caseDb = false;
            }
        } else {
            $dbRam = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ram' && str_contains(strtolower($p->nama_produk), '8gb');
            });
            if ($dbRam) {
                $recommendedProducts->push($dbRam);
                $ramName = $dbRam->nama_produk;
                $ramPrice = intval($dbRam->harga_jual);
                $ramDb = true;
            } else {
                $ramName = "Kingston Fury Beast DDR4 8GB 3200MHz";
                $ramPrice = 350000;
                $ramDb = false;
            }

            $dbSsd = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'ssd' && str_contains(strtolower($p->nama_produk), '512gb');
            });
            if ($dbSsd) {
                $recommendedProducts->push($dbSsd);
                $ssdName = $dbSsd->nama_produk;
                $ssdPrice = intval($dbSsd->harga_jual);
                $ssdDb = true;
            } else {
                $ssdName = "Kingston NV2 NVMe M.2 SSD 512GB";
                $ssdPrice = 580000;
                $ssdDb = false;
            }

            $dbCase = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'casing' && str_contains(strtolower($p->nama_produk), 'simbadda');
            });
            if ($dbCase) {
                $recommendedProducts->push($dbCase);
                $caseName = $dbCase->nama_produk;
                $casePrice = intval($dbCase->harga_jual);
                $caseDb = true;
            } else {
                $caseName = "Casing Simbadda Standard + PSU 450W";
                $casePrice = 450000;
                $caseDb = false;
            }
        }

        // Core Brand Compatibility
        if ($actualBrand === 'Intel') {
            if ($tier === 'High') {
                $dbCpu = $dbProducts->first(function($p) {
                    return strtolower($p->kategori->nama_kategori ?? '') === 'processor' && str_contains(strtolower($p->nama_produk), 'i5-12400f');
                });
                if ($dbCpu) {
                    $recommendedProducts->push($dbCpu);
                    $cpuName = $dbCpu->nama_produk;
                    $cpuPrice = intval($dbCpu->harga_jual);
                    $cpuDb = true;
                } else {
                    $cpuName = "Intel Core i5-12400F Processor (LGA1700)";
                    $cpuPrice = 1800000;
                    $cpuDb = false;
                }
            } else {
                $dbCpu = $dbProducts->first(function($p) {
                    return strtolower($p->kategori->nama_kategori ?? '') === 'processor' && str_contains(strtolower($p->nama_produk), 'i3-12100f');
                });
                if ($dbCpu) {
                    $recommendedProducts->push($dbCpu);
                    $cpuName = $dbCpu->nama_produk;
                    $cpuPrice = intval($dbCpu->harga_jual);
                    $cpuDb = true;
                } else {
                    $cpuName = "Intel Core i3-12100F Processor (LGA1700)";
                    $cpuPrice = 1250000;
                    $cpuDb = false;
                }
            }

            $dbMobo = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'motherboard' && str_contains(strtolower($p->nama_produk), 'h610m');
            });
            if ($dbMobo) {
                $recommendedProducts->push($dbMobo);
                $moboName = $dbMobo->nama_produk;
                $moboPrice = intval($dbMobo->harga_jual);
                $moboDb = true;
            } else {
                $moboName = "MSI PRO H610M-E DDR4 Motherboard (LGA1700)";
                $moboPrice = 980000;
                $moboDb = false;
            }

            $dbVga = $dbProducts->first(function($p) {
                return strtolower($p->kategori->nama_kategori ?? '') === 'vga' && str_contains(strtolower($p->nama_produk), 'rx 580');
            });
            if ($dbVga) {
                $recommendedProducts->push($dbVga);
                $vgaName = $dbVga->nama_produk;
                $vgaPrice = intval($dbVga->harga_jual);
                $vgaDb = true;
            } else {
                $vgaName = "Radeon RX 580 8GB Graphic Card";
                $vgaPrice = 1100000;
                $vgaDb = false;
            }
        } else {
            // AMD Brand
            if ($purpose === 'Office' || ($tier === 'Low' && $remBudget < 4500000)) {
                $dbCpu = $dbProducts->first(function($p) {
                    return strtolower($p->kategori->nama_kategori ?? '') === 'processor' && str_contains(strtolower($p->nama_produk), '5600g');
                });
                if ($dbCpu) {
                    $recommendedProducts->push($dbCpu);
                    $cpuName = $dbCpu->nama_produk;
                    $cpuPrice = intval($dbCpu->harga_jual);
                    $cpuDb = true;
                } else {
                    $cpuName = "AMD Ryzen 5 5600G Processor (AM4, Integrated Radeon)";
                    $cpuPrice = 1900000;
                    $cpuDb = false;
                }

                $dbMobo = $dbProducts->first(function($p) {
                    return strtolower($p->kategori->nama_kategori ?? '') === 'motherboard' && str_contains(strtolower($p->nama_produk), 'a320m');
                });
                if ($dbMobo) {
                    $recommendedProducts->push($dbMobo);
                    $moboName = $dbMobo->nama_produk;
                    $moboPrice = intval($dbMobo->harga_jual);
                    $moboDb = true;
                } else {
                    $moboName = "ASRock A320M-HDV Motherboard (AM4)";
                    $moboPrice = 780000;
                    $moboDb = false;
                }

                $vgaName = "Integrated Radeon Graphics (Bawaan Processor)";
                $vgaPrice = 0;
                $vgaDb = false;
            } else {
                if ($tier === 'High') {
                    $dbCpu = $dbProducts->first(function($p) {
                        return strtolower($p->kategori->nama_kategori ?? '') === 'processor' && str_contains(strtolower($p->nama_produk), '5600g');
                    });
                    if ($dbCpu) {
                        $recommendedProducts->push($dbCpu);
                        $cpuName = $dbCpu->nama_produk;
                        $cpuPrice = intval($dbCpu->harga_jual);
                        $cpuDb = true;
                    } else {
                        $cpuName = "AMD Ryzen 5 5600G Processor (AM4)";
                        $cpuPrice = 1900000;
                        $cpuDb = false;
                    }

                    $dbMobo = $dbProducts->first(function($p) {
                        return strtolower($p->kategori->nama_kategori ?? '') === 'motherboard' && str_contains(strtolower($p->nama_produk), 'b550m');
                    });
                    if ($dbMobo) {
                        $recommendedProducts->push($dbMobo);
                        $moboName = $dbMobo->nama_produk;
                        $moboPrice = intval($dbMobo->harga_jual);
                        $moboDb = true;
                    } else {
                        $moboName = "ASRock B550M-HDV Motherboard (AM4)";
                        $moboPrice = 1350000;
                        $moboDb = false;
                    }
                } else {
                    $dbCpu = $dbProducts->first(function($p) {
                        return strtolower($p->kategori->nama_kategori ?? '') === 'processor' && str_contains(strtolower($p->nama_produk), '4100');
                    });
                    if ($dbCpu) {
                        $recommendedProducts->push($dbCpu);
                        $cpuName = $dbCpu->nama_produk;
                        $cpuPrice = intval($dbCpu->harga_jual);
                        $cpuDb = true;
                    } else {
                        $cpuName = "AMD Ryzen 3 4100 Processor (AM4)";
                        $cpuPrice = 950000;
                        $cpuDb = false;
                    }

                    $dbMobo = $dbProducts->first(function($p) {
                        return strtolower($p->kategori->nama_kategori ?? '') === 'motherboard' && str_contains(strtolower($p->nama_produk), 'a320m');
                    });
                    if ($dbMobo) {
                        $recommendedProducts->push($dbMobo);
                        $moboName = $dbMobo->nama_produk;
                        $moboPrice = intval($dbMobo->harga_jual);
                        $moboDb = true;
                    } else {
                        $moboName = "ASRock A320M-HDV Motherboard (AM4)";
                        $moboPrice = 780000;
                        $moboDb = false;
                    }
                }

                $dbVga = $dbProducts->first(function($p) {
                    return strtolower($p->kategori->nama_kategori ?? '') === 'vga' && str_contains(strtolower($p->nama_produk), 'rx 580');
                });
                if ($dbVga) {
                    $recommendedProducts->push($dbVga);
                    $vgaName = $dbVga->nama_produk;
                    $vgaPrice = intval($dbVga->harga_jual);
                    $vgaDb = true;
                } else {
                    $vgaName = "Radeon RX 580 8GB Graphic Card";
                    $vgaPrice = 1100000;
                    $vgaDb = false;
                }
            }
        }

        $components['Processor'] = ['name' => $cpuName, 'price' => $cpuPrice, 'db' => $cpuDb];
        $components['Motherboard'] = ['name' => $moboName, 'price' => $moboPrice, 'db' => $moboDb];
        $components['RAM'] = ['name' => $ramName, 'price' => $ramPrice, 'db' => $ramDb];
        $components['SSD'] = ['name' => $ssdName, 'price' => $ssdPrice, 'db' => $ssdDb];
        $components['Casing & PSU'] = ['name' => $caseName, 'price' => $casePrice, 'db' => $caseDb];
        if ($vgaPrice > 0) {
            $components['VGA / Kartu Grafis'] = ['name' => $vgaName, 'price' => $vgaPrice, 'db' => $vgaDb];
        }

        // 4. Hitung Total & Persiapkan Jawaban
        $totalCost = 0;
        foreach ($components as $key => $comp) {
            $totalCost += $comp['price'];
        }

        $totalCostFormatted = number_format($totalCost, 0, ',', '.');
        $budgetFormatted = number_format($budget, 0, ',', '.');
        
        $jawaban = "### 🖥️ REKOMENDASI SPESIFIKASI CPU RAKITAN Anda\n";
        $jawaban .= "Berikut adalah spesifikasi PC rakitan yang telah kami sesuaikan agar **100% kompatibel**, pas untuk kebutuhan **{$purpose}**, dan optimal untuk budget **Rp {$budgetFormatted}**:\n\n";

        foreach ($components as $key => $comp) {
            $dbIndicator = $comp['db'] ? " ✅ _(Tersedia di Toko)_" : "";
            $jawaban .= "* **{$key}**: {$comp['name']}{$dbIndicator}\n";
        }

        $jawaban .= "\n";
        $jawaban .= "---------------------------------------------\n";
        $jawaban .= "📊 **RINGKASAN BIAYA:**\n";
        $jawaban .= "💰 Target Budget: **Rp {$budgetFormatted}**\n";
        $jawaban .= "⚙️ Total Estimasi Rakitan: **Rp {$totalCostFormatted}**\n";
        
        $diff = $budget - $totalCost;
        if ($diff >= 0) {
            $diffFormatted = number_format($diff, 0, ',', '.');
            $jawaban .= "🟢 Sisa Budget: **Rp {$diffFormatted}**\n\n";
        } else {
            $over = abs($diff);
            $overFormatted = number_format($over, 0, ',', '.');
            $jawaban .= "🔴 Melebihi Budget: **Rp {$overFormatted}**\n\n";
        }

        $socketInfo = ($actualBrand === 'Intel') ? 'LGA1700' : 'AM4';
        $jawaban .= "⚠️ **Catatan Kompatibilitas:**\n";
        $jawaban .= "- Processor dan Motherboard menggunakan socket **{$socketInfo}** dan tipe RAM **DDR4**.\n";
        if ($actualBrand === 'Intel' || ($actualBrand === 'AMD' && $vgaPrice > 0 && $cpuName !== 'AMD Ryzen 5 5600G Processor')) {
            $jawaban .= "- Rakitan ini dilengkapi kartu grafis discrete (GPU) **{$vgaName}** karena processor yang dipilih memerlukan GPU eksternal untuk menampilkan layar.\n";
        } else {
            $jawaban .= "- Rakitan ini menggunakan Radeon Integrated Graphics bawaan Ryzen 5 5600G yang hemat daya namun bertenaga.\n";
        }
        
        if ($recommendedProducts->isNotEmpty()) {
            $jawaban .= "\n🛍️ **Rekomendasi Produk:** Kami telah mencocokkan beberapa komponen di atas dengan stok barang di toko kami. Silakan klik produk di bawah ini untuk melihat detailnya di Katalog.";
        }

        return [
            'jawaban' => $jawaban,
            'rekomendasi_produk' => $recommendedProducts->unique('id')->values(),
        ];
    }
}


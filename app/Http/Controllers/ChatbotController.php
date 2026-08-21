<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\ChatbotLog;
use App\Models\Transaksi;
use App\Models\Komplain;
use App\Models\User;
use App\Models\Notifikasi;
use App\Services\WhatsAppService;
use App\Services\DeepSeekAiService;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected DeepSeekAiService $deepSeekService;

    public function __construct(DeepSeekAiService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * Memproses pesan chat dari pengguna dan memberikan respon AI cerdas dari DeepSeek.
     */
    public function getResponse(Request $request)
    {
        $pesan_user = strtolower(trim($request->pesan ?? ''));
        $pesan_original = trim($request->pesan ?? '');

        if (empty($pesan_original)) {
            return response()->json([
                'jawaban' => 'Halo! Ada yang bisa kami bantu seputar produk atau layanan servis di Nusantara Jaya Computer?',
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }

        // --- 1. CEK JIKA USER MEMASUKKAN / MENANYAKAN NOMOR TRANSAKSI (TRX-...) ---
        $flow = session('chatbot_flow');
        if (preg_match('/TRX-[A-Z0-9_-]+/i', $pesan_original, $matches) && $flow !== 'complaint_waiting_trx') {
            $trxCode = strtoupper($matches[0]);
            $transaction = Transaksi::with(['user', 'detail.produk'])->where('kode_transaksi', $trxCode)->first();

            if ($transaction) {
                $totalFormatted = number_format($transaction->total_bayar, 0, ',', '.');
                $namaPelanggan = $transaction->nama_pelanggan;

                $jawaban_bot = "Halo **{$namaPelanggan}**! Pesanan Anda dengan Nomor Transaksi **{$transaction->kode_transaksi}** (Total: **Rp {$totalFormatted}**) telah tercatat di sistem kami dan **akan segera diproses oleh admin**.\n\nTerima kasih telah berbelanja di Nusantara Jaya Computer! Mohon ditunggu konfirmasi dari tim kami.";

                // Buat notifikasi in-app untuk Admin
                try {
                    Notifikasi::create([
                        'user_id' => null,
                        'judul' => '🤖 Konfirmasi Checkout via Chatbot',
                        'pesan' => "Pelanggan **{$namaPelanggan}** mengonfirmasi transaksi **{$transaction->kode_transaksi}** melalui Chatbot.",
                        'link' => route('transaksi.index', ['kode' => $transaction->kode_transaksi]),
                        'is_read' => false,
                        'tipe' => 'chatbot',
                    ]);

                    // Kirim WA Notif ke Admin (0851-8239-2525)
                    $waService = new WhatsAppService();
                    $waService->send('0851-8239-2525', "*NUSANTARA JAYA COMPUTER*\nPelanggan *{$namaPelanggan}* mengonfirmasi transaksi *{$transaction->kode_transaksi}* via Chatbot.\nMohon segera diproses oleh Admin.");
                } catch (\Exception $e) {
                    // Abaikan error notifikasi
                }

                $this->saveChatLog($pesan_original, $jawaban_bot, 'konfirmasi_transaksi');

                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            } else {
                $jawaban_bot = "Maaf, nomor transaksi **{$trxCode}** tidak ditemukan di sistem kami. Mohon pastikan kode transaksi yang Anda masukkan sudah sesuai.";
                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
        }

        // --- 2. CEK SESSION FLOW UNTUK KOMPLAIN TRANSAKSI ---
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
            $transaction = Transaksi::with('user')->where('kode_transaksi', $trxCode)->first();

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

            $transaction = Transaksi::with('user')->find($trxId);
            $customerId = Auth::id() ?: ($transaction ? $transaction->user_id : null);
            $customerName = Auth::check() ? Auth::user()->name : ($transaction ? $transaction->nama_pelanggan : 'Guest');

            $customerPhone = '-';
            if (Auth::check() && !empty(Auth::user()->no_whatsapp)) {
                $customerPhone = Auth::user()->no_whatsapp;
            } elseif ($transaction && $transaction->user && !empty($transaction->user->no_whatsapp)) {
                $customerPhone = $transaction->user->no_whatsapp;
            }

            // Simpan komplain resmi ke database
            Komplain::create([
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
            $recipients = $trxType === 'penjualan'
                ? User::whereIn('peran', ['admin', 'kasir'])->whereNotNull('no_whatsapp')->where('no_whatsapp', '!=', '')->get()
                : User::whereIn('peran', ['admin', 'teknisi'])->whereNotNull('no_whatsapp')->where('no_whatsapp', '!=', '')->get();

            $waService = new WhatsAppService();
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

            session()->forget(['chatbot_flow', 'complaint_trx_id', 'complaint_trx_code', 'complaint_trx_type']);
            $this->saveChatLog("Komplain transaksi {$trxCode}: {$desc}", "Laporan komplain berhasil dicatat dan diteruskan ke tim terkait.", 'komplain');

            $tipeStaf = $trxType === 'penjualan' ? 'Admin/Kasir' : 'Admin/Teknisi';
            $jawaban_bot = "Terima kasih. Laporan komplain Anda telah resmi tercatat di sistem kami dengan Kode Transaksi **{$trxCode}**.\n\nTim kami ({$tipeStaf}) telah dinotifikasi dan akan segera menghubungi Anda melalui nomor WhatsApp Anda (**{$customerPhone}**) untuk membantu penyelesaian kendala ini. Mohon ditunggu.";

            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }

        // --- 3. CEK INISIASI FORM KOMPLAIN RESMI VIA KATA KUNCI KOMPLAIN ---
        if (str_contains($pesan_user, 'komplain') || str_contains($pesan_user, 'complain') || str_contains($pesan_user, 'komplen')) {
            session(['chatbot_flow' => 'complaint_waiting_trx']);
            $jawaban_bot = "Saya prihatin mendengar kendala yang Anda alami. Mohon maaf atas ketidaknyamanan ini.\n\nUntuk memproses laporan komplain Anda, mohon masukkan **nomor transaksi** Anda (contoh: **TRX-XXXXXXXXXX** atau kode transaksi penjualan/servis Anda).\n_(Ketik **batal** untuk keluar)_";
            
            $this->saveChatLog($pesan_original, $jawaban_bot, 'komplain');

            return response()->json([
                'jawaban' => $jawaban_bot,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ]);
        }

        // --- 4. CEK JIKA USER ADALAH GUEST DAN BERNIAT MEMBELI / TRANSAKSI ---
        if (!Auth::check()) {
            $kata_kunci_beli = ['beli', 'pesan', 'order', 'checkout', 'bayar', 'keranjang', 'ambil', 'pembelian', 'pemesanan', 'purchase'];
            $ingin_beli = false;
            foreach ($kata_kunci_beli as $kk) {
                if (str_contains($pesan_user, $kk)) {
                    $ingin_beli = true;
                    break;
                }
            }

            if ($ingin_beli) {
                $jawaban_bot = "Untuk melakukan transaksi pembelian barang secara aman dan terverifikasi di **Nusantara Jaya Computer**, silakan buat akun atau masuk terlebih dahulu demi keamanan transaksi Anda.\n\nSilakan **[Daftar Akun Baru](" . route('register') . ")** atau **[Masuk ke Akun Anda](" . route('login') . ")** untuk melanjutkan pemesanan.";
                $this->saveChatLog($pesan_original, $jawaban_bot, 'transaksi_guest');

                return response()->json([
                    'jawaban' => $jawaban_bot,
                    'rekomendasi_produk' => collect(),
                    'rekomendasi_jasa' => collect(),
                ]);
            }
        }

        // --- 5. PROSES PERTANYAAN MENGGUNAKAN DEEPSEEK AI DENGAN GUARDRAILS KETAT ---
        // Ambil riwayat chat dari session
        $chatHistory = session('chatbot_history', []);

        // Dapatkan jawaban cerdas dan rekomendasi dari DeepSeek AI Service
        $aiResult = $this->deepSeekService->chat($pesan_original, $chatHistory);

        // Update riwayat chat pada session (maksimal 6 interaksi terakhir)
        $chatHistory[] = ['role' => 'user', 'content' => $pesan_original];
        $chatHistory[] = ['role' => 'assistant', 'content' => $aiResult['jawaban']];
        if (count($chatHistory) > 12) {
            $chatHistory = array_slice($chatHistory, -12);
        }
        session(['chatbot_history' => $chatHistory]);

        // Tentukan kategori untuk laporan analitik
        $kategori = $this->determineCategory($pesan_user, $aiResult['jawaban']);

        // Simpan riwayat chat ke database log
        $this->saveChatLog($pesan_original, $aiResult['jawaban'], $kategori);

        return response()->json([
            'jawaban' => $aiResult['jawaban'],
            'rekomendasi_produk' => $aiResult['rekomendasi_produk'],
            'rekomendasi_jasa' => $aiResult['rekomendasi_jasa'],
        ]);
    }

    /**
     * Menyimpan log percakapan chatbot.
     */
    protected function saveChatLog(string $pesan, string $jawaban, ?string $kategori = 'deepseek_ai'): void
    {
        try {
            ChatbotLog::create([
                'user_id' => Auth::id(),
                'pesan' => $pesan,
                'jawaban' => $jawaban,
                'kategori' => $kategori,
            ]);
        } catch (\Exception $e) {
            // Abaikan error logging database
        }
    }

    /**
     * Menentukan kategori pertanyaan untuk keperluan analitik & pelaporan.
     */
    protected function determineCategory(string $pesanUser, string $jawabanBot): string
    {
        $text = strtolower($pesanUser . ' ' . $jawabanBot);

        if (str_contains($text, 'servis') || str_contains($text, 'service') || str_contains($text, 'instal') || str_contains($text, 'lcd') || str_contains($text, 'perbaikan') || str_contains($text, 'rusak')) {
            return 'servis';
        }
        if (str_contains($text, 'laptop') || str_contains($text, 'notebook')) {
            return 'laptop';
        }
        if (str_contains($text, 'printer') || str_contains($text, 'tinta') || str_contains($text, 'cetak')) {
            return 'printer';
        }
        if (str_contains($text, 'rakit') || str_contains($text, 'komponen') || str_contains($text, 'processor') || str_contains($text, 'ram') || str_contains($text, 'vga')) {
            return 'rakit_pc';
        }
        if (str_contains($text, 'lokasi') || str_contains($text, 'alamat') || str_contains($text, 'maps') || str_contains($text, 'whatsapp') || str_contains($text, 'kontak')) {
            return 'info_toko';
        }
        if (str_contains($text, 'harga') || str_contains($text, 'produk') || str_contains($text, 'stok') || str_contains($text, 'katalog')) {
            return 'produk';
        }

        return 'konsultasi_ai';
    }
}

<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\JasaServis;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '') ?: (env('GEMINI_API_KEY', '') ?: '');
        $this->model = config('services.gemini.model', 'gemini-3.6-flash');
        $this->baseUrl = rtrim(config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->timeout = (int) config('services.gemini.timeout', 30);
    }

    /**
     * Memeriksa apakah API Key telah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        return !empty(trim($this->apiKey));
    }

    /**
     * Menghasilkan balasan dari Google Gemini AI berdasarkan pesan user.
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @return array ['jawaban' => string, 'rekomendasi_produk' => Collection, 'rekomendasi_jasa' => Collection]
     */
    public function chat(string $userMessage, array $chatHistory = []): array
    {
        // 1. Jika API key belum diisi, gunakan fallback lokal cerdas
        if (!$this->isConfigured()) {
            return $this->handleFallback($userMessage, "Layanan AI Gemini belum dihubungkan (API Key belum dikonfigurasi).");
        }

        // 2. Siapkan System Prompt
        $systemPrompt = $this->buildSystemPrompt();

        // 3. Susun contents untuk Google Gemini REST API
        $contents = [];

        // Masukkan riwayat percakapan (maksimal 6 interaksi terakhir)
        $recentHistory = array_slice($chatHistory, -6);
        foreach ($recentHistory as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $role = ($msg['role'] === 'user') ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => (string) $msg['content']]
                    ]
                ];
            }
        }

        // Tambahkan pesan user saat ini
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        try {
            $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";
            
            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.5,
                    'maxOutputTokens' => 1500,
                ]
            ];

            $response = Http::timeout($this->timeout)->post($endpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $replyText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

                if (!empty(trim($replyText))) {
                    $recommended = $this->findRelevantRecommendations($userMessage, $replyText);

                    return [
                        'jawaban' => $replyText,
                        'rekomendasi_produk' => $recommended['produk'],
                        'rekomendasi_jasa' => $recommended['jasa'],
                    ];
                }
            }

            Log::warning('Gemini API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->handleFallback($userMessage, "Maaf, server AI sedang mengalami sedikit kendala.");
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return $this->handleFallback($userMessage, "Maaf, terjadi kendala saat menghubungi AI.");
        }
    }

    /**
     * Membangun System Prompt dengan batasan ketat (Guardrails) dan katalog produk/jasa terkini.
     */
    protected function buildSystemPrompt(): string
    {
        $products = Produk::with('kategori:id,nama_kategori')
            ->select('id', 'kategori_id', 'merk', 'nama_produk', 'stok', 'harga_jual', 'deskripsi')
            ->get();

        $services = JasaServis::select('id', 'nama_jasa', 'biaya_jasa')->get();

        $productListStr = "";
        foreach ($products as $p) {
            $kat = $p->kategori->nama_kategori ?? 'Umum';
            $stokStr = $p->stok > 0 ? "Ready Stock ({$p->stok} unit)" : "Stok Habis";
            $hargaStr = "Rp " . number_format($p->harga_jual, 0, ',', '.');
            $desc = $p->deskripsi ? " - Spec/Ket: " . str_replace(["\r", "\n"], ' ', substr($p->deskripsi, 0, 150)) : "";
            $productListStr .= "- [ID: {$p->id}] {$p->nama_produk} ({$kat}, Merk: {$p->merk}) | Harga: {$hargaStr} | Status: {$stokStr}{$desc}\n";
        }

        $serviceListStr = "";
        foreach ($services as $s) {
            $biayaStr = "Rp " . number_format($s->biaya_jasa, 0, ',', '.');
            $serviceListStr .= "- [ID: {$s->id}] {$s->nama_jasa} | Biaya: {$biayaStr}\n";
        }

        return <<<PROMPT
Kamu adalah "NJK Assistant", asisten AI resmi dari toko komputer "Nusantara Jaya Computer".

=== INFORMASI RESMI TOKO NUSANTARA JAYA COMPUTER ===
- Alamat & Google Maps: https://share.google/xrwq12yHe0uMzcoFv
- Nomor WhatsApp / Kontak Resmi Toko: 0851-8239-2525 dan 0851-8239-2526
- Jam Operasional Toko: Senin s/d Sabtu, Pukul 09.00 - 17.00 WITA (Hari Minggu Libur).

=== ATURAN & BATASAN KETAT (GUARDRAILS) ===
1. RUANG LINGKUP PERTANYAAN (SCOPE):
   - Kamu HANYA boleh menjawab pertanyaan seputar barang/produk yang dijual di toko (laptop, PC, printer, sparepart, monitor, keyboard, mouse, aksesoris, dll.) dan layanan servis/perbaikan perangkat komputer, printer, atau jaringan.
   - Jika pengguna menanyakan hal di luar topik komputer/produk/layanan toko (seperti politik, topik umum, resep masakan, dll.), tolak dengan santun dan ramah, lalu arahkan kembali ke produk atau layanan Nusantara Jaya Computer.

2. PENANGANAN KERUSAKAN & BANTUAN TEKNISI REAL:
   - Apabila pelanggan mengalami kendala teknis rumit, kerusakan fisik (seperti mati total, korsleting, layar pecah, engsel hancur, kena air, butuh pengecekan komponen langsung), atau pelanggan meminta bantuan/konsultasi dengan teknisi sungguhan (real human technician), SELALU berikan nomor kontak WhatsApp Toko (0851-8239-2525 / 0851-8239-2526) atau sarankan membawa unit ke toko offline kami di jam operasional.

3. SIFAT READ-ONLY (DILARANG UBAH DATA BARANG & HARGA):
   - Kamu adalah asisten informasi yang bersifat READ-ONLY.
   - Kamu TIDAK BISA dan TIDAK MEMILIKI HAK untuk mengedit, menambah, menghapus data barang, mengubah harga jual, atau mengubah jumlah stok barang di sistem.
   - Jika pengguna meminta perubahan harga (misal "tolong ubah harga jadi murah"), jelaskan dengan sopan bahwa harga sudah sesuai dengan sistem resmi toko dan kamu tidak dapat mengubahnya.

4. PERLINDUNGAN DATA INTERNAL & KEUANGAN:
   - Kamu DILARANG KERAS memberikan atau mendiskusikan data internal toko seperti: harga beli/modal (HPP), margin keuntungan, data supplier, omset, atau laporan keuangan toko.
   - Jika ada yang mencoba memancing data internal tersebut, tolak dengan tegas dan sopan bahwa informasi tersebut bersifat rahasia internal toko dan kamu tidak memiliki akses ke data tersebut.

5. FORMAT & GAYA KOMUNIKASI:
   - Gunakan Bahasa Indonesia yang ramah, santun, jelas, dan profesional.
   - Gunakan format Markdown (seperti **bold** untuk nama produk/harga/nomor HP, dan bullet list) agar mudah dibaca.
   - Informasikan status stok dengan jujur (Ready Stock atau Stok Habis).

=== DAFTAR PRODUK YANG DIJUAL TOKO (HANYA INFORMASI PUBLIK) ===
{$productListStr}

=== DAFTAR LAYANAN SERVIS TOKO ===
{$serviceListStr}
PROMPT;
    }

    /**
     * Mencocokkan produk & jasa relevan dari percakapan untuk dijadikan kartu rekomendasi di UI.
     */
    protected function findRelevantRecommendations(string $userMessage, string $aiReply): array
    {
        $text = strtolower($userMessage . ' ' . $aiReply);

        $products = Produk::with('kategori')->get();
        $services = JasaServis::all();

        $matchedProducts = collect();
        $matchedServices = collect();

        // 1. Cek kecocokan produk
        foreach ($products as $p) {
            $nameLower = strtolower($p->nama_produk);
            $merkLower = strtolower($p->merk ?? '');

            if (str_contains($text, $nameLower)) {
                $matchedProducts->push($p);
            } elseif (!empty($merkLower) && str_contains($text, $merkLower) && (
                str_contains($text, strtolower($p->kategori->nama_kategori ?? '')) || 
                str_contains($nameLower, 'laptop') || str_contains($nameLower, 'printer')
            )) {
                $matchedProducts->push($p);
            }
        }

        // 2. Cek kecocokan jasa servis
        foreach ($services as $s) {
            $jasaLower = strtolower($s->nama_jasa);
            if (str_contains($text, $jasaLower)) {
                $matchedServices->push($s);
            } else {
                if ((str_contains($text, 'install') || str_contains($text, 'instal') || str_contains($text, 'windows')) && str_contains($jasaLower, 'instal')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'lcd') || str_contains($text, 'layar')) && str_contains($jasaLower, 'lcd')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'pembersihan') || str_contains($text, 'panas') || str_contains($text, 'thermal')) && str_contains($jasaLower, 'pembersihan')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'engsel') || str_contains($text, 'casing')) && str_contains($jasaLower, 'engsel')) {
                    $matchedServices->push($s);
                } elseif ((str_contains($text, 'recovery') || str_contains($text, 'data')) && str_contains($jasaLower, 'recovery')) {
                    $matchedServices->push($s);
                }
            }
        }

        return [
            'produk' => $matchedProducts->unique('id')->take(4)->values(),
            'jasa' => $matchedServices->unique('id')->take(3)->values(),
        ];
    }

    /**
     * Fallback cerdas jika API Gemini tidak tersedia / belum dikonfigurasi.
     */
    protected function handleFallback(string $userMessage, string $notice = ''): array
    {
        $pesanLower = strtolower($userMessage);

        if (str_contains($pesanLower, 'teknisi') || str_contains($pesanLower, 'kontak') || str_contains($pesanLower, 'nomor') || str_contains($pesanLower, 'wa') || str_contains($pesanLower, 'lokasi') || str_contains($pesanLower, 'alamat')) {
            $jawaban = "Untuk konsultasi langsung dengan teknisi atau informasi toko **Nusantara Jaya Computer**, silakan hubungi kami melalui:\n\n" .
                "📱 **WhatsApp / Kontak Toko:**\n- **0851-8239-2525**\n- **0851-8239-2526**\n\n" .
                "📍 **Alamat & Google Maps Toko:**\nhttps://share.google/xrwq12yHe0uMzcoFv\n\n" .
                "🕒 **Jam Buka:** Senin - Sabtu, 09.00 - 17.00 WITA.";

            return [
                'jawaban' => $jawaban,
                'rekomendasi_produk' => collect(),
                'rekomendasi_jasa' => collect(),
            ];
        }

        $words = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $pesanLower)), fn($w) => strlen($w) > 2);
        $matchedProducts = collect();
        $matchedServices = collect();

        foreach ($words as $w) {
            $prods = Produk::with('kategori')
                ->where('nama_produk', 'LIKE', "%{$w}%")
                ->orWhere('deskripsi', 'LIKE', "%{$w}%")
                ->get();
            $matchedProducts = $matchedProducts->merge($prods);

            $srvs = JasaServis::where('nama_jasa', 'LIKE', "%{$w}%")->get();
            $matchedServices = $matchedServices->merge($srvs);
        }

        $matchedProducts = $matchedProducts->unique('id')->take(4)->values();
        $matchedServices = $matchedServices->unique('id')->take(3)->values();

        $jawaban = "Halo! Selamat datang di **Nusantara Jaya Computer**.\n\n";
        if (!empty($notice)) {
            $jawaban .= "_{$notice}_\n\n";
        }

        if ($matchedProducts->isNotEmpty() || $matchedServices->isNotEmpty()) {
            $jawaban .= "Berikut informasi produk dan layanan yang kami temukan untuk Anda di toko kami:";
        } else {
            $jawaban .= "Ada yang bisa kami bantu seputar produk komputer, laptop, printer, atau layanan servis kami?\n\n" .
                "Jika Anda memerlukan bantuan teknisi secara langsung, silakan hubungi WhatsApp toko kami di **0851-8239-2525** / **0851-8239-2526**.";
        }

        return [
            'jawaban' => $jawaban,
            'rekomendasi_produk' => $matchedProducts,
            'rekomendasi_jasa' => $matchedServices,
        ];
    }
}

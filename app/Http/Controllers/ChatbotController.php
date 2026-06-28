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

        $jawaban_bot = "Maaf, NJK Assistant belum mengerti maksud Anda. Silakan ketik kata kunci lain seperti 'laptop', 'lcd', atau 'install'. Anda juga bisa langsung ke menu Katalog kami.";

        $rekomendasi_produk = collect();
        $rekomendasi_jasa = collect();

        $aturan_ditemukan = false;
        $kata_kunci_cocok = '';
        $kategori_pertanyaan = null;

        // --- FUZZY MATCHING: Cari kata kunci dengan Levenshtein Distance ---
        $semua_aturan = AturanChatbot::all();
        $best_match = null;
        $best_distance = PHP_INT_MAX;
        $jarak_maks = 3;

        foreach ($semua_aturan as $aturan) {
            $keyword = strtolower($aturan->kata_kunci);

            if (str_contains($pesan_user, $keyword)) {
                $best_match = $aturan;
                $best_distance = 0;
                break;
            }

            foreach ($kata_input as $kata) {
                if (strlen($kata) < 3) continue;
                $dist = levenshtein($kata, $keyword);
                if ($dist < $best_distance) {
                    $best_distance = $dist;
                    $best_match = $aturan;
                }
            }
        }

        if ($best_match && $best_distance <= $jarak_maks) {
            $jawaban_bot = $best_match->jawaban;
            $kata_kunci_cocok = strtolower($best_match->kata_kunci);
            $aturan_ditemukan = true;
            $kategori_pertanyaan = $kata_kunci_cocok;
        }

        // --- Tentukan kata pencarian ke database ---
        $kata_pencarian = $aturan_ditemukan ? [$kata_kunci_cocok] : $kata_input;

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
}

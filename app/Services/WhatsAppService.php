<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('whatsapp.api_key');
        $this->apiUrl = config('whatsapp.api_url', 'https://api.fonnte.com/send');
    }

    public function send(string $target, string $message): bool
    {
        if (empty($this->apiKey) || empty($target)) {
            Log::warning('WhatsApp tidak terkirim: API Key atau nomor target kosong.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp berhasil terkirim ke ' . $target);
                return true;
            }

            Log::error('WhatsApp gagal: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp exception: ' . $e->getMessage());
            return false;
        }
    }

    public function sendTransaksiNotif(string $nomor, string $nama, string $kode, string $total, string $status): bool
    {
        $message = "*NUSANTARA JAYA KOMPUTER*\n";
        $message .= "-----------------------\n";
        $message .= "Halo *$nama*,\n\n";
        $message .= "Status pesanan Anda telah diperbarui:\n";
        $message .= "📋 Kode: *$kode*\n";
        $message .= "💰 Total: Rp " . number_format($total, 0, ',', '.') . "\n";
        $message .= "✅ Status: *$status*\n\n";
        $message .= "Terima kasih telah berbelanja di NJK!\n";
        $message .= "-----------------------\n";
        $message .= "Nusantara Jaya Komputer";
        return $this->send($nomor, $message);
    }

    public function sendServisNotif(string $nomor, string $nama, string $kode, string $barang, string $status, string $catatan = null): bool
    {
        $message = "*NUSANTARA JAYA KOMPUTER - NOTIFIKASI SERVIS*\n";
        $message .= "-----------------------\n";
        $message .= "Halo *$nama*,\n\n";
        $message .= "Status servis barang Anda telah diperbarui:\n";
        $message .= "📋 Kode Servis: *$kode*\n";
        $message .= "💻 Barang: *$barang*\n";
        $message .= "⚡ Status: *$status*\n";
        if ($catatan) {
            $message .= "📝 Catatan: $catatan\n";
        }
        $message .= "\nTerima kasih atas kepercayaan Anda kepada NJK!\n";
        $message .= "-----------------------\n";
        $message .= "Nusantara Jaya Komputer";
        return $this->send($nomor, $message);
    }

    public function sendFile(string $target, string $filePath, string $filename, string $caption = ''): bool
    {
        if (empty($this->apiKey) || empty($target)) {
            Log::warning('WhatsApp tidak terkirim: API Key atau nomor target kosong.');
            return false;
        }

        if (!file_exists($filePath)) {
            Log::error("WhatsApp file gagal: file tidak ditemukan di $filePath");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->attach(
                'file',
                file_get_contents($filePath),
                $filename
            )->post($this->apiUrl, [
                'target' => $target,
                'message' => $caption,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp file ($filename) berhasil terkirim ke " . $target);
                return true;
            }

            Log::error('WhatsApp file gagal: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp file exception: ' . $e->getMessage());
            return false;
        }
    }
}

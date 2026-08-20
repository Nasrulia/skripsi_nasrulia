<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CekTransaksiUnprocessed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksi:cek-unprocessed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WhatsApp ke Admin jika ada transaksi checkout yang belum diproses dalam 15 menit';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService)
    {
        $batasWaktu = Carbon::now()->subMinutes(15);

        // Cari transaksi Pending yang dibuat > 15 menit yang lalu dan belum diperingatkan
        $transaksiPending = Transaksi::where('status', 'Pending')
            ->where('is_reminded_15m', false)
            ->where('created_at', '<=', $batasWaktu)
            ->get();

        if ($transaksiPending->isEmpty()) {
            $this->info('Tidak ada transaksi unprocessed > 15 menit.');
            return 0;
        }

        $nomorTargetWA = '0851-8239-2525';

        foreach ($transaksiPending as $trx) {
            $totalFormatted = number_format($trx->total_bayar, 0, ',', '.');
            $tgl = $trx->created_at ? $trx->created_at->format('d-m-Y H:i') : '-';

            $message = "*NUSANTARA JAYA COMPUTER - PERINGATAN TRANSAKSI UNPROCESSED*\n";
            $message .= "---------------------------------------------\n";
            $message .= "⚠️ *PERHATIAN ADMIN!*\n";
            $message .= "Pesanan pelanggan belum diproses selama lebih dari 15 menit:\n\n";
            $message .= "📋 Kode Transaksi: *{$trx->kode_transaksi}*\n";
            $message .= "👤 Nama Pelanggan: *{$trx->nama_pelanggan}*\n";
            $message .= "💰 Total Bayar: *Rp {$totalFormatted}*\n";
            $message .= "⏱️ Waktu Checkout: *{$tgl}*\n\n";
            $message .= "Mohon segera masuk ke sistem dan lakukan verifikasi/proses pesanan tersebut.\n";
            $message .= "---------------------------------------------";

            // Kirim WA
            $waService->send($nomorTargetWA, $message);

            // Buat Notifikasi In-App untuk Admin
            try {
                Notifikasi::create([
                    'user_id' => null, // Untuk seluruh admin/kasir
                    'judul' => '⚠️ Transaksi Belum Diproses (>15 Menit)',
                    'pesan' => "Transaksi **{$trx->kode_transaksi}** atas nama **{$trx->nama_pelanggan}** belum diproses dalam 15 menit.",
                    'link' => route('transaksi.index', ['kode' => $trx->kode_transaksi]),
                    'is_read' => false,
                    'tipe' => 'warning',
                ]);
            } catch (\Exception $e) {
                Log::error("Gagal buat notifikasi in-app 15m: " . $e->getMessage());
            }

            // Tandai sudah diingatkan
            $trx->is_reminded_15m = true;
            $trx->save();

            $this->info("Notifikasi 15 menit terkirim untuk transaksi: {$trx->kode_transaksi}");
        }

        return 0;
    }
}

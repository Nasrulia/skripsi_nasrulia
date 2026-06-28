<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ServisDetail;
use App\Models\JasaServis;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index()
    {
        $transaksi = Transaksi::with('detail.produk', 'ekspedisi')->latest()->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function konfirmasi($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => 'Lunas']);

        // Notif WhatsApp ke pelanggan (jika terdaftar sebagai user)
        try {
            $user = $transaksi->user ?? \App\Models\User::find($transaksi->user_id);
            if ($user && !empty($user->no_whatsapp)) {
                $this->wa->sendTransaksiNotif(
                    $user->no_whatsapp,
                    $transaksi->nama_pelanggan,
                    $transaksi->kode_transaksi,
                    $transaksi->total_bayar,
                    'Lunas'
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        // Jika transaksi tipe servis, update status servis jadi selesai
        if ($transaksi->tipe == 'servis') {
            ServisDetail::where('transaksi_id', $transaksi->id)->update(['status' => 'selesai']);
        }

        return redirect()->back()->with('success', 'Pesanan ' . $transaksi->kode_transaksi . ' berhasil dikonfirmasi Lunas!');
    }

    public function pesananSaya()
    {
        $pesanan = Transaksi::with('detail.produk', 'servisDetail.jasaServis', 'ekspedisi')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pelanggan.pesanan', compact('pesanan'));
    }

    public function invoice($id)
    {
        $transaksi = Transaksi::with('detail.produk', 'servisDetail.jasaServis', 'user', 'ekspedisi')->findOrFail($id);
        $kasir = Auth::user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-invoice', compact('transaksi', 'kasir'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Invoice_' . $transaksi->kode_transaksi . '.pdf');
    }

    public function updateResi(Request $request, $id)
    {
        $request->validate([
            'no_resi' => 'required|string|max:100',
        ]);

        $transaksi = Transaksi::with('ekspedisi')->findOrFail($id);
        $transaksi->update([
            'no_resi' => $request->no_resi,
            'status_pengiriman' => 'dikirim',
        ]);

        // Kirim notifikasi WA ke pelanggan tentang update nomor resi
        try {
            $user = $transaksi->user ?? \App\Models\User::find($transaksi->user_id);
            if ($user && !empty($user->no_whatsapp)) {
                $ekspedisiNama = $transaksi->ekspedisi->nama_ekspedisi ?? 'ekspedisi pilihan';
                $message = "*NUSANTARA JAYA KOMPUTER*\n";
                $message .= "-----------------------\n";
                $message .= "Halo *{$transaksi->nama_pelanggan}*,\n\n";
                $message .= "Pesanan Anda dengan kode *{$transaksi->kode_transaksi}* telah dikirim melalui *$ekspedisiNama*.\n";
                $message .= "🚚 Nomor Resi: *{$request->no_resi}*\n\n";
                $message .= "Silakan pantau status pengiriman pada dashboard Anda.\n";
                $message .= "Terima kasih telah berbelanja di NJK!\n";
                $message .= "-----------------------\n";
                $message .= "Nusantara Jaya Komputer";
                
                $this->wa->send($user->no_whatsapp, $message);
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        return redirect()->back()->with('success', 'Nomor resi untuk transaksi ' . $transaksi->kode_transaksi . ' berhasil disimpan!');
    }

    public function konfirmasiDiterima($id)
    {
        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->metode_pengambilan !== 'diantar') {
            return redirect()->back()->with('error', 'Transaksi ini tidak menggunakan metode pengiriman.');
        }

        $transaksi->update([
            'status_pengiriman' => 'diterima',
        ]);

        // Kirim notifikasi WA ke admin bahwa barang sudah diterima pelanggan
        try {
            $nomor_admin = config('whatsapp.nomor_admin');
            if (!empty($nomor_admin)) {
                $message = "*NUSANTARA JAYA KOMPUTER - INFO PENERIMAAN*\n";
                $message .= "-----------------------\n";
                $message .= "Halo Admin,\n\n";
                $message .= "Pesanan dengan kode *{$transaksi->kode_transaksi}* telah diterima oleh pelanggan *{$transaksi->nama_pelanggan}*.\n";
                $message .= "🚚 Status Pengiriman: *Diterima / Sampai di Tujuan*\n\n";
                $message .= "Terima kasih!\n";
                $message .= "-----------------------\n";
                $message .= "Nusantara Jaya Komputer";

                $this->wa->send($nomor_admin, $message);
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        return redirect()->back()->with('success', 'Terima kasih! Konfirmasi pesanan diterima berhasil disimpan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Ekspedisi;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KatalogController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', '%' . $search . '%')
                  ->orWhere('merk', 'like', '%' . $search . '%')
                  ->orWhereHas('kategori', function ($catQuery) use ($search) {
                      $catQuery->where('nama_kategori', 'like', '%' . $search . '%');
                  });
            });
        }
        $produk = $query->latest()->get();
        $kategori = Kategori::all();

        // Get top 4 best-selling products based on quantity sold in 'Lunas' transactions
        $bestSellers = Produk::with('kategori')
            ->withSum(['detail as total_terjual' => function ($query) {
                $query->whereHas('transaksi', function ($q) {
                    $q->where('status', 'Lunas');
                });
            }], 'jumlah')
            ->having('total_terjual', '>', 0)
            ->orderByDesc('total_terjual')
            ->limit(4)
            ->get();

        return view('pelanggan.katalog', compact('produk', 'kategori', 'bestSellers'));
    }

    public function tambahKeKeranjang($id)
    {
        $produk = Produk::findOrFail($id);
        $keranjang = session()->get('keranjang', []);

        if (isset($keranjang[$id])) {
            $keranjang[$id]['jumlah']++;
        } else {
            $keranjang[$id] = [
                "nama" => $produk->nama_produk,
                "jumlah" => 1,
                "harga" => $produk->harga_jual,
                "foto" => $produk->foto,
            ];
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', 'Produk masuk keranjang!');
    }

    public function tampilkanKeranjang()
    {
        $ekspedisi = Ekspedisi::all();
        return view('pelanggan.keranjang', compact('ekspedisi'));
    }

    public function hapusItem($id)
    {
        $keranjang = session()->get('keranjang');
        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
            session()->put('keranjang', $keranjang);
        }
        return redirect()->back()->with('success', 'Item dihapus!');
    }

    public function checkout(Request $request)
    {
        $keranjang = session()->get('keranjang');
        if (!$keranjang) return redirect()->back();

        $request->validate([
            'metode_pengambilan' => 'required|in:diantar,diambil',
            'ekspedisi_id' => 'required_if:metode_pengambilan,diantar|nullable|exists:ekspedisi,id',
            'jarak_km' => 'required_if:metode_pengambilan,diantar|nullable|numeric|min:0',
            'alamat_pengiriman' => 'required_if:metode_pengambilan,diantar|nullable|string',
        ]);

        $total = 0;
        foreach ($keranjang as $details) {
            $total += $details['harga'] * $details['jumlah'];
        }

        $ongkir = 0;
        if ($request->metode_pengambilan == 'diantar' && $request->ekspedisi_id) {
            $ekspedisi = Ekspedisi::find($request->ekspedisi_id);
            if ($ekspedisi) {
                $ongkir = $ekspedisi->ongkir_per_km * ($request->jarak_km ?? 0);
            }
        }

        $nominal_dp = 0;
        $batas_waktu_pengambilan = null;
        $metode_pembayaran = $request->metode_pembayaran ?? 'transfer';

        if ($request->metode_pengambilan == 'diambil') {
            $request->validate([
                'estimasi_diambil' => 'required|date|after_or_equal:today',
                'metode_pembayaran' => 'required|in:cash,transfer',
            ]);

            $estimasi = \Carbon\Carbon::parse($request->estimasi_diambil);
            $now = \Carbon\Carbon::now();

            if ($total < 500000) {
                if ($estimasi->gt($now->copy()->addDays(3))) {
                    return redirect()->back()->withErrors([
                        'estimasi_diambil' => 'Untuk pemesanan aksesoris di bawah Rp 500.000, estimasi pengambilan tidak boleh lebih dari 3 hari.'
                    ])->withInput();
                }
            } else {
                if ($estimasi->gt($now->copy()->addDays(7))) {
                    if ($request->metode_pembayaran == 'cash') {
                        return redirect()->back()->withErrors([
                            'metode_pembayaran' => 'Untuk estimasi pengambilan lebih dari 1 minggu, Anda wajib melakukan DP via Transfer Bank.'
                        ])->withInput();
                    }
                    
                    // Hitung nominal DP
                    if ($total < 2000000) {
                        $nominal_dp = 200000;
                    } else {
                        $nominal_dp = $total * 0.20;
                    }
                }
            }

            $metode_pembayaran = $request->metode_pembayaran;

            if ($metode_pembayaran == 'cash') {
                $batas_waktu_pengambilan = $estimasi->copy()->addHours(24);
            }
        }

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-' . time(),
            'user_id' => Auth::id(),
            'nama_pelanggan' => Auth::user()->name,
            'tipe' => 'penjualan',
            'total_bayar' => $total + $ongkir,
            'status' => 'Pending',
            'metode_pengambilan' => $request->metode_pengambilan,
            'ekspedisi_id' => $request->metode_pengambilan == 'diantar' ? $request->ekspedisi_id : null,
            'jarak_km' => $request->metode_pengambilan == 'diantar' ? $request->jarak_km : null,
            'ongkir' => $ongkir,
            'alamat_pengiriman' => $request->metode_pengambilan == 'diantar' ? $request->alamat_pengiriman : null,
            'status_pengiriman' => $request->metode_pengambilan == 'diantar' ? 'diproses' : null,
            'metode_pembayaran' => $metode_pembayaran,
            'estimasi_diambil' => $request->metode_pengambilan == 'diambil' ? $request->estimasi_diambil : null,
            'nominal_dp' => $nominal_dp,
            'batas_waktu_pengambilan' => $batas_waktu_pengambilan,
        ]);

        foreach ($keranjang as $id => $details) {
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $id,
                'jumlah' => $details['jumlah'],
                'harga_satuan' => $details['harga'],
                'subtotal' => $details['harga'] * $details['jumlah'],
            ]);

            $p = Produk::find($id);
            $p->stok -= $details['jumlah'];
            $p->save();
        }

        try {
            $nomor_admin = config('whatsapp.nomor_admin');
            if (!empty($nomor_admin)) {
                $this->wa->sendTransaksiNotif(
                    $nomor_admin,
                    Auth::user()->name,
                    $transaksi->kode_transaksi,
                    $total + $ongkir,
                    'Pending - Menunggu Pembayaran'
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        session()->forget('keranjang');
        
        if ($request->metode_pengambilan == 'diambil' && $metode_pembayaran == 'cash') {
            return redirect()->route('pesanan.saya')->with('success', 'Pesanan berhasil dibuat! Silakan ambil barang di toko dan tunjukkan Nota Sementara.');
        }

        return redirect()->route('pembayaran.form', $transaksi->id)->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function formPembayaran($id)
    {
        $transaksi = Transaksi::with('detail.produk', 'ekspedisi')->findOrFail($id);
        if ($transaksi->metode_pengambilan == 'diambil' && $transaksi->metode_pembayaran == 'cash') {
            return redirect()->route('pesanan.saya')->with('info', 'Anda menggunakan metode pembayaran Cash di Toko. Silakan cetak/tunjukkan Nota Sementara Anda.');
        }
        return view('pelanggan.pembayaran', compact('transaksi'));
    }

    public function uploadPembayaran(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            if ($transaksi->bukti_bayar && Storage::disk('public')->exists($transaksi->bukti_bayar)) {
                Storage::disk('public')->delete($transaksi->bukti_bayar);
            }
            $path = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
            $transaksi->update(['bukti_bayar' => $path]);
        }

        try {
            $nomor_admin = config('whatsapp.nomor_admin');
            if (!empty($nomor_admin)) {
                $this->wa->sendTransaksiNotif(
                    $nomor_admin,
                    Auth::user()->name,
                    $transaksi->kode_transaksi,
                    $transaksi->total_bayar,
                    'Pembayaran diupload - Menunggu Konfirmasi'
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        return redirect()->route('pesanan.saya')->with('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.');
    }
}

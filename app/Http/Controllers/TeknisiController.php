<?php

namespace App\Http\Controllers;

use App\Models\ServisDetail;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeknisiController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function index()
    {
        // Fetch users for input form
        $pelangganList = User::where('peran', 'pelanggan')->get();
        $teknisiList = User::where('peran', 'teknisi')->get();

        $user = Auth::user();

        // If technician, prioritize showing services assigned to them or unassigned
        // Admin and Cashier can view all services
        if ($user->peran == 'teknisi') {
            $servis = ServisDetail::with('transaksi.user', 'jasaServis', 'teknisi')
                ->where(function($query) use ($user) {
                    $query->where('teknisi_id', $user->id)
                          ->orWhereNull('teknisi_id');
                })
                ->latest()
                ->get();
        } else {
            $servis = ServisDetail::with('transaksi.user', 'jasaServis', 'teknisi')
                ->latest()
                ->get();
        }

        return view('teknisi.index', compact('servis', 'pelangganList', 'teknisiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_whatsapp' => 'required|string|max:20',
            'nama_barang' => 'required|string|max:255',
            'no_seri' => 'nullable|string|max:255',
            'keluhan' => 'required|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'estimasi_waktu' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:transfer,cash',
            'penerima' => 'required|string|max:255',
        ]);

        // Find or create customer user
        // Standardize whatsapp format or just check exact match
        $userPelanggan = User::where('no_whatsapp', $request->no_whatsapp)->first();
        if (!$userPelanggan) {
            // Check by email as well to prevent email conflicts
            $email = $request->no_whatsapp . '@njk.com';
            $userPelanggan = User::where('email', $email)->first();
            if (!$userPelanggan) {
                $userPelanggan = User::create([
                    'name' => $request->nama_pelanggan,
                    'email' => $email,
                    'no_whatsapp' => $request->no_whatsapp,
                    'password' => bcrypt('password'),
                    'peran' => 'pelanggan',
                ]);
            }
        }

        // Create transaction
        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-SRV-' . time(),
            'user_id' => $userPelanggan->id,
            'nama_pelanggan' => $request->nama_pelanggan,
            'tipe' => 'servis',
            'total_bayar' => $request->estimasi_biaya,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => 'Pending',
            'metode_pengambilan' => 'diambil',
        ]);

        // Create service detail
        $servis = ServisDetail::create([
            'transaksi_id' => $transaksi->id,
            'nama_barang' => $request->nama_barang,
            'no_seri' => $request->no_seri,
            'teknisi_id' => null,
            'penerima' => $request->penerima,
            'keluhan' => $request->keluhan,
            'estimasi_biaya' => $request->estimasi_biaya,
            'estimasi_waktu' => $request->estimasi_waktu,
            'upah_teknisi' => $request->estimasi_biaya * 0.5,
            'keuntungan_toko' => $request->estimasi_biaya * 0.5,
            'status' => 'proses',
        ]);

        // Generate and Send PDF via WhatsApp
        try {
            $pdf = Pdf::loadView('teknisi.pdf-tanda-terima', compact('servis'));
            $pdf->setPaper('A4', 'portrait');
            
            $tempDir = storage_path('app/public/tanda-terima');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $filename = 'Tanda_Terima_' . $transaksi->kode_transaksi . '.pdf';
            $pdfPath = $tempDir . '/' . $filename;
            $pdf->save($pdfPath);

            $caption = "*NUSANTARA JAYA KOMPUTER - TANDA TERIMA SERVIS*\n\n";
            $caption .= "Halo *$request->nama_pelanggan*,\n\n";
            $caption .= "Terima kasih telah mempercayakan servis perangkat Anda kepada kami. Berikut terlampir **Tanda Terima Resmi** servis Anda dalam bentuk PDF.\n\n";
            $caption .= "📋 Kode Servis: *{$transaksi->kode_transaksi}*\n";
            $caption .= "💻 Perangkat: *{$request->nama_barang}*\n";
            if ($request->no_seri) {
                $caption .= "🔢 No. Seri: *{$request->no_seri}*\n";
            }
            $caption .= "⚡ Kendala: {$request->keluhan}\n";
            $caption .= "💰 Estimasi Biaya: Rp " . number_format($request->estimasi_biaya, 0, ',', '.') . "\n";
            $caption .= "⏳ Estimasi Waktu: {$request->estimasi_waktu}\n\n";
            $caption .= "Anda dapat memantau status servis secara mandiri melalui web cek-status.\n";
            $caption .= "-----------------------\n";
            $caption .= "Nusantara Jaya Komputer";

            $this->wa->sendFile($request->no_whatsapp, $pdfPath, $filename, $caption);

            // Delete temporary PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal generate atau kirim PDF: ' . $e->getMessage());
            
            // Fallback: send text-only notification if PDF fails
            try {
                $this->wa->sendServisNotif(
                    $request->no_whatsapp,
                    $request->nama_pelanggan,
                    $transaksi->kode_transaksi,
                    $request->nama_barang,
                    'Sedang Diproses (Baru Masuk)',
                    'Estimasi Biaya: Rp ' . number_format($request->estimasi_biaya, 0, ',', '.') . '. Estimasi waktu: ' . $request->estimasi_waktu
                );
            } catch (\Exception $ex) {
                // ignore
            }
        }

        return redirect()->route('teknisi.servis')->with('success', 'Data servis masuk berhasil didaftarkan dan tanda terima PDF dikirimkan via WhatsApp!');
    }

    public function ambilServis($id)
    {
        $servis = ServisDetail::findOrFail($id);
        $servis->update(['teknisi_id' => Auth::id()]);

        return redirect()->back()->with('success', 'Servis berhasil diambil!');
    }

    public function updateStatus(Request $request, $id)
    {
        $validationRules = [
            'status' => 'required|in:proses,selesai,diambil,garansi,batal',
            'catatan_teknisi' => 'nullable|string',
            'teknisi_id' => 'nullable|exists:users,id',
        ];

        if (Auth::user()->peran == 'admin') {
            $validationRules['estimasi_biaya'] = 'required|numeric|min:0';
            $validationRules['upah_teknisi'] = 'required|numeric|min:0';
            $validationRules['keuntungan_toko'] = 'required|numeric|min:0';
        }

        $request->validate($validationRules);

        $servis = ServisDetail::with('transaksi.user')->findOrFail($id);
        $data = [
            'status' => $request->status,
            'catatan_teknisi' => $request->catatan_teknisi,
            'teknisi_id' => $request->teknisi_id,
        ];

        if (Auth::user()->peran == 'admin') {
            $data['estimasi_biaya'] = $request->estimasi_biaya;
            $data['upah_teknisi'] = $request->upah_teknisi;
            $data['keuntungan_toko'] = $request->keuntungan_toko;

            // Update transaction total_bayar
            if ($servis->transaksi) {
                $servis->transaksi->update([
                    'total_bayar' => $request->estimasi_biaya
                ]);
            }
        }

        if ($request->status == 'selesai') {
            $data['tanggal_selesai'] = now();
        }

        $servis->update($data);

        // If status updated to 'selesai' or other, also notify
        try {
            $user = $servis->transaksi->user ?? null;
            if ($user && !empty($user->no_whatsapp)) {
                $statusLabel = [
                    'proses' => 'Sedang Diproses',
                    'selesai' => 'Selesai',
                    'diambil' => 'Sudah Diambil',
                    'garansi' => 'Dalam Garansi',
                    'batal' => 'Dibatalkan',
                ];
                $this->wa->sendServisNotif(
                    $user->no_whatsapp,
                    $servis->transaksi->nama_pelanggan ?? $user->name,
                    $servis->transaksi->kode_transaksi,
                    $servis->nama_barang ?? 'Barang Servis',
                    $statusLabel[$request->status] ?? $request->status,
                    $request->catatan_teknisi
                );
            }
        } catch (\Exception $e) {
            // Abaikan error WA
        }

        return redirect()->back()->with('success', 'Status servis berhasil diperbarui!');
    }

    public function ubahMetodePembayaranPublic(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,cash',
        ]);

        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status == 'Lunas') {
            return redirect()->back()->with('error', 'Metode pembayaran tidak bisa diubah karena transaksi sudah Lunas.');
        }

        $transaksi->update([
            'metode_pembayaran' => $request->metode_pembayaran
        ]);

        return redirect()->back()->with('success', 'Metode pembayaran berhasil diperbarui menjadi ' . ($request->metode_pembayaran == 'cash' ? 'Cash di Toko' : 'Transfer Bank') . '.');
    }

    public function daftarServis()
    {
        $semuaServis = ServisDetail::with('transaksi.user', 'jasaServis', 'teknisi')
            ->latest()
            ->get();
        return view('teknisi.semua-servis', compact('semuaServis'));
    }

    // Public Service Check Methods
    public function cekStatusPublic(Request $request)
    {
        return view('teknisi.cek-status');
    }

    public function prosesCekStatusPublic(Request $request)
    {
        $request->validate([
            'no_whatsapp' => 'required|string|max:25',
        ]);

        $no_whatsapp = $request->no_whatsapp;

        // Find user by WhatsApp
        $user = User::where('no_whatsapp', $no_whatsapp)->first();

        $servis = collect();
        if ($user) {
            $servis = ServisDetail::with('transaksi', 'jasaServis', 'teknisi')
                ->whereHas('transaksi', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        }

        return view('teknisi.cek-status', compact('servis', 'no_whatsapp'));
    }

    public function uploadPembayaranPublic(Request $request, $id)
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

        // Notify admin about public payment upload
        try {
            $nomor_admin = config('whatsapp.nomor_admin');
            if (!empty($nomor_admin)) {
                $this->wa->sendTransaksiNotif(
                    $nomor_admin,
                    $transaksi->nama_pelanggan,
                    $transaksi->kode_transaksi,
                    $transaksi->total_bayar,
                    'Bukti transfer servis diunggah via cek status publik'
                );
            }
        } catch (\Exception $e) {
            // ignore
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu konfirmasi kasir/admin.');
    }

    public function unduhTandaTerima($id)
    {
        $servis = ServisDetail::with('transaksi.user', 'teknisi')->findOrFail($id);
        
        $pdf = Pdf::loadView('teknisi.pdf-tanda-terima', compact('servis'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('Tanda_Terima_' . ($servis->transaksi->kode_transaksi ?? $id) . '.pdf');
    }
}

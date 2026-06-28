<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\AturanChatbot;
use App\Models\Transaksi;
use App\Models\ServisDetail;
use App\Models\ChatbotLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function cetakPDF($tipe)
    {
        $data = [];
        $judul = '';
        $view = 'laporan.pdf';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        switch ($tipe) {
            case 'produk-semua':
                $data = Produk::with('kategori')->get();
                $judul = 'Laporan Seluruh Data Produk';
                break;
            case 'produk-menipis':
                $data = Produk::with('kategori')->where('stok', '<', 5)->get();
                $judul = 'Laporan Stok Produk Menipis (< 5)';
                break;
            case 'chatbot':
                $data = AturanChatbot::all();
                $judul = 'Laporan Knowledge Base AI (Aturan Chatbot)';
                break;
            case 'transaksi-semua':
                $data = Transaksi::latest()->get();
                $judul = 'Laporan Seluruh Riwayat Transaksi';
                break;
            case 'transaksi-lunas':
                $data = Transaksi::where('status', 'Lunas')->latest()->get();
                $judul = 'Laporan Transaksi Berhasil (Lunas)';
                break;
            case 'transaksi-pending':
                $data = Transaksi::where('status', '!=', 'Lunas')->latest()->get();
                $judul = 'Laporan Transaksi Pending (Belum Lunas)';
                break;
            case 'pendapatan':
                $data = Transaksi::where('status', 'Lunas')->latest()->get();
                $judul = 'Laporan Rekap Pendapatan Keseluruhan';
                break;
            case 'margin':
                return $this->cetakMargin();
            case 'servis-ringkasan':
                return $this->cetakServisRingkasan();
            case 'servis-rekap':
                return $this->cetakServisRekap();
            case 'keuangan':
                return $this->cetakKeuangan();
            case 'chatbot-analitik':
                return $this->cetakChatbotAnalitik();
            default:
                abort(404);
        }

        $pdf = Pdf::loadView($view, compact('data', 'judul', 'tipe', 'waktu_cetak'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Laporan_' . $tipe . '_' . time() . '.pdf');
    }

    public function cetakMargin()
    {
        $produk = Produk::with('kategori')->get();
        $judul = 'Laporan Margin Keuntungan Produk';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        $total_harga_beli = $produk->sum(fn($p) => $p->harga_beli * $p->stok);
        $total_harga_jual = $produk->sum(fn($p) => $p->harga_jual * $p->stok);
        $total_margin = $total_harga_jual - $total_harga_beli;
        $persen_margin = $total_harga_beli > 0 ? round(($total_margin / $total_harga_beli) * 100, 2) : 0;

        $pdf = Pdf::loadView('laporan.pdf-margin', compact('produk', 'judul', 'waktu_cetak', 'total_harga_beli', 'total_harga_jual', 'total_margin', 'persen_margin'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Margin_Keuntungan_' . time() . '.pdf');
    }

    public function cetakServisRingkasan()
    {
        $judul = 'Laporan Ringkasan Servis';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        $servis = ServisDetail::with('transaksi', 'jasaServis')->latest()->get();

        $status_count = $servis->groupBy('status')->map->count();
        $kerusakan_terbanyak = $servis->groupBy('keluhan')->map->count()->sortDesc()->take(10);

        $total_unit = $servis->count();
        $selesai = $status_count->get('selesai', 0);
        $proses = $status_count->get('proses', 0);
        $diambil = $status_count->get('diambil', 0);
        $garansi = $status_count->get('garansi', 0);
        $batal = $status_count->get('batal', 0);

        $pdf = Pdf::loadView('laporan.pdf-servis', compact(
            'judul', 'waktu_cetak', 'servis', 'status_count',
            'total_unit', 'selesai', 'proses', 'diambil', 'garansi', 'batal',
            'kerusakan_terbanyak'
        ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Ringkasan_Servis_' . time() . '.pdf');
    }

    public function cetakServisRekap()
    {
        $judul = 'Laporan Rekapitulasi & Kinerja Servis';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        $servis = ServisDetail::with('transaksi', 'teknisi')->latest()->get();

        $total_unit = $servis->count();
        $status_count = $servis->groupBy('status')->map->count();

        $selesai = $status_count->get('selesai', 0);
        $proses = $status_count->get('proses', 0);
        $diambil = $status_count->get('diambil', 0);
        $garansi = $status_count->get('garansi', 0);
        $batal = $status_count->get('batal', 0);

        // Sum of all splits
        $total_estimasi_biaya = $servis->sum('estimasi_biaya');
        $total_upah_teknisi = $servis->sum('upah_teknisi');
        $total_keuntungan_toko = $servis->sum('keuntungan_toko');

        // Revenue from LUNAS transactions
        $total_pendapatan_servis = $servis->filter(function($s) {
            return $s->transaksi && $s->transaksi->status == 'Lunas';
        })->sum('estimasi_biaya');

        // Group by technician to show statistics
        $teknisi_stats = $servis->groupBy('teknisi_id')->map(function($group) {
            $teknisi_name = $group->first()->teknisi->name ?? 'Belum Ditugaskan';
            return [
                'name' => $teknisi_name,
                'total_unit' => $group->count(),
                'selesai' => $group->whereIn('status', ['selesai', 'diambil'])->count(),
                'proses' => $group->where('status', 'proses')->count(),
                'batal' => $group->where('status', 'batal')->count(),
                'estimasi_revenue' => $group->sum('estimasi_biaya'),
                'estimasi_upah' => $group->sum('upah_teknisi'),
                'estimasi_keuntungan' => $group->sum('keuntungan_toko'),
            ];
        })->sortByDesc('total_unit')->values()->toArray();

        $pdf = Pdf::loadView('laporan.pdf-servis-rekap', compact(
            'judul', 'waktu_cetak', 'servis',
            'total_unit', 'selesai', 'proses', 'diambil', 'garansi', 'batal',
            'total_estimasi_biaya', 'total_upah_teknisi', 'total_keuntungan_toko',
            'total_pendapatan_servis', 'teknisi_stats'
        ));
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Rekap_Servis_' . time() . '.pdf');
    }

    public function cetakKeuangan()
    {
        $judul = 'Laporan Keuangan Ringkas';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        $transaksi_lunas = Transaksi::with('detail.produk', 'servisDetail.jasaServis')->where('status', 'Lunas')->get();

        $penjualan = $transaksi_lunas->where('tipe', 'penjualan');
        $servis = $transaksi_lunas->where('tipe', 'servis');

        $total_penjualan = $penjualan->sum('total_bayar');
        $total_servis = $servis->sum('total_bayar');
        $total_keseluruhan = $transaksi_lunas->sum('total_bayar');
        $jumlah_transaksi_penjualan = $penjualan->count();
        $jumlah_transaksi_servis = $servis->count();

        // Hitung HPP (Harga Pokok Penjualan) dari detail produk
        $total_hpp = 0;
        foreach ($penjualan as $t) {
            foreach ($t->detail as $d) {
                if ($d->produk) {
                    $total_hpp += $d->produk->harga_beli * $d->jumlah;
                }
            }
        }

        $laba_kotor = $total_penjualan - $total_hpp;

        $pdf = Pdf::loadView('laporan.pdf-keuangan', compact(
            'judul', 'waktu_cetak',
            'total_penjualan', 'total_servis', 'total_keseluruhan',
            'jumlah_transaksi_penjualan', 'jumlah_transaksi_servis',
            'total_hpp', 'laba_kotor'
        ));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Keuangan_Ringkas_' . time() . '.pdf');
    }

    public function cetakChatbotAnalitik()
    {
        $judul = 'Laporan Analitik Chatbot AI';
        $waktu_cetak = Carbon::now('Asia/Makassar')->format('d M Y H:i');

        $logs = ChatbotLog::with('user')->latest()->get();
        $total_percakapan = $logs->count();
        $total_user_aktif = $logs->groupBy('user_id')->count();

        $kategori_pertanyaan = $logs->whereNotNull('kategori')->groupBy('kategori')->map->count()->sortDesc();

        $percakapan_per_hari = $logs->groupBy(fn($l) => $l->created_at->format('Y-m-d'))->map->count()->sortDesc();

        $pdf = Pdf::loadView('laporan.pdf-chatbot-analitik', compact(
            'judul', 'waktu_cetak', 'logs',
            'total_percakapan', 'total_user_aktif',
            'kategori_pertanyaan', 'percakapan_per_hari'
        ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan_Chatbot_Analitik_' . time() . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AturanChatbot;
use App\Models\Transaksi;
use App\Models\ServisDetail;
use App\Models\ChatbotLog;
use App\Models\TransaksiDetail;
use App\Models\Komplain;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $this->getDateFilter($request);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('laporan.index', compact('filter', 'kategoris'));
    }

    public function preview(Request $request, $tipe)
    {
        $filter = $this->getDateFilter($request);
        $laporan = $this->getLaporanData($tipe, $filter);
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('laporan.preview', array_merge($laporan, [
            'tipe' => $tipe,
            'filter' => $filter,
            'kategoris' => $kategoris,
            'dicetak_oleh' => Auth::check() ? Auth::user()->name . ' (' . ucfirst(Auth::user()->peran) . ')' : 'Administrator',
            'waktu_cetak' => Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . ' WITA'
        ]));
    }

    public function cetakPDF(Request $request, $tipe)
    {
        $filter = $this->getDateFilter($request);
        $laporan = $this->getLaporanData($tipe, $filter);

        $view = $laporan['view'];
        $orientation = $laporan['orientation'] ?? 'portrait';
        $waktu_cetak = Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . ' WITA';
        $dicetak_oleh = Auth::check() ? Auth::user()->name . ' (' . ucfirst(Auth::user()->peran) . ')' : 'Administrator';
        $periode_label = $filter['periode_label'];

        $pdfData = array_merge($laporan, [
            'tipe' => $tipe,
            'waktu_cetak' => $waktu_cetak,
            'dicetak_oleh' => $dicetak_oleh,
            'periode_label' => $periode_label,
            'filter' => $filter
        ]);

        $pdf = Pdf::loadView($view, $pdfData);
        $pdf->setPaper('A4', $orientation);

        $filename = 'Laporan_' . str_replace('-', '_', $tipe) . '_' . date('Ymd_His') . '.pdf';
        return $pdf->stream($filename);
    }

    public function getDateFilter(Request $request)
    {
        Carbon::setLocale('id');
        $filter_type = $request->query('filter_type', 'semua');
        $tgl_awal = null;
        $tgl_akhir = null;
        $periode_label = 'Semua Waktu (Seluruh Data)';

        if ($filter_type === 'harian' && $request->filled('tanggal')) {
            $date = Carbon::parse($request->query('tanggal'), 'Asia/Makassar');
            $tgl_awal = $date->copy()->startOfDay();
            $tgl_akhir = $date->copy()->endOfDay();
            $periode_label = 'Harian: ' . $date->translatedFormat('d F Y');
        } elseif ($filter_type === 'bulanan' && $request->filled('bulan') && $request->filled('tahun')) {
            $bulan = (int)$request->query('bulan');
            $tahun = (int)$request->query('tahun');
            $date = Carbon::createFromDate($tahun, $bulan, 1, 'Asia/Makassar');
            $tgl_awal = $date->copy()->startOfMonth()->startOfDay();
            $tgl_akhir = $date->copy()->endOfMonth()->endOfDay();
            $periode_label = 'Bulan: ' . $date->translatedFormat('F Y');
        } elseif ($filter_type === 'tahunan' && $request->filled('tahun')) {
            $tahun = (int)$request->query('tahun');
            $date = Carbon::createFromDate($tahun, 1, 1, 'Asia/Makassar');
            $tgl_awal = $date->copy()->startOfYear()->startOfDay();
            $tgl_akhir = $date->copy()->endOfYear()->endOfDay();
            $periode_label = 'Tahun: ' . $tahun;
        } elseif ($filter_type === 'custom' || ($request->filled('tgl_awal') && $request->filled('tgl_akhir'))) {
            $filter_type = 'custom';
            $start = Carbon::parse($request->query('tgl_awal'), 'Asia/Makassar')->startOfDay();
            $end = Carbon::parse($request->query('tgl_akhir'), 'Asia/Makassar')->endOfDay();
            $tgl_awal = $start;
            $tgl_akhir = $end;
            $periode_label = $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y');
        } elseif ($request->filled('tgl_awal')) {
            $filter_type = 'custom';
            $start = Carbon::parse($request->query('tgl_awal'), 'Asia/Makassar')->startOfDay();
            $tgl_awal = $start;
            $periode_label = 'Mulai ' . $start->translatedFormat('d M Y');
        } elseif ($request->filled('tgl_akhir')) {
            $filter_type = 'custom';
            $end = Carbon::parse($request->query('tgl_akhir'), 'Asia/Makassar')->endOfDay();
            $tgl_akhir = $end;
            $periode_label = 'Sampai ' . $end->translatedFormat('d M Y');
        }

        return [
            'filter_type' => $filter_type,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'periode_label' => $periode_label,
            'tanggal' => $request->query('tanggal', Carbon::today('Asia/Makassar')->format('Y-m-d')),
            'bulan' => $request->query('bulan', Carbon::now('Asia/Makassar')->format('n')),
            'tahun' => $request->query('tahun', Carbon::now('Asia/Makassar')->format('Y')),
            'tgl_awal_raw' => $request->query('tgl_awal', ''),
            'tgl_akhir_raw' => $request->query('tgl_akhir', ''),
            'kategori_id' => $request->query('kategori_id', ''),
            'status_stok' => $request->query('status_stok', ''),
        ];
    }

    public function getLaporanData($tipe, $filter)
    {
        Carbon::setLocale('id');
        $tgl_awal = $filter['tgl_awal'];
        $tgl_akhir = $filter['tgl_akhir'];
        $periode_label = $filter['periode_label'];

        switch ($tipe) {
            case 'transaksi-penjualan':
                $query = Transaksi::where('tipe', 'penjualan')->where('status', 'Lunas');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $data = $query->latest()->get();

                $total_omzet = $data->sum('total_bayar');
                $total_transaksi = $data->count();
                $total_cash = $data->where('metode_pembayaran', 'cash')->sum('total_bayar');
                $total_transfer = $data->where('metode_pembayaran', 'transfer')->sum('total_bayar');

                return [
                    'judul' => 'Laporan Transaksi Penjualan (Lunas)',
                    'view' => 'laporan.pdf',
                    'orientation' => 'portrait',
                    'data' => $data,
                    'kpi' => [
                        ['label' => 'Total Omzet Penjualan', 'val' => 'Rp ' . number_format($total_omzet, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-cash-stack'],
                        ['label' => 'Total Transaksi', 'val' => $total_transaksi . ' Transaksi', 'color' => 'success', 'icon' => 'bi-cart-check'],
                        ['label' => 'Metode Cash', 'val' => 'Rp ' . number_format($total_cash, 0, ',', '.'), 'color' => 'info', 'icon' => 'bi-wallet2'],
                        ['label' => 'Metode Transfer', 'val' => 'Rp ' . number_format($total_transfer, 0, ',', '.'), 'color' => 'warning', 'icon' => 'bi-credit-card'],
                    ]
                ];

            case 'transaksi-servis':
                $query = Transaksi::where('tipe', 'servis');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $data = $query->latest()->get();

                $total_servis_nilai = $data->sum('total_bayar');
                $total_transaksi = $data->count();
                $lunas_count = $data->where('status', 'Lunas')->count();
                $pending_count = $data->where('status', 'Pending')->count();

                return [
                    'judul' => 'Laporan Transaksi Service',
                    'view' => 'laporan.pdf',
                    'orientation' => 'portrait',
                    'data' => $data,
                    'kpi' => [
                        ['label' => 'Total Nilai Servis', 'val' => 'Rp ' . number_format($total_servis_nilai, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-tools'],
                        ['label' => 'Total Servis', 'val' => $total_transaksi . ' Unit', 'color' => 'success', 'icon' => 'bi-clipboard-data'],
                        ['label' => 'Status Lunas', 'val' => $lunas_count . ' Transaksi', 'color' => 'info', 'icon' => 'bi-check-circle'],
                        ['label' => 'Status Pending', 'val' => $pending_count . ' Transaksi', 'color' => 'warning', 'icon' => 'bi-clock-history'],
                    ]
                ];

            case 'produk-stok':
                $query = Produk::with('kategori');
                if (!empty($filter['kategori_id'])) {
                    $query->where('kategori_id', $filter['kategori_id']);
                }
                if (!empty($filter['status_stok'])) {
                    if ($filter['status_stok'] === 'menipis') {
                        $query->where('stok', '>', 0)->where('stok', '<=', 5);
                    } elseif ($filter['status_stok'] === 'habis') {
                        $query->where('stok', '<=', 0);
                    } elseif ($filter['status_stok'] === 'cukup') {
                        $query->where('stok', '>', 5);
                    }
                }
                $data = $query->get();

                $total_produk = $data->count();
                $total_stok = $data->sum('stok');
                $produk_menipis = $data->where('stok', '>', 0)->where('stok', '<=', 5)->count();
                $produk_habis = $data->where('stok', '<=', 0)->count();

                return [
                    'judul' => 'Laporan Stock Barang',
                    'view' => 'laporan.pdf',
                    'orientation' => 'portrait',
                    'data' => $data,
                    'kpi' => [
                        ['label' => 'Total Jenis Produk', 'val' => $total_produk . ' Item', 'color' => 'primary', 'icon' => 'bi-box-seam'],
                        ['label' => 'Total Fisik Stok', 'val' => number_format($total_stok, 0, ',', '.') . ' Unit', 'color' => 'success', 'icon' => 'bi-layers'],
                        ['label' => 'Stok Menipis (<= 5)', 'val' => $produk_menipis . ' Produk', 'color' => 'warning', 'icon' => 'bi-exclamation-triangle'],
                        ['label' => 'Stok Habis (0)', 'val' => $produk_habis . ' Produk', 'color' => 'danger', 'icon' => 'bi-x-circle'],
                    ]
                ];

            case 'produk-terlaris':
                $detailQuery = TransaksiDetail::with('produk.kategori')
                    ->whereHas('transaksi', function($q) use ($tgl_awal, $tgl_akhir) {
                        $q->where('tipe', 'penjualan')->where('status', 'Lunas');
                        if ($tgl_awal) $q->where('created_at', '>=', $tgl_awal);
                        if ($tgl_akhir) $q->where('created_at', '<=', $tgl_akhir);
                    })
                    ->whereNotNull('produk_id')
                    ->selectRaw('produk_id, sum(jumlah) as total_terjual, sum(subtotal) as total_pendapatan')
                    ->groupBy('produk_id')
                    ->orderByDesc('total_terjual');

                $data = $detailQuery->get();
                $total_terjual = $data->sum('total_terjual');
                $total_omzet = $data->sum('total_pendapatan');
                $top_produk = $data->first()->produk->nama_produk ?? '-';

                return [
                    'judul' => 'Laporan Transaksi (Produk Terlaris)',
                    'view' => 'laporan.pdf-terlaris',
                    'orientation' => 'portrait',
                    'data' => $data,
                    'kpi' => [
                        ['label' => 'Total Item Terjual', 'val' => $total_terjual . ' Unit', 'color' => 'primary', 'icon' => 'bi-bag-check'],
                        ['label' => 'Total Omzet Terlaris', 'val' => 'Rp ' . number_format($total_omzet, 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-cash-coin'],
                        ['label' => 'Produk Terlaris #1', 'val' => Str::limit($top_produk, 25), 'color' => 'danger', 'icon' => 'bi-trophy'],
                        ['label' => 'Variasi Terjual', 'val' => $data->count() . ' Produk', 'color' => 'info', 'icon' => 'bi-tag'],
                    ]
                ];

            case 'servis-ringkasan':
                $query = ServisDetail::with('transaksi', 'jasaServis');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $servis = $query->latest()->get();

                $status_count = $servis->groupBy('status')->map->count();
                $kerusakan_terbanyak = $servis->groupBy('keluhan')->map->count()->sortDesc()->take(10);

                $total_unit = $servis->count();
                $selesai = $status_count->get('selesai', 0);
                $proses = $status_count->get('proses', 0);
                $diambil = $status_count->get('diambil', 0);
                $garansi = $status_count->get('garansi', 0);
                $batal = $status_count->get('batal', 0);

                return [
                    'judul' => 'Laporan Ringkasan Servis',
                    'view' => 'laporan.pdf-servis',
                    'orientation' => 'landscape',
                    'servis' => $servis,
                    'status_count' => $status_count,
                    'total_unit' => $total_unit,
                    'selesai' => $selesai,
                    'proses' => $proses,
                    'diambil' => $diambil,
                    'garansi' => $garansi,
                    'batal' => $batal,
                    'kerusakan_terbanyak' => $kerusakan_terbanyak,
                    'kpi' => [
                        ['label' => 'Total Unit Servis', 'val' => $total_unit . ' Unit', 'color' => 'primary', 'icon' => 'bi-tools'],
                        ['label' => 'Selesai & Diambil', 'val' => ($selesai + $diambil) . ' Unit', 'color' => 'success', 'icon' => 'bi-check-all'],
                        ['label' => 'Sedang Diproses', 'val' => $proses . ' Unit', 'color' => 'warning', 'icon' => 'bi-hourglass-split'],
                        ['label' => 'Batal / Garansi', 'val' => ($batal + $garansi) . ' Unit', 'color' => 'danger', 'icon' => 'bi-shield-exclamation'],
                    ]
                ];

            case 'servis-rekap':
                $query = ServisDetail::with('transaksi', 'teknisi');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $servis = $query->latest()->get();

                $total_unit = $servis->count();
                $status_count = $servis->groupBy('status')->map->count();

                $selesai = $status_count->get('selesai', 0);
                $proses = $status_count->get('proses', 0);
                $diambil = $status_count->get('diambil', 0);
                $garansi = $status_count->get('garansi', 0);
                $batal = $status_count->get('batal', 0);

                $total_estimasi_biaya = $servis->sum('estimasi_biaya');
                $total_upah_teknisi = $servis->sum('upah_teknisi');
                $total_keuntungan_toko = $servis->sum('keuntungan_toko');

                $total_pendapatan_servis = $servis->filter(function($s) {
                    return $s->transaksi && $s->transaksi->status == 'Lunas';
                })->sum('estimasi_biaya');

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

                return [
                    'judul' => 'Laporan Teknisi & Data Service',
                    'view' => 'laporan.pdf-servis-rekap',
                    'orientation' => 'landscape',
                    'servis' => $servis,
                    'total_unit' => $total_unit,
                    'selesai' => $selesai,
                    'proses' => $proses,
                    'diambil' => $diambil,
                    'garansi' => $garansi,
                    'batal' => $batal,
                    'total_estimasi_biaya' => $total_estimasi_biaya,
                    'total_upah_teknisi' => $total_upah_teknisi,
                    'total_keuntungan_toko' => $total_keuntungan_toko,
                    'total_pendapatan_servis' => $total_pendapatan_servis,
                    'teknisi_stats' => $teknisi_stats,
                    'kpi' => [
                        ['label' => 'Total Nilai Jasa', 'val' => 'Rp ' . number_format($total_estimasi_biaya, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-cash-stack'],
                        ['label' => 'Total Upah Teknisi', 'val' => 'Rp ' . number_format($total_upah_teknisi, 0, ',', '.'), 'color' => 'info', 'icon' => 'bi-person-badge'],
                        ['label' => 'Laba Bersih Toko', 'val' => 'Rp ' . number_format($total_keuntungan_toko, 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-shop'],
                        ['label' => 'Pendapatan Lunas', 'val' => 'Rp ' . number_format($total_pendapatan_servis, 0, ',', '.'), 'color' => 'warning', 'icon' => 'bi-wallet2'],
                    ]
                ];

            case 'komplain':
                $keywords = ['komplain', 'rusak', 'kecewa', 'error', 'lambat', 'salah', 'tidak sesuai', 'retur', 'pecah', 'cacat', 'komplen', 'masalah', 'kendala', 'complain'];

                $logQuery = ChatbotLog::with('user')
                    ->where(function($q) use ($keywords) {
                        foreach ($keywords as $kw) {
                            $q->orWhere('pesan', 'like', '%' . $kw . '%');
                        }
                    });
                if ($tgl_awal) $logQuery->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $logQuery->where('created_at', '<=', $tgl_akhir);
                $logs = $logQuery->latest()->get();
                $total_komplain = $logs->count();

                $komplainQuery = Komplain::with(['transaksi', 'user']);
                if ($tgl_awal) $komplainQuery->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $komplainQuery->where('created_at', '<=', $tgl_akhir);
                $komplain_resmi = $komplainQuery->latest()->get();

                $servisKomplainQuery = ServisDetail::with('transaksi', 'teknisi')->whereIn('status', ['garansi', 'batal']);
                if ($tgl_awal) $servisKomplainQuery->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $servisKomplainQuery->where('created_at', '<=', $tgl_akhir);
                $servis_komplain = $servisKomplainQuery->latest()->get();

                return [
                    'judul' => 'Laporan Komplain Pelanggan via Chatbot & Servis',
                    'view' => 'laporan.pdf-komplain',
                    'orientation' => 'portrait',
                    'logs' => $logs,
                    'total_komplain' => $total_komplain,
                    'komplain_resmi' => $komplain_resmi,
                    'servis_komplain' => $servis_komplain,
                    'kpi' => [
                        ['label' => 'Komplain Chatbot AI', 'val' => $total_komplain . ' Keluhan', 'color' => 'primary', 'icon' => 'bi-robot'],
                        ['label' => 'Komplain Resmi Tiket', 'val' => $komplain_resmi->count() . ' Kasus', 'color' => 'danger', 'icon' => 'bi-exclamation-octagon'],
                        ['label' => 'Servis Garansi / Batal', 'val' => $servis_komplain->count() . ' Unit', 'color' => 'warning', 'icon' => 'bi-arrow-counterclockwise'],
                        ['label' => 'Total Keseluruhan', 'val' => ($total_komplain + $komplain_resmi->count() + $servis_komplain->count()) . ' Item', 'color' => 'secondary', 'icon' => 'bi-chat-left-text'],
                    ]
                ];

            case 'chatbot-analitik':
                $query = ChatbotLog::with('user');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $logs = $query->latest()->get();

                $total_percakapan = $logs->count();
                $total_user_aktif = $logs->groupBy('user_id')->count();
                $kategori_pertanyaan = $logs->whereNotNull('kategori')->groupBy('kategori')->map->count()->sortDesc();
                $percakapan_per_hari = $logs->groupBy(fn($l) => $l->created_at->format('Y-m-d'))->map->count()->sortDesc();

                return [
                    'judul' => 'Laporan Chatbot (Analitik & Percakapan)',
                    'view' => 'laporan.pdf-chatbot-analitik',
                    'orientation' => 'landscape',
                    'logs' => $logs,
                    'total_percakapan' => $total_percakapan,
                    'total_user_aktif' => $total_user_aktif,
                    'kategori_pertanyaan' => $kategori_pertanyaan,
                    'percakapan_per_hari' => $percakapan_per_hari,
                    'kpi' => [
                        ['label' => 'Total Interaksi Chat', 'val' => number_format($total_percakapan, 0, ',', '.') . ' Pesan', 'color' => 'primary', 'icon' => 'bi-chat-dots'],
                        ['label' => 'Pengguna Unik', 'val' => $total_user_aktif . ' User', 'color' => 'success', 'icon' => 'bi-people'],
                        ['label' => 'Top Kategori', 'val' => $kategori_pertanyaan->keys()->first() ?? 'Umum', 'color' => 'info', 'icon' => 'bi-bookmark-star'],
                        ['label' => 'Rata-rata Harian', 'val' => ($percakapan_per_hari->count() > 0 ? round($total_percakapan / $percakapan_per_hari->count(), 1) : 0) . ' Chat/Hari', 'color' => 'warning', 'icon' => 'bi-graph-up'],
                    ]
                ];

            case 'keuangan':
                $query = Transaksi::with('detail.produk', 'servisDetail.jasaServis')->where('status', 'Lunas');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $transaksi_lunas = $query->get();

                $penjualan = $transaksi_lunas->where('tipe', 'penjualan');
                $servis = $transaksi_lunas->where('tipe', 'servis');

                $total_penjualan = $penjualan->sum('total_bayar');
                $total_servis = $servis->sum('total_bayar');
                $total_keseluruhan = $transaksi_lunas->sum('total_bayar');
                $jumlah_transaksi_penjualan = $penjualan->count();
                $jumlah_transaksi_servis = $servis->count();

                $total_hpp = 0;
                foreach ($penjualan as $t) {
                    foreach ($t->detail as $d) {
                        if ($d->produk) {
                            $total_hpp += $d->produk->harga_beli * $d->jumlah;
                        }
                    }
                }

                $laba_kotor = $total_penjualan - $total_hpp;

                return [
                    'judul' => 'Laporan Keuangan Ringkas',
                    'view' => 'laporan.pdf-keuangan',
                    'orientation' => 'portrait',
                    'total_penjualan' => $total_penjualan,
                    'total_servis' => $total_servis,
                    'total_keseluruhan' => $total_keseluruhan,
                    'jumlah_transaksi_penjualan' => $jumlah_transaksi_penjualan,
                    'jumlah_transaksi_servis' => $jumlah_transaksi_servis,
                    'total_hpp' => $total_hpp,
                    'laba_kotor' => $laba_kotor,
                    'kpi' => [
                        ['label' => 'Total Omzet Masuk', 'val' => 'Rp ' . number_format($total_keseluruhan, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-cash-stack'],
                        ['label' => 'Pendapatan Penjualan', 'val' => 'Rp ' . number_format($total_penjualan, 0, ',', '.'), 'color' => 'info', 'icon' => 'bi-bag-check'],
                        ['label' => 'Pendapatan Servis', 'val' => 'Rp ' . number_format($total_servis, 0, ',', '.'), 'color' => 'warning', 'icon' => 'bi-tools'],
                        ['label' => 'Estimasi Laba Kotor', 'val' => 'Rp ' . number_format($laba_kotor, 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-graph-up-arrow'],
                    ]
                ];

            case 'metode-pembayaran':
                $query = Transaksi::where('status', 'Lunas');
                if ($tgl_awal) $query->where('created_at', '>=', $tgl_awal);
                if ($tgl_akhir) $query->where('created_at', '<=', $tgl_akhir);
                $transaksi = $query->get();

                $total_cash = $transaksi->where('metode_pembayaran', 'cash')->sum('total_bayar');
                $count_cash = $transaksi->where('metode_pembayaran', 'cash')->count();

                $total_transfer = $transaksi->where('metode_pembayaran', 'transfer')->sum('total_bayar');
                $count_transfer = $transaksi->where('metode_pembayaran', 'transfer')->count();

                $total_keseluruhan = $total_cash + $total_transfer;
                $count_keseluruhan = $count_cash + $count_transfer;

                $persen_cash_omzet = $total_keseluruhan > 0 ? round(($total_cash / $total_keseluruhan) * 100, 2) : 0;
                $persen_transfer_omzet = $total_keseluruhan > 0 ? round(($total_transfer / $total_keseluruhan) * 100, 2) : 0;

                $persen_cash_count = $count_keseluruhan > 0 ? round(($count_cash / $count_keseluruhan) * 100, 2) : 0;
                $persen_transfer_count = $count_keseluruhan > 0 ? round(($count_transfer / $count_keseluruhan) * 100, 2) : 0;

                $transaksi_cash = $transaksi->where('metode_pembayaran', 'cash')->sortByDesc('created_at')->take(15)->values();
                $transaksi_transfer = $transaksi->where('metode_pembayaran', 'transfer')->sortByDesc('created_at')->take(15)->values();

                return [
                    'judul' => 'Laporan Analisis Metode Pembayaran (Cash vs Transfer)',
                    'view' => 'laporan.pdf-metode-pembayaran',
                    'orientation' => 'portrait',
                    'total_cash' => $total_cash,
                    'count_cash' => $count_cash,
                    'persen_cash_omzet' => $persen_cash_omzet,
                    'persen_cash_count' => $persen_cash_count,
                    'total_transfer' => $total_transfer,
                    'count_transfer' => $count_transfer,
                    'persen_transfer_omzet' => $persen_transfer_omzet,
                    'persen_transfer_count' => $persen_transfer_count,
                    'total_keseluruhan' => $total_keseluruhan,
                    'count_keseluruhan' => $count_keseluruhan,
                    'transaksi_cash' => $transaksi_cash,
                    'transaksi_transfer' => $transaksi_transfer,
                    'kpi' => [
                        ['label' => 'Total Omzet Lunas', 'val' => 'Rp ' . number_format($total_keseluruhan, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-cash-coin'],
                        ['label' => 'Nominal Cash (' . $persen_cash_omzet . '%)', 'val' => 'Rp ' . number_format($total_cash, 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-cash'],
                        ['label' => 'Nominal Transfer (' . $persen_transfer_omzet . '%)', 'val' => 'Rp ' . number_format($total_transfer, 0, ',', '.'), 'color' => 'info', 'icon' => 'bi-credit-card-2-front'],
                        ['label' => 'Total Transaksi', 'val' => $count_keseluruhan . ' TRX', 'color' => 'warning', 'icon' => 'bi-receipt'],
                    ]
                ];

            case 'margin':
                $query = Produk::with('kategori');
                if (!empty($filter['kategori_id'])) {
                    $query->where('kategori_id', $filter['kategori_id']);
                }
                $produk = $query->get();

                $total_harga_beli = $produk->sum(fn($p) => $p->harga_beli * $p->stok);
                $total_harga_jual = $produk->sum(fn($p) => $p->harga_jual * $p->stok);
                $total_margin = $total_harga_jual - $total_harga_beli;
                $persen_margin = $total_harga_beli > 0 ? round(($total_margin / $total_harga_beli) * 100, 2) : 0;

                return [
                    'judul' => 'Laporan Margin Keuntungan Produk',
                    'view' => 'laporan.pdf-margin',
                    'orientation' => 'landscape',
                    'produk' => $produk,
                    'total_harga_beli' => $total_harga_beli,
                    'total_harga_jual' => $total_harga_jual,
                    'total_margin' => $total_margin,
                    'persen_margin' => $persen_margin,
                    'kpi' => [
                        ['label' => 'Nilai Beli Modal Stok', 'val' => 'Rp ' . number_format($total_harga_beli, 0, ',', '.'), 'color' => 'secondary', 'icon' => 'bi-box-arrow-in-down'],
                        ['label' => 'Potensi Omzet Penjualan', 'val' => 'Rp ' . number_format($total_harga_jual, 0, ',', '.'), 'color' => 'primary', 'icon' => 'bi-tag'],
                        ['label' => 'Potensi Margin Keuntungan', 'val' => 'Rp ' . number_format($total_margin, 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-graph-up'],
                        ['label' => 'Persentase Rata-rata', 'val' => $persen_margin . '%', 'color' => 'info', 'icon' => 'bi-percent'],
                    ]
                ];

            default:
                abort(404, 'Jenis laporan tidak ditemukan.');
        }
    }
}

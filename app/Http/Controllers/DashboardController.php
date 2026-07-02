<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\ServisDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get initial values for summary cards (same as in original blade)
        $total_produk = Produk::count();
        $penjualan_hari_ini = Transaksi::whereDate('created_at', Carbon::today())->count();
        $servis_berjalan = ServisDetail::where('status', 'proses')->count();
        $pendapatan_bulan_ini = Transaksi::where('status', 'Lunas')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('total_bayar');

        $teknisi_stats = collect();
        if (Auth::user()->peran == 'teknisi') {
            $servis = ServisDetail::with('transaksi')->where('teknisi_id', Auth::id())->get();
            
            $total_completed = $servis->whereIn('status', ['selesai', 'diambil'])->count();
            $total_proses = $servis->where('status', 'proses')->count();
            $total_upah = $servis->whereIn('status', ['selesai', 'diambil'])->sum('upah_teknisi');
            $total_toko = $servis->whereIn('status', ['selesai', 'diambil'])->sum('keuntungan_toko');

            $list_servis = $servis->whereIn('status', ['selesai', 'diambil'])->map(function($s) {
                return [
                    'kode' => $s->transaksi->kode_transaksi ?? '-',
                    'pelanggan' => $s->transaksi->nama_pelanggan ?? '-',
                    'barang' => $s->nama_barang ?? 'Custom Servis',
                    'total' => $s->estimasi_biaya,
                    'upah' => $s->upah_teknisi,
                    'toko' => $s->keuntungan_toko,
                    'tanggal' => $s->tanggal_selesai ? Carbon::parse($s->tanggal_selesai)->translatedFormat('d M Y') : Carbon::parse($s->updated_at)->translatedFormat('d M Y')
                ];
            })->values();

            $teknisi_stats = collect([
                'total_completed' => $total_completed,
                'total_proses' => $total_proses,
                'total_upah' => $total_upah,
                'total_toko' => $total_toko,
                'list' => $list_servis
            ]);
        }

        $admin_teknisi_stats = collect();
        if (Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir') {
            $teknisis = \App\Models\User::where('peran', 'teknisi')->get();
            $admin_teknisi_stats = $teknisis->map(function ($t) {
                $servis = ServisDetail::where('teknisi_id', $t->id)->get();
                return [
                    'nama' => $t->name,
                    'proses' => $servis->where('status', 'proses')->count(),
                    'selesai' => $servis->whereIn('status', ['selesai', 'diambil'])->count(),
                    'total_servis' => $servis->count(),
                    'upah' => $servis->whereIn('status', ['selesai', 'diambil'])->sum('upah_teknisi'),
                    'toko' => $servis->whereIn('status', ['selesai', 'diambil'])->sum('keuntungan_toko'),
                ];
            });
        }

        return view('dashboard', compact(
            'total_produk',
            'penjualan_hari_ini',
            'servis_berjalan',
            'pendapatan_bulan_ini',
            'teknisi_stats',
            'admin_teknisi_stats'
        ));
    }

    public function getStatistiks(Request $request)
    {
        // Check permissions
        if (Auth::user()->peran !== 'admin' && Auth::user()->peran !== 'kasir') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filter = $request->query('filter', 'hari');
        $data = [];

        // Set locale to Indonesian for nice formatting
        Carbon::setLocale('id');

        if ($filter === 'hari') {
            // Last 15 days
            for ($i = 14; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $data[$dateStr] = [
                    'label' => $date->translatedFormat('d M Y'),
                    'penjualan' => 0,
                    'keuntungan' => 0,
                ];
            }

            $transaksi = Transaksi::with(['detail.produk'])
                ->where('status', 'Lunas')
                ->where('created_at', '>=', Carbon::today()->subDays(14)->startOfDay())
                ->get();

            foreach ($transaksi as $t) {
                $dateStr = $t->created_at->format('Y-m-d');
                if (isset($data[$dateStr])) {
                    $data[$dateStr]['penjualan'] += (float)$t->total_bayar;
                    
                    $profit = 0;
                    if ($t->tipe === 'penjualan') {
                        foreach ($t->detail as $d) {
                            if ($d->produk) {
                                $profit += ($d->harga_satuan - $d->produk->harga_beli) * $d->jumlah;
                            }
                        }
                    } else {
                        $profit = $t->total_bayar;
                    }
                    $data[$dateStr]['keuntungan'] += (float)$profit;
                }
            }
        } elseif ($filter === 'minggu') {
            // Last 8 weeks
            for ($i = 7; $i >= 0; $i--) {
                $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
                $weekKey = $startOfWeek->format('Y-W');
                
                $label = $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M');
                $data[$weekKey] = [
                    'label' => $label,
                    'penjualan' => 0,
                    'keuntungan' => 0,
                ];
            }

            $transaksi = Transaksi::with(['detail.produk'])
                ->where('status', 'Lunas')
                ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek()->startOfDay())
                ->get();

            foreach ($transaksi as $t) {
                // Carbon startOfWeek makes sure the week key aligns perfectly
                $weekKey = $t->created_at->startOfWeek()->format('Y-W');
                if (isset($data[$weekKey])) {
                    $data[$weekKey]['penjualan'] += (float)$t->total_bayar;
                    
                    $profit = 0;
                    if ($t->tipe === 'penjualan') {
                        foreach ($t->detail as $d) {
                            if ($d->produk) {
                                $profit += ($d->harga_satuan - $d->produk->harga_beli) * $d->jumlah;
                            }
                        }
                    } else {
                        $profit = $t->total_bayar;
                    }
                    $data[$weekKey]['keuntungan'] += (float)$profit;
                }
            }
        } elseif ($filter === 'bulan') {
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthKey = $month->format('Y-m');
                
                $data[$monthKey] = [
                    'label' => $month->translatedFormat('M Y'),
                    'penjualan' => 0,
                    'keuntungan' => 0,
                ];
            }

            $transaksi = Transaksi::with(['detail.produk'])
                ->where('status', 'Lunas')
                ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth()->startOfDay())
                ->get();

            foreach ($transaksi as $t) {
                $monthKey = $t->created_at->format('Y-m');
                if (isset($data[$monthKey])) {
                    $data[$monthKey]['penjualan'] += (float)$t->total_bayar;
                    
                    $profit = 0;
                    if ($t->tipe === 'penjualan') {
                        foreach ($t->detail as $d) {
                            if ($d->produk) {
                                $profit += ($d->harga_satuan - $d->produk->harga_beli) * $d->jumlah;
                            }
                        }
                    } else {
                        $profit = $t->total_bayar;
                    }
                    $data[$monthKey]['keuntungan'] += (float)$profit;
                }
            }
        }

        return response()->json(array_values($data));
    }
}

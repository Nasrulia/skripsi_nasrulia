<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .toko-nama { font-size: 24pt; font-weight: bold; margin: 0; color: #1a1a27; letter-spacing: 1px; }
        .toko-alamat { font-size: 10pt; margin: 5px 0 0 0; color: #555; }
        .judul-laporan { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; text-decoration: underline; }
        .info { margin-bottom: 15px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #777; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 10pt; }
        td { font-size: 9pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Pusat Layanan IT, Penjualan Komputer, dan Jasa Servis Profesional<br>
        Banjarmasin, Kalimantan Selatan | Email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>

    <div class="judul-laporan">{{ $judul }}</div>

    <div class="info">
        Periode       : {{ $periode_label ?? 'Semua Waktu' }}<br>
        Tanggal Cetak : {{ $waktu_cetak }}<br>
        Dicetak Oleh  : {{ $dicetak_oleh ?? 'Administrator' }}<br>
        Kriteria      : Transaksi Penjualan Lunas (Diurutkan Berdasarkan Penjualan Tertinggi)
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kategori</th>
                <th width="40%">Nama Produk</th>
                <th width="15%">Jumlah Terjual</th>
                <th width="20%">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $total_terjual_semua = 0; $total_omzet_semua = 0; @endphp
            @forelse($data as $d)
                @php 
                    $total_terjual_semua += $d->total_terjual;
                    $total_omzet_semua += $d->total_pendapatan;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->produk->kategori->nama_kategori ?? '-' }}</td>
                    <td class="fw-bold">{{ $d->produk->nama_produk ?? 'Produk Terhapus' }}</td>
                    <td class="text-center">{{ $d->total_terjual }} unit</td>
                    <td class="text-right">Rp {{ number_format($d->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data transaksi penjualan produk yang lunas.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->isNotEmpty())
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-center">{{ $total_terjual_semua }} unit</td>
                <td class="text-right">Rp {{ number_format($total_omzet_semua, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->format('d F Y') }}<br>
            Mengetahui,<br>
            Pimpinan Toko
            <div class="ttd-nama">Nasrulia / Pemilik</div>
        </div>
    </div>

</body>
</html>

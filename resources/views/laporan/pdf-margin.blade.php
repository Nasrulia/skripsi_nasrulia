<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .toko-nama { font-size: 24pt; font-weight: bold; margin: 0; color: #1a1a27; }
        .toko-alamat { font-size: 10pt; margin: 5px 0; color: #555; }
        .judul-laporan { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; text-decoration: underline; }
        .info { margin-bottom: 15px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #777; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 10pt; }
        td { font-size: 9pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .bg-hijau { background-color: #d4edda; }
        .bg-merah { background-color: #f8d7da; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Banjarmasin, Kalimantan Selatan | email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>
    <div class="judul-laporan">{{ $judul }}</div>
    <div class="info">Tanggal Cetak: {{ $waktu_cetak }} | Dicetak Oleh: {{ $dicetak_oleh ?? 'Administrator' }}</div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kategori</th>
                <th width="25%">Nama Produk</th>
                <th width="12%">Harga Beli</th>
                <th width="12%">Harga Jual</th>
                <th width="8%">Stok</th>
                <th width="13%">Margin/Unit</th>
                <th width="10%">% Margin</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $total_margin_all = 0; @endphp
            @forelse($produk as $p)
                @php
                    $margin = $p->harga_jual - $p->harga_beli;
                    $persen = $p->harga_beli > 0 ? round(($margin / $p->harga_beli) * 100, 2) : 0;
                    $total_margin_all += $margin * $p->stok;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td class="text-right">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $p->stok }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($margin, 0, ',', '.') }}</td>
                    <td class="text-center {{ $persen >= 20 ? 'bg-hijau' : 'bg-merah' }}">{{ $persen }}%</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data produk.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right fw-bold">TOTAL NILAI MARGIN (Stok x Margin/Unit):</td>
                <td class="text-right fw-bold" style="background-color: #d1e7dd;">Rp {{ number_format($total_margin_all, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right fw-bold">TOTAL Harga Beli Seluruh Stok:</td>
                <td class="text-right">Rp {{ number_format($total_harga_beli, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right fw-bold">TOTAL Harga Jual Seluruh Stok:</td>
                <td class="text-right">Rp {{ number_format($total_harga_jual, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right fw-bold">PERSENTASE MARGIN KESELURUHAN:</td>
                <td colspan="2" class="text-center fw-bold" style="background-color: #cff4fc;">{{ $persen_margin }}%</td>
            </tr>
        </tfoot>
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

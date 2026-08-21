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
        th, td { border: 1px solid #777; padding: 8px 10px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 10pt; }
        td { font-size: 10pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .bg-hijau { background-color: #d4edda; }
        .bg-biru { background-color: #cff4fc; }
        .bg-kuning { background-color: #fff3cd; }
        .ttd-container { width: 100%; margin-top: 50px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .border-bottom { border-bottom: 2px solid #333; }
        .fs-5 { font-size: 12pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Banjarmasin, Kalimantan Selatan | email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>
    <div class="judul-laporan">{{ $judul }}</div>
    <div class="info">Periode: {{ $periode_label ?? 'Semua Waktu' }} | Tanggal Cetak: {{ $waktu_cetak }} | Dicetak Oleh: {{ $dicetak_oleh ?? 'Administrator' }}</div>

    <table>
        <tr>
            <th colspan="2" class="bg-biru fs-5">RINGKASAN PENDAPATAN USAHA</th>
        </tr>
        <tr>
            <td width="60%"><strong>A. Pendapatan dari Penjualan Barang</strong></td>
            <td width="40%" class="text-right fw-bold">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Jumlah Transaksi Penjualan Barang</td>
            <td class="text-right">{{ $jumlah_transaksi_penjualan }} transaksi</td>
        </tr>
        <tr>
            <td><strong>B. Pendapatan dari Jasa Servis</strong></td>
            <td class="text-right fw-bold">Rp {{ number_format($total_servis, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Jumlah Transaksi Jasa Servis</td>
            <td class="text-right">{{ $jumlah_transaksi_servis }} transaksi</td>
        </tr>
        <tr>
            <td colspan="2" class="border-bottom"></td>
        </tr>
        <tr>
            <td><strong>TOTAL PENDAPATAN KOTOR (A + B)</strong></td>
            <td class="text-right fw-bold fs-5 bg-hijau">Rp {{ number_format($total_keseluruhan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th colspan="2" class="bg-kuning fs-5">RINCIAN LABA KOTOR PENJUALAN BARANG</th>
        </tr>
        <tr>
            <td width="60%">Total Penjualan Barang</td>
            <td width="40%" class="text-right">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Harga Pokok Penjualan (HPP)</td>
            <td class="text-right">Rp {{ number_format($total_hpp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="border-bottom"></td>
        </tr>
        <tr>
            <td><strong>LABA KOTOR PENJUALAN BARANG</strong></td>
            <td class="text-right fw-bold fs-5 bg-hijau">Rp {{ number_format($laba_kotor, 0, ',', '.') }}</td>
        </tr>
        @php $persen_laba = $total_penjualan > 0 ? round(($laba_kotor / $total_penjualan) * 100, 2) : 0; @endphp
        <tr>
            <td>Margin Laba Kotor (%)</td>
            <td class="text-right fw-bold">{{ $persen_laba }}%</td>
        </tr>
    </table>

    <table>
        <tr>
            <th colspan="2" class="bg-biru fs-5">PROYEKSI & EVALUASI USAHA</th>
        </tr>
        <tr>
            <td width="60%">Proporsi Pendapatan Penjualan Barang</td>
            @php $prop_barang = $total_keseluruhan > 0 ? round(($total_penjualan / $total_keseluruhan) * 100, 2) : 0; @endphp
            <td width="40%" class="text-right fw-bold">{{ $prop_barang }}%</td>
        </tr>
        <tr>
            <td>Proporsi Pendapatan Jasa Servis</td>
            @php $prop_servis = $total_keseluruhan > 0 ? round(($total_servis / $total_keseluruhan) * 100, 2) : 0; @endphp
            <td class="text-right fw-bold">{{ $prop_servis }}%</td>
        </tr>
        <tr>
            <td><em>Kesimpulan: {{ $prop_barang >= $prop_servis ? 'Bisnis didominasi penjualan barang' : 'Bisnis didominasi jasa servis' }}</em></td>
            <td class="text-center">-</td>
        </tr>
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

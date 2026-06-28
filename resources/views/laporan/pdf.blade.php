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
        Tanggal Cetak : {{ $waktu_cetak }}<br>
        Dicetak Oleh  : Administrator
    </div>

    <table>
        <thead>
            @if($tipe == 'produk-semua' || $tipe == 'produk-menipis')
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kategori</th>
                <th width="35%">Nama Produk</th>
                <th width="15%">Harga Beli</th>
                <th width="15%">Harga Jual</th>
                <th width="10%">Stok</th>
            </tr>
            
            @elseif($tipe == 'jasa')
            <tr>
                <th width="5%">No</th>
                <th width="65%">Nama Jasa Servis</th>
                <th width="30%">Biaya Jasa (Rp)</th>
            </tr>

            @elseif($tipe == 'chatbot')
            <tr>
                <th width="5%">No</th>
                <th width="25%">Kata Kunci (Keyword)</th>
                <th width="70%">Jawaban Sistem AI</th>
            </tr>

            @elseif($tipe == 'transaksi-semua' || $tipe == 'transaksi-lunas' || $tipe == 'transaksi-pending')
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Transaksi</th>
                <th width="20%">Tanggal</th>
                <th width="25%">Nama Pelanggan</th>
                <th width="15%">Status</th>
                <th width="15%">Total Bayar</th>
            </tr>

            @elseif($tipe == 'pendapatan')
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Transaksi</th>
                <th width="25%">Tanggal Lunas</th>
                <th width="30%">Nama Pelanggan</th>
                <th width="20%">Nominal Pendapatan</th>
            </tr>
            @endif
        </thead>
        
        <tbody>
            @php $no = 1; $grand_total = 0; @endphp
            @forelse($data as $d)
                @if($tipe == 'produk-semua' || $tipe == 'produk-menipis')
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $d->nama_produk }}</td>
                    <td class="text-right">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ $d->stok }}</td>
                </tr>

                @elseif($tipe == 'jasa')
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->nama_jasa }}</td>
                    <td class="text-right">Rp {{ number_format($d->biaya_jasa, 0, ',', '.') }}</td>
                </tr>

                @elseif($tipe == 'chatbot')
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="fw-bold text-center">{{ $d->kata_kunci }}</td>
                    <td>{{ $d->jawaban }}</td>
                </tr>

                @elseif($tipe == 'transaksi-semua' || $tipe == 'transaksi-lunas' || $tipe == 'transaksi-pending')
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $d->kode_transaksi }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($d->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td class="text-center">{{ strtoupper($d->status) }}</td>
                    <td class="text-right">Rp {{ number_format($d->total_bayar, 0, ',', '.') }}</td>
                </tr>

                @elseif($tipe == 'pendapatan')
                @php $grand_total += $d->total_bayar; @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $d->kode_transaksi }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($d->updated_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($d->total_bayar, 0, ',', '.') }}</td>
                </tr>
                @endif

            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data untuk laporan ini.</td>
                </tr>
            @endforelse
        </tbody>

        @if($tipe == 'pendapatan')
        <tfoot>
            <tr>
                <td colspan="4" class="text-right fw-bold">TOTAL PENDAPATAN KESELURUHAN:</td>
                <td class="text-right fw-bold" style="background-color: #d1e7dd;">Rp {{ number_format($grand_total, 0, ',', '.') }}</td>
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
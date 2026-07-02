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
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 9.5pt; }
        td { font-size: 8.5pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }

        .summary-table { margin-bottom: 25px; border: none; }
        .summary-table td { border: none; padding: 0 10px 0 0; vertical-align: top; }
        .summary-card { border: 1px solid #bbb; padding: 12px; border-radius: 8px; background-color: #fafafa; }
        .summary-title { font-size: 8pt; text-transform: uppercase; color: #666; font-weight: bold; margin-bottom: 5px; }
        .summary-value { font-size: 14pt; font-weight: bold; color: #111; }
        .summary-subtext { font-size: 7.5pt; color: #777; margin-top: 5px; }
        
        .section-title { font-size: 11pt; font-weight: bold; margin-top: 15px; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
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
        Dicetak Oleh  : Administrator<br>
        Filter        : Transaksi Berhasil (Lunas)
    </div>

    <!-- Ringkasan Grid -->
    <table class="summary-table">
        <tr>
            <td width="33%">
                <div class="summary-card" style="border-color: #198754; background-color: #f3faf6;">
                    <div class="summary-title" style="color: #198754;">Omzet Tunai (Cash)</div>
                    <div class="summary-value" style="color: #198754;">Rp {{ number_format($total_cash, 0, ',', '.') }}</div>
                    <div class="summary-subtext">
                        {{ $count_cash }} Transaksi ({{ $persen_cash_count }}%)<br>
                        Proporsi Omzet: {{ $persen_cash_omzet }}%
                    </div>
                </div>
            </td>
            <td width="33%">
                <div class="summary-card" style="border-color: #0d6efd; background-color: #f0f7ff;">
                    <div class="summary-title" style="color: #0d6efd;">Omzet Transfer (Bank)</div>
                    <div class="summary-value" style="color: #0d6efd;">Rp {{ number_format($total_transfer, 0, ',', '.') }}</div>
                    <div class="summary-subtext">
                        {{ $count_transfer }} Transaksi ({{ $persen_transfer_count }}%)<br>
                        Proporsi Omzet: {{ $persen_transfer_omzet }}%
                    </div>
                </div>
            </td>
            <td width="34%">
                <div class="summary-card" style="border-color: #712cf9; background-color: #f9f5ff;">
                    <div class="summary-title" style="color: #712cf9;">Total Omzet Gabungan</div>
                    <div class="summary-value" style="color: #712cf9;">Rp {{ number_format($total_keseluruhan, 0, ',', '.') }}</div>
                    <div class="summary-subtext">
                        {{ $count_keseluruhan }} Transaksi Lunas<br>
                        Tunai + Transfer
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Rincian Transaksi Cash -->
    <div class="section-title">A. Rincian Transaksi Tunai (Cash) - Terbaru</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Transaksi</th>
                <th width="20%">Tanggal</th>
                <th width="20%">Tipe</th>
                <th width="20%">Nama Pelanggan</th>
                <th width="15%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no_cash = 1; @endphp
            @forelse($transaksi_cash as $t)
                <tr>
                    <td class="text-center">{{ $no_cash++ }}</td>
                    <td class="text-center fw-bold">{{ $t->kode_transaksi }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="text-center">{{ strtoupper($t->tipe) }}</td>
                    <td>{{ $t->nama_pelanggan }}</td>
                    <td class="text-right">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada transaksi tunai terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rincian Transaksi Transfer -->
    <div class="section-title" style="margin-top: 25px;">B. Rincian Transaksi Non-Tunai (Transfer Bank) - Terbaru</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Transaksi</th>
                <th width="20%">Tanggal</th>
                <th width="20%">Tipe</th>
                <th width="20%">Nama Pelanggan</th>
                <th width="15%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no_transfer = 1; @endphp
            @forelse($transaksi_transfer as $t)
                <tr>
                    <td class="text-center">{{ $no_transfer++ }}</td>
                    <td class="text-center fw-bold">{{ $t->kode_transaksi }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="text-center">{{ strtoupper($t->tipe) }}</td>
                    <td>{{ $t->nama_pelanggan }}</td>
                    <td class="text-right">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada transaksi transfer terbaru.</td>
                </tr>
            @endforelse
        </tbody>
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

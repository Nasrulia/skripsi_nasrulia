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
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 9pt; }
        td { font-size: 8.5pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .card-summary-grid { width: 100%; border-collapse: collapse; margin-bottom: 25px; border: none; }
        .card-summary-grid td { border: none; padding: 0 10px 0 0; vertical-align: top; }
        .summary-card { border: 1px solid #bbb; padding: 12px; border-radius: 8px; background-color: #fafafa; min-height: 90px; }
        .summary-title { font-size: 8pt; text-transform: uppercase; color: #666; font-weight: bold; margin-bottom: 5px; }
        .summary-value { font-size: 15pt; font-weight: bold; color: #111; }
        .summary-subtext { font-size: 7.5pt; color: #777; margin-top: 5px; }
        
        .badge-status { padding: 3px 6px; border-radius: 4px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-proses { background-color: #cfe2ff; color: #084298; }
        .badge-selesai { background-color: #d1e7dd; color: #0f5132; }
        .badge-diambil { background-color: #cff4fc; color: #055160; }
        .badge-garansi { background-color: #fff3cd; color: #664d03; }
        .badge-batal { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Banjarmasin, Kalimantan Selatan | email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>
    <div class="judul-laporan">{{ $judul }}</div>
    <div class="info">Tanggal Cetak: {{ $waktu_cetak }} | Dicetak Oleh: Administrator</div>

    <!-- Ringkasan Grid -->
    <table class="card-summary-grid">
        <tr>
            <td width="33%">
                <div class="summary-card">
                    <div class="summary-title">Total Unit Servis</div>
                    <div class="summary-value">{{ $total_unit }} Unit</div>
                    <div class="summary-subtext">
                        Selesai: {{ $selesai + $diambil }} | Proses: {{ $proses }} | Batal: {{ $batal }}
                    </div>
                </div>
            </td>
            <td width="33%">
                <div class="summary-card">
                    <div class="summary-title">Total Biaya Servis</div>
                    <div class="summary-value">Rp {{ number_format($total_estimasi_biaya, 0, ',', '.') }}</div>
                    <div class="summary-subtext">Total potensi nilai jasa masuk</div>
                </div>
            </td>
            <td width="34%">
                <div class="summary-card" style="border-color: #198754; background-color: #f3faf6;">
                    <div class="summary-title" style="color: #198754;">Pendapatan Riil (Lunas)</div>
                    <div class="summary-value" style="color: #198754;">Rp {{ number_format($total_pendapatan_servis, 0, ',', '.') }}</div>
                    <div class="summary-subtext">Uang masuk dari servis berstatus Lunas</div>
                </div>
            </td>
        </tr>
        <tr style="height: 10px;"><td colspan="3"></td></tr>
        <tr>
            <td>
                <div class="summary-card" style="border-color: #0d6efd; background-color: #f0f7ff;">
                    <div class="summary-title" style="color: #0d6efd;">Total Upah Teknisi</div>
                    <div class="summary-value" style="color: #0d6efd;">Rp {{ number_format($total_upah_teknisi, 0, ',', '.') }}</div>
                    <div class="summary-subtext">Total bagi hasil untuk teknisi</div>
                </div>
            </td>
            <td>
                <div class="summary-card" style="border-color: #712cf9; background-color: #f9f5ff;">
                    <div class="summary-title" style="color: #712cf9;">Total Untung Toko</div>
                    <div class="summary-value" style="color: #712cf9;">Rp {{ number_format($total_keuntungan_toko, 0, ',', '.') }}</div>
                    <div class="summary-subtext">Total keuntungan bersih masuk ke Toko</div>
                </div>
            </td>
            <td></td>
        </tr>
    </table>

    <!-- Kinerja per Teknisi -->
    <h4 style="margin-bottom: 8px;">Ringkasan Kinerja & Pendapatan per Nama Teknisi</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Teknisi</th>
                <th width="13%">Total Kerja (Unit)</th>
                <th width="11%">Selesai/Diambil</th>
                <th width="11%">Proses</th>
                <th width="15%">Total Jasa Servis</th>
                <th width="15%">Upah Teknisi</th>
                <th width="15%">Untung Toko</th>
            </tr>
        </thead>
        <tbody>
            @php $no_tek = 1; @endphp
            @forelse($teknisi_stats as $ts)
                <tr>
                    <td class="text-center">{{ $no_tek++ }}</td>
                    <td class="fw-bold">{{ $ts['name'] }}</td>
                    <td class="text-center">{{ $ts['total_unit'] }} unit</td>
                    <td class="text-center text-success">{{ $ts['selesai'] }}</td>
                    <td class="text-center text-primary">{{ $ts['proses'] }}</td>
                    <td class="text-right">Rp {{ number_format($ts['estimasi_revenue'], 0, ',', '.') }}</td>
                    <td class="text-right text-primary fw-bold">Rp {{ number_format($ts['estimasi_upah'], 0, ',', '.') }}</td>
                    <td class="text-right text-success fw-bold">Rp {{ number_format($ts['estimasi_keuntungan'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Belum ada data teknisi yang bertugas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Detail Seluruh Servis -->
    <h4 style="margin-bottom: 8px; margin-top: 25px;">Detail Laporan Transaksi Servis</h4>
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Kode TRX</th>
                <th width="12%">Pelanggan</th>
                <th width="15%">Barang Servis</th>
                <th width="10%">Diterima Oleh</th>
                <th width="12%">Teknisi</th>
                <th width="8%">Status</th>
                <th width="10%">Total Biaya</th>
                <th width="10%">Upah Teknisi</th>
                <th width="10%">Untung Toko</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($servis as $s)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="fw-bold text-center">{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                    <td>{{ $s->transaksi->nama_pelanggan ?? '-' }}</td>
                    <td>{{ $s->nama_barang ?? 'Custom Servis' }}</td>
                    <td>{{ $s->penerima ?? '-' }}</td>
                    <td>{{ $s->teknisi->name ?? 'Belum Ditugaskan' }}</td>
                    <td class="text-center">
                        @if($s->status == 'proses')
                            <span class="badge-status badge-proses">Proses</span>
                        @elseif($s->status == 'selesai')
                            <span class="badge-status badge-selesai">Selesai</span>
                        @elseif($s->status == 'diambil')
                            <span class="badge-status badge-diambil">Diambil</span>
                        @elseif($s->status == 'garansi')
                            <span class="badge-status badge-garansi">Garansi</span>
                        @elseif($s->status == 'batal')
                            <span class="badge-status badge-batal">Batal</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($s->upah_teknisi, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($s->keuntungan_toko, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center">Belum ada data transaksi servis.</td></tr>
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

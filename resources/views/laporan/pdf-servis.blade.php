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
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .card-summary { border: 1px solid #333; padding: 15px; margin-bottom: 20px; }
        .card-summary table { margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Banjarmasin, Kalimantan Selatan | email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>
    <div class="judul-laporan">{{ $judul }}</div>
    <div class="info">Periode: {{ $periode_label ?? 'Semua Waktu' }} | Tanggal Cetak: {{ $waktu_cetak }} | Dicetak Oleh: {{ $dicetak_oleh ?? 'Administrator' }}</div>

    <div class="card-summary">
        <table>
            <tr>
                <td width="50%"><strong>Total Unit Servis Masuk:</strong> {{ $total_unit }}</td>
                <td><strong>Selesai:</strong> {{ $selesai }} unit</td>
            </tr>
            <tr>
                <td><strong>Proses Pengerjaan:</strong> {{ $proses }} unit</td>
                <td><strong>Diambil:</strong> {{ $diambil }} unit</td>
            </tr>
            <tr>
                <td><strong>Garansi:</strong> {{ $garansi }} unit</td>
                <td><strong>Batal:</strong> {{ $batal }} unit</td>
            </tr>
        </table>
    </div>

    <h4 style="margin-bottom: 10px;">10 Jenis Kerusakan Paling Sering Ditangani</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="75%">Keluhan / Jenis Kerusakan</th>
                <th width="20%">Jumlah Unit</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($kerusakan_terbanyak as $keluhan => $count)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $keluhan }}</td>
                    <td class="text-center fw-bold">{{ $count }} unit</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">Belum ada data servis.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="margin-bottom: 10px; margin-top: 30px;">Detail Seluruh Servis</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode TRX</th>
                <th width="13%">Nama Pelanggan</th>
                <th width="15%">Jenis Servis</th>
                <th width="20%">Keluhan</th>
                <th width="10%">Status</th>
                <th width="12%">Tgl Masuk</th>
                <th width="13%">Penerima</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($servis as $s)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                    <td>{{ $s->transaksi->nama_pelanggan ?? '-' }}</td>
                    <td>{{ $s->jasaServis->nama_jasa ?? $s->nama_barang ?? 'Custom Servis' }}</td>
                    <td>{{ $s->keluhan }}</td>
                    <td class="text-center fw-bold">{{ strtoupper($s->status) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $s->penerima ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Belum ada data servis.</td></tr>
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

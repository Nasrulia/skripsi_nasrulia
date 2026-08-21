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
        .summary-box { border: 1px solid #333; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="toko-nama">NUSANTARA JAYA COMPUTER</h1>
        <p class="toko-alamat">Banjarmasin, Kalimantan Selatan | email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526</p>
    </div>
    <div class="judul-laporan">{{ $judul }}</div>
    <div class="info">Periode: {{ $periode_label ?? 'Semua Waktu' }} | Tanggal Cetak: {{ $waktu_cetak }} | Dicetak Oleh: {{ $dicetak_oleh ?? 'Administrator' }}</div>

    <div class="summary-box">
        <table>
            <tr>
                <td width="50%"><strong>Total Percakapan:</strong> {{ $total_percakapan }}</td>
                <td><strong>Total User Aktif:</strong> {{ $total_user_aktif }} orang</td>
            </tr>
        </table>
    </div>

    <h4 style="margin-bottom: 10px;">Kategori Pertanyaan Paling Sering Diajukan</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Kategori Pertanyaan</th>
                <th width="30%">Frekuensi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($kategori_pertanyaan as $kategori => $count)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="fw-bold">{{ ucfirst($kategori) }}</td>
                    <td class="text-center fw-bold">{{ $count }}x</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">Belum ada data percakapan chatbot.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="margin-bottom: 10px; margin-top: 30px;">Percakapan Per Hari</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Tanggal</th>
                <th width="30%">Jumlah Percakapan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($percakapan_per_hari as $tanggal => $count)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</td>
                    <td class="text-center fw-bold">{{ $count }}x</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">Belum ada data percakapan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="margin-bottom: 10px; margin-top: 30px;">Riwayat Percakapan Terbaru</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">User</th>
                <th width="25%">Pesan</th>
                <th width="35%">Jawaban Bot</th>
                <th width="13%">Kategori</th>
                <th width="10%">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($logs->take(50) as $log)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $log->user->name ?? 'Guest' }}</td>
                    <td>{{ \Str::limit($log->pesan, 50) }}</td>
                    <td>{{ \Str::limit($log->jawaban, 80) }}</td>
                    <td class="text-center">{{ $log->kategori ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Belum ada riwayat percakapan.</td></tr>
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .toko-nama { font-size: 24pt; font-weight: bold; margin: 0; color: #1a1a27; letter-spacing: 1px; }
        .toko-alamat { font-size: 10pt; margin: 5px 0 0 0; color: #555; }
        .judul-laporan { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; text-decoration: underline; }
        .info { margin-bottom: 15px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { border: 1px solid #777; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; font-size: 9.5pt; }
        td { font-size: 8.5pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 7.5pt; text-transform: uppercase; }
        .badge-garansi { background-color: #fff3cd; color: #664d03; }
        .badge-batal { background-color: #f8d7da; color: #842029; }
        .section-title { font-size: 12pt; font-weight: bold; margin-bottom: 10px; margin-top: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
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
        Sumber Data   : Keluhan Chatbot AI & Kendala Layanan Servis
    </div>

    <!-- Bagian 1: Laporan Resmi Komplain Pelanggan -->
    <div class="section-title">A. Laporan Resmi Komplain Pelanggan</div>
    <p style="font-size: 9pt; color: #666; margin-top: 0;">Menampilkan data komplain resmi yang diajukan oleh pelanggan dan terdaftar ke dalam sistem melalui verifikasi nomor transaksi.</p>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode TRX</th>
                <th width="20%">Pelanggan</th>
                <th width="12%">Kategori</th>
                <th width="33%">Rincian Komplain & Tanggal</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no_resmi = 1; @endphp
            @forelse($komplain_resmi as $k)
                <tr>
                    <td class="text-center">{{ $no_resmi++ }}</td>
                    <td class="text-center fw-bold">{{ $k->kode_transaksi }}</td>
                    <td>
                        <div class="fw-bold">{{ $k->nama_pelanggan }}</div>
                        <div style="font-size: 8pt; color: #555;">WA: {{ $k->no_whatsapp }}</div>
                    </td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $k->tipe }}</td>
                    <td>
                        <div>"{{ $k->deskripsi }}"</div>
                        <div style="font-size: 7.5pt; color: #777; margin-top: 3px;">Masuk: {{ \Carbon\Carbon::parse($k->created_at)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="text-center">
                        @if($k->status === 'pending')
                            <span class="badge badge-garansi">PENDING</span>
                        @else
                            <span class="badge" style="background-color: #d1e7dd; color: #0f5132;">SELESAI</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada laporan komplain resmi yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian 2: Deteksi Keluhan Chatbot -->
    <div class="section-title">B. Deteksi Komplain Pelanggan Melalui Chatbot AI</div>
    <p style="font-size: 9pt; color: #666; margin-top: 0;">Menampilkan log percakapan chatbot yang mendeteksi kata kunci sensitif/keluhan pelanggan (seperti komplain, rusak, kecewa, error, lambat, salah kirim, retur, dll).</p>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Pengguna</th>
                <th width="15%">Waktu</th>
                <th width="32%">Pesan Keluhan</th>
                <th width="33%">Tanggapan Chatbot</th>
            </tr>
        </thead>
        <tbody>
            @php $no_chat = 1; @endphp
            @forelse($logs as $log)
                <tr>
                    <td class="text-center">{{ $no_chat++ }}</td>
                    <td class="fw-bold">{{ $log->user->name ?? 'Tamu/Guest' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="text-danger">"{{ $log->pesan }}"</td>
                    <td style="font-style: italic;">{{ $log->jawaban }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ditemukan interaksi chat yang berindikasi komplain.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian 3: Kendala Pengerjaan Servis -->
    <div class="section-title">C. Komplain Pengerjaan Servis (Garansi & Batal)</div>
    <p style="font-size: 9pt; color: #666; margin-top: 0;">Menampilkan unit servis yang berstatus Garansi (masuk kembali karena komplain pengerjaan sebelumnya) atau Batal (tidak terselesaikan).</p>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode TRX</th>
                <th width="18%">Pelanggan</th>
                <th width="18%">Barang Servis</th>
                <th width="24%">Keluhan Kerusakan</th>
                <th width="10%">Status</th>
                <th width="10%">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @php $no_servis = 1; @endphp
            @forelse($servis_komplain as $s)
                <tr>
                    <td class="text-center">{{ $no_servis++ }}</td>
                    <td class="text-center fw-bold">{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                    <td>{{ $s->transaksi->nama_pelanggan ?? '-' }}</td>
                    <td>{{ $s->nama_barang ?? '-' }}</td>
                    <td>{{ $s->keluhan }}</td>
                    <td class="text-center">
                        @if($s->status == 'garansi')
                            <span class="badge badge-garansi">GARANSI</span>
                        @elseif($s->status == 'batal')
                            <span class="badge badge-batal">BATAL</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada unit servis dengan status Garansi atau Batal.</td>
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

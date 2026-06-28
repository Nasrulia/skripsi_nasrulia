<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Servis - {{ $servis->transaksi->kode_transaksi }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            padding: 20px;
        }
        .header {
            border-bottom: 3px double #cbd5e1;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .brand {
            font-size: 18pt;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .tagline {
            font-size: 8.5pt;
            color: #64748b;
            margin: 3px 0 0 0;
        }
        .contact-info {
            text-align: right;
            font-size: 8.5pt;
            color: #475569;
        }
        .contact-info p {
            margin: 2px 0;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 15px 0 25px 0;
            letter-spacing: 1px;
        }
        .info-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-section td {
            border: none;
            padding: 6px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 150px;
        }
        .info-separator {
            width: 15px;
            color: #94a3b8;
        }
        .info-value {
            color: #0f172a;
        }
        .table-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .detail-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: left;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
        }
        .detail-table td {
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .bank-info-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 25px;
            font-size: 9pt;
            color: #166534;
        }
        .bank-info-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .terms-section {
            margin-top: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
        }
        .terms-title {
            font-weight: bold;
            font-size: 8.5pt;
            color: #334155;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .terms-list {
            margin: 0;
            padding-left: 15px;
            font-size: 8pt;
            color: #64748b;
        }
        .terms-list li {
            margin-bottom: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-title {
            font-size: 9pt;
            color: #475569;
            margin-bottom: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .signature-role {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #3b82f6;
            border-radius: 4px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td width="60" style="padding-right: 15px;">
                        <!-- Embed Instagram-style NJC logo inline -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="55" height="55">
                          <circle cx="50" cy="50" r="48" fill="#0084FF" />
                          <circle cx="50" cy="50" r="42" fill="none" stroke="#FFFFFF" stroke-width="2.5" />
                          <g fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M 26 62 L 26 38 L 38 62 L 38 38 L 47 38 L 47 58 C 47 63, 42 63, 42 63" stroke-width="5" />
                            <path d="M 70 43 C 67 37, 56 37, 56 50 C 56 63, 67 63, 70 57" stroke-width="5" />
                          </g>
                          <rect x="60" y="47" width="6" height="6" fill="#FFFFFF" rx="1"/>
                          <rect x="62" y="49" width="2" height="2" fill="#0084FF"/>
                        </svg>
                    </td>
                    <td>
                        <h1 class="brand">NUSANTARA JAYA COMPUTER</h1>
                        <p class="tagline">Solusi Terbaik Perbaikan Perangkat Keras & Lunak Anda</p>
                    </td>
                    <td class="contact-info">
                        <p><strong>Banjarmasin, Kalimantan Selatan</strong></p>
                        <p>Telp / WA: 0851-8239-2525 / 0852-8239-2526</p>
                        <p>Email: admin@njk.com</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Judul Dokumen -->
        <div class="title">Tanda Terima Servis Masuk</div>

        <!-- Informasi Pelanggan & Servis -->
        <table class="info-section">
            <tr>
                <td class="info-label">Kode Transaksi</td>
                <td class="info-separator">:</td>
                <td class="info-value"><strong>{{ $servis->transaksi->kode_transaksi }}</strong></td>

                <td class="info-label" style="padding-left: 40px;">Nama Pelanggan</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ $servis->transaksi->nama_pelanggan }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Masuk</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ \Carbon\Carbon::parse($servis->created_at)->format('d M Y H:i') }}</td>

                <td class="info-label" style="padding-left: 40px;">No. WhatsApp</td>
                <td class="info-separator">:</td>
                <td class="info-value">+62 {{ $servis->transaksi->user->no_whatsapp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Status Awal</td>
                <td class="info-separator">:</td>
                <td class="info-value"><span class="badge" style="background-color: #f59e0b;">Sedang Diproses</span></td>

                <td class="info-label" style="padding-left: 40px;">Diterima Oleh</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ $servis->penerima ?? '-' }}</td>
            </tr>
        </table>

        <!-- Rincian Barang Servis -->
        <div class="table-title">Rincian Perangkat & Keluhan</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th width="35%">Nama Barang / Perangkat</th>
                    <th width="25%">No. Seri / Serial Number</th>
                    <th width="40%">Kendala / Keluhan Awal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $servis->nama_barang }}</strong></td>
                    <td>{{ $servis->no_seri ?? '-' }}</td>
                    <td>{{ $servis->keluhan }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Rincian Biaya & Pembayaran -->
        <div class="table-title">Estimasi & Metode Pembayaran</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th width="35%">Estimasi Biaya Servis</th>
                    <th width="30%">Estimasi Waktu Pengerjaan</th>
                    <th width="35%">Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Rp {{ number_format($servis->estimasi_biaya, 0, ',', '.') }}</strong></td>
                    <td>{{ $servis->estimasi_waktu }}</td>
                    <td style="text-transform: uppercase;"><strong>{{ $servis->transaksi->metode_pembayaran }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Rekening Pembayaran Perusahaan -->
        <div class="bank-info-box">
            <div class="bank-info-title"><i class="bi bi-credit-card-2-front"></i> Rekening Pembayaran Perusahaan:</div>
            Pembayaran dapat dilakukan melalui transfer ke rekening berikut:<br>
            <strong>BANK BRI</strong> &bull; a.n. <strong>CV NUSANTARA JAYA</strong> &bull; No. Rekening: <strong>000 3010 2030 4563</strong>
        </div>

        <!-- Syarat & Ketentuan -->
        <div class="terms-section">
            <div class="terms-title">Syarat & Ketentuan Layanan Servis:</div>
            <ol class="terms-list">
                <li><strong>Pengambilan Barang</strong>: Pengambilan barang wajib menunjukkan lembar tanda terima ini atau bukti notifikasi resmi dari WhatsApp kami.</li>
                <li><strong>Kehilangan Data</strong>: Kami tidak bertanggung jawab atas kehilangan data di dalam perangkat selama proses servis. Pelanggan disarankan untuk melakukan backup data penting terlebih dahulu.</li>
                <li><strong>Barang Terbengkalai</strong>: Barang servis yang tidak diambil dalam waktu 30 hari kalender setelah dihubungi mengenai penyelesaian servis akan dikenakan biaya penyimpanan tambahan, atau tidak menjadi tanggung jawab kami jika terjadi kerusakan lebih lanjut atau kehilangan.</li>
                <li><strong>Garansi Servis</strong>: Garansi perbaikan hanya berlaku selama masa garansi yang ditentukan terhitung setelah barang diambil, dan hanya untuk kerusakan yang sama (tidak termasuk penggantian sparepart baru di luar kesepakatan).</li>
            </ol>
        </div>

        <!-- Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-title">Pelanggan</div>
                    <div class="signature-name">{{ $servis->transaksi->nama_pelanggan }}</div>
                    <div class="signature-role">Tanda Tangan / Persetujuan</div>
                </td>
                <td>
                    <div class="signature-title">Penerima Barang,</div>
                    <div class="signature-name">{{ $servis->penerima }}</div>
                    <div class="signature-role">Staf Nusantara Jaya Computer</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

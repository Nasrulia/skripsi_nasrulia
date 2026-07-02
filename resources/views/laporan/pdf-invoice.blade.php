<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaksi->kode_transaksi }}</title>
    <style>
        @page { margin: 20mm 15mm; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 10pt; color: #000; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .toko-nama { font-size: 18pt; font-weight: bold; margin: 0; letter-spacing: 2px; }
        .toko-alamat { font-size: 8pt; margin: 2px 0; }
        .judul-invoice { text-align: center; font-size: 14pt; font-weight: bold; margin: 10px 0; border: 2px solid #000; padding: 5px; }
        .info-transaksi { width: 100%; margin-bottom: 15px; font-size: 9pt; }
        .info-transaksi td { padding: 2px 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.items th, table.items td { border: 1px solid #000; padding: 5px 8px; }
        table.items th { background-color: #e0e0e0; text-align: center; font-weight: bold; font-size: 9pt; }
        table.items td { font-size: 9pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .total-box { width: 100%; margin-bottom: 30px; }
        .total-box td { padding: 3px 8px; font-size: 9pt; }
        .grand-total { font-size: 12pt; font-weight: bold; background-color: #e0e0e0; }
        .ttd-wrapper { width: 100%; margin-top: 30px; }
        .ttd-kiri { float: left; width: 45%; text-align: center; font-size: 9pt; }
        .ttd-kanan { float: right; width: 45%; text-align: center; font-size: 9pt; position: relative; }
        .ttd-kosong { height: 50px; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .stempel { position: absolute; top: 25px; left: 50%; margin-left: -40px; z-index: 1; }
        .stempel-img { width: 80px; height: 80px; opacity: 0.55; }
        .footer { text-align: center; font-size: 7pt; margin-top: 20px; border-top: 1px solid #999; padding-top: 5px; color: #666; }
        .status-lunas { color: green; font-weight: bold; font-size: 14pt; text-align: center; border: 2px solid green; padding: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td width="15%" style="text-align: center; vertical-align: middle; padding: 0;">
                @php
                    $logoImg = public_path('images/logo.jpg');
                @endphp
                @if(file_exists($logoImg))
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($logoImg)) }}" alt="Logo" style="width: 70px; height: 70px; border-radius: 50%;">
                @endif
            </td>
            <td width="85%" style="text-align: center; vertical-align: middle; padding: 0;">
                <h1 class="toko-nama" style="font-size: 22pt; font-weight: bold; margin: 0; font-family: 'Times New Roman', Times, serif; letter-spacing: 1px;">CV NUSANTARA JAYA</h1>
                <p style="font-size: 10.5pt; font-weight: bold; margin: 4px 0 2px 0; font-family: Arial, Helvetica, sans-serif;">JL.Pahlawan No.88 ( Kampung Melayu ) Banjarmasin</p>
                <p style="font-size: 10.5pt; font-weight: bold; margin: 0; font-family: Arial, Helvetica, sans-serif;">HP/WA : 0851 8239 2525 / 0851 8239 2526</p>
            </td>
        </tr>
    </table>

    @if($transaksi->status != 'Lunas' && $transaksi->metode_pengambilan == 'diambil' && $transaksi->metode_pembayaran == 'cash')
        <div class="judul-invoice">NOTA SEMENTARA</div>
        <div class="status-lunas" style="color: red; border-color: red;">BELUM LUNAS (BAYAR DI TOKO)</div>
    @else
        <div class="judul-invoice">INVOICE / NOTA RESMI</div>
        @if($transaksi->status == 'Lunas')
            <div class="status-lunas">SUDAH LUNAS</div>
        @else
            <div class="status-lunas" style="color: orange; border-color: orange;">PENDING (MENUNGGU KONFIRMASI)</div>
        @endif
    @endif

    <table class="info-transaksi">
        <tr>
            <td width="15%"><strong>No. Invoice</strong></td>
            <td width="35%">: {{ $transaksi->kode_transaksi }}</td>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Pelanggan</strong></td>
            <td>: {{ $transaksi->nama_pelanggan }}</td>
            <td><strong>Jenis</strong></td>
            <td>: {{ strtoupper($transaksi->tipe) }}</td>
        </tr>
        <tr>
            <td><strong>Kasir</strong></td>
            <td>: {{ $kasir->name ?? 'Admin' }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ strtoupper($transaksi->status) }}</td>
        </tr>
        @if($transaksi->metode_pengambilan)
        <tr>
            <td><strong>Pengambilan</strong></td>
            <td>: {{ $transaksi->metode_pengambilan == 'diantar' ? 'DIKIRIM' : 'AMBIL DI TOKO' }}</td>
            <td><strong>Metode Bayar</strong></td>
            <td>: {{ $transaksi->metode_pembayaran == 'cash' ? 'CASH DI TOKO' : 'TRANSFER BANK' }}</td>
        </tr>
        @endif
        @if($transaksi->metode_pengambilan == 'diambil')
        <tr>
            <td><strong>Estimasi Ambil</strong></td>
            <td>: {{ $transaksi->estimasi_diambil ? \Carbon\Carbon::parse($transaksi->estimasi_diambil)->format('d/m/Y H:i') : '-' }}</td>
            @if($transaksi->metode_pembayaran == 'cash' && $transaksi->batas_waktu_pengambilan)
            <td><strong>Batas Waktu</strong></td>
            <td style="color: red; font-weight: bold;">: {{ \Carbon\Carbon::parse($transaksi->batas_waktu_pengambilan)->format('d/m/Y H:i') }}</td>
            @else
            <td></td>
            <td></td>
            @endif
        </tr>
        @endif
        @if($transaksi->metode_pengambilan == 'diantar')
        <tr>
            <td><strong>Ekspedisi</strong></td>
            <td>: {{ $transaksi->ekspedisi->nama_ekspedisi ?? '-' }} ({{ $transaksi->jarak_km ?? '-' }} km)</td>
            <td><strong>Ongkir</strong></td>
            <td>: Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Alamat Kirim</strong></td>
            <td colspan="3">: {{ $transaksi->alamat_pengiriman }}</td>
        </tr>
        @endif
    </table>

    @if($transaksi->tipe == 'penjualan')
    <table class="items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Barang</th>
                <th width="15%">Harga Satuan</th>
                <th width="10%">Qty</th>
                <th width="25%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transaksi->detail as $d)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->produk->nama_produk ?? 'Produk dihapus' }}</td>
                    <td class="text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($transaksi->tipe == 'servis')
    <table class="items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Jenis Servis</th>
                <th width="30%">Keluhan</th>
                <th width="25%">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transaksi->servisDetail as $sd)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $sd->jasaServis->nama_jasa ?? $sd->nama_barang ?? 'Custom Servis' }}</td>
                    <td>{{ $sd->keluhan }}</td>
                    <td class="text-right">Rp {{ number_format($sd->jasaServis->biaya_jasa ?? $sd->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="total-box">
        <tr>
            <td width="75%" class="text-right fw-bold">SUBTOTAL :</td>
            <td width="25%" class="text-right fw-bold">Rp {{ number_format($transaksi->total_bayar - $transaksi->ongkir, 0, ',', '.') }}</td>
        </tr>
        @if($transaksi->ongkir > 0)
        <tr>
            <td width="75%" class="text-right fw-bold">ONGKOS KIRIM :</td>
            <td width="25%" class="text-right fw-bold">Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td width="75%" class="text-right fw-bold">TOTAL BELANJA :</td>
            <td width="25%" class="text-right fw-bold grand-total">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
        </tr>
        @if($transaksi->nominal_dp > 0)
        <tr>
            <td width="75%" class="text-right fw-bold">DP DIBAYAR (TRANSFER) :</td>
            <td width="25%" class="text-right fw-bold" style="color: green;">Rp {{ number_format($transaksi->nominal_dp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td width="75%" class="text-right fw-bold">SISA PELUNASAN (DI TOKO) :</td>
            <td width="25%" class="text-right fw-bold" style="color: red;">Rp {{ number_format($transaksi->total_bayar - $transaksi->nominal_dp, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td class="text-right"><em>Terbilang: #{{ ucwords(terbilang($transaksi->total_bayar)) }} Rupiah #</em></td>
            <td></td>
        </tr>
    </table>

    @php
        if ($transaksi->tipe == 'servis') {
            $teknisi = $transaksi->servisDetail->first()?->teknisi;
            $ttdName = $teknisi ? $teknisi->name : 'Teknisi NJK';
            $ttdRole = 'Teknisi';
        } else {
            $ttdName = $kasir->name ?? 'Admin';
            $ttdRole = 'Kasir';
        }
    @endphp

    <div class="ttd-wrapper clearfix">
        <div class="ttd-kanan">
            <p>Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->format('d F Y') }}<br>{{ $ttdRole }}</p>
            
            <div class="stempel">
                @php 
                    $stempelSvg = public_path('images/stempel.svg');
                    $stempelPng = public_path('storage/stempel.png');
                @endphp
                @if(file_exists($stempelSvg))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($stempelSvg)) }}" alt="Stempel Toko" class="stempel-img">
                @elseif(file_exists($stempelPng))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($stempelPng)) }}" alt="Stempel Toko" class="stempel-img">
                @else
                    <div style="width:100px;height:80px;border:2px dashed #999;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:7pt;color:#999;">[STAMPEL]</div>
                @endif
            </div>

            <div class="ttd-kosong"></div>
            <p class="fw-bold" style="position: relative; z-index: 10;">( {{ $ttdName }} )</p>
            <p style="font-size:7pt; margin-top:2px;">* Stempel & Tanda Tangan Resmi *</p>
        </div>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di Nusantara Jaya Computer | Barang yang sudah dibeli tidak dapat dikembalikan | Simpan nota ini sebagai bukti pembayaran resmi
    </div>
</body>
</html>

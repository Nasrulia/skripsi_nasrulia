<x-app-layout>
    <style>
        /* Styling Preview Dokumen */
        .preview-paper {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            padding: 40px;
            margin: 0 auto;
            max-width: 100%;
        }
        .kop-surat {
            border-bottom: 3px double #1a1a27;
            padding-bottom: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        .kop-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1a1a27;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .kop-sub {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0;
            line-height: 1.4;
        }
        .laporan-title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .laporan-title {
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-decoration: underline;
            margin-bottom: 6px;
        }
        .badge-periode {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            font-size: 0.825rem;
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-block;
            border: 1px solid #cbd5e1;
        }
        .info-meta {
            font-size: 0.85rem;
            color: #475569;
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 20px;
        }
        .preview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 25px;
        }
        .preview-table th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: center;
            font-size: 0.825rem;
            text-transform: uppercase;
        }
        .preview-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            color: #334155;
            vertical-align: middle;
        }
        .preview-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .preview-table tfoot td {
            background-color: #f1f5f9;
            font-weight: 700;
            border: 1px solid #cbd5e1;
        }
        .ttd-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            width: 250px;
            font-size: 0.9rem;
            color: #1e293b;
        }
        .ttd-space {
            height: 70px;
        }
        .ttd-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        /* Responsive & Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                box-shadow: none !important;
                border: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Top Action Bar (No Print) -->
    <div class="row mb-4 no-print align-items-center justify-content-between g-3">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-xs-hover">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
                </a>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Preview: {{ $judul }}</h4>
                    <p class="text-muted small mb-0">Pratinjau laporan sebelum diunduh / dicetak ke file PDF</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
            <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3 shadow-xs-hover">
                <i class="bi bi-printer me-1"></i> Print Browser
            </button>
            <a href="{{ route('laporan.cetak', array_merge(['tipe' => $tipe], request()->all())) }}" target="_blank" class="btn btn-danger rounded-pill px-4 shadow-sm fw-semibold">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh / Cetak PDF
            </a>
        </div>
    </div>

    <!-- Filter Toolbar (No Print) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print bg-white">
        <div class="card-body p-4">
            <form action="{{ route('laporan.preview', $tipe) }}" method="GET" id="form-filter">
                <div class="row g-3 align-items-end">
                    <!-- Mode Filter Radio / Selector -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-funnel-fill text-primary me-1"></i> Mode Filter Waktu
                        </label>
                        <select name="filter_type" id="filter_type" class="form-select rounded-3 border shadow-xs" onchange="toggleFilterInputs()">
                            <option value="semua" {{ $filter['filter_type'] == 'semua' ? 'selected' : '' }}>Semua Data (All Time)</option>
                            <option value="harian" {{ $filter['filter_type'] == 'harian' ? 'selected' : '' }}>Harian (Tanggal Tertentu)</option>
                            <option value="bulanan" {{ $filter['filter_type'] == 'bulanan' ? 'selected' : '' }}>Bulanan (Bulan & Tahun)</option>
                            <option value="tahunan" {{ $filter['filter_type'] == 'tahunan' ? 'selected' : '' }}>Tahunan (Tahun Tertentu)</option>
                            <option value="custom" {{ $filter['filter_type'] == 'custom' ? 'selected' : '' }}>Rentang Tanggal Custom</option>
                        </select>
                    </div>

                    <!-- Input Filter Harian -->
                    <div class="col-lg-3 col-md-6 filter-input-group" id="input-harian" style="{{ $filter['filter_type'] == 'harian' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-bold text-dark">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control rounded-3 border" value="{{ $filter['tanggal'] }}">
                    </div>

                    <!-- Input Filter Bulanan -->
                    <div class="col-lg-3 col-md-6 filter-input-group" id="input-bulanan" style="{{ $filter['filter_type'] == 'bulanan' ? '' : 'display: none;' }}">
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label small fw-bold text-dark">Bulan</label>
                                <select name="bulan" class="form-select rounded-3 border">
                                    @php
                                        $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                    @endphp
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ $filter['bulan'] == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="form-label small fw-bold text-dark">Tahun</label>
                                <select name="tahun" class="form-select rounded-3 border">
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ $filter['tahun'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Input Filter Tahunan -->
                    <div class="col-lg-3 col-md-6 filter-input-group" id="input-tahunan" style="{{ $filter['filter_type'] == 'tahunan' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-bold text-dark">Pilih Tahun</label>
                        <select name="tahun_tahunan" class="form-select rounded-3 border" onchange="document.querySelector('#input-bulanan select[name=tahun]').value = this.value">
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $filter['tahun'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Input Filter Custom Range -->
                    <div class="col-lg-4 col-md-6 filter-input-group" id="input-custom" style="{{ $filter['filter_type'] == 'custom' ? '' : 'display: none;' }}">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark">Dari Tanggal</label>
                                <input type="date" name="tgl_awal" class="form-control rounded-3 border" value="{{ $filter['tgl_awal_raw'] }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark">Sampai Tanggal</label>
                                <input type="date" name="tgl_akhir" class="form-control rounded-3 border" value="{{ $filter['tgl_akhir_raw'] }}">
                            </div>
                        </div>
                    </div>

                    @if($tipe === 'produk-stok' || $tipe === 'margin')
                    <!-- Filter Kategori Produk -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-dark">Kategori</label>
                        <select name="kategori_id" class="form-select rounded-3 border">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ $filter['kategori_id'] == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if($tipe === 'produk-stok')
                    <!-- Filter Status Stok -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-dark">Status Stok</label>
                        <select name="status_stok" class="form-select rounded-3 border">
                            <option value="">Semua</option>
                            <option value="menipis" {{ $filter['status_stok'] == 'menipis' ? 'selected' : '' }}>Menipis (&le; 5)</option>
                            <option value="habis" {{ $filter['status_stok'] == 'habis' ? 'selected' : '' }}>Habis (0)</option>
                            <option value="cukup" {{ $filter['status_stok'] == 'cukup' ? 'selected' : '' }}>Cukup (&gt; 5)</option>
                        </select>
                    </div>
                    @endif

                    <!-- Tombol Aksi Filter -->
                    <div class="col-lg-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-xs fw-semibold">
                            <i class="bi bi-check2 me-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('laporan.preview', $tipe) }}" class="btn btn-light rounded-3 px-3 border shadow-xs text-muted" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards (No Print) -->
    @if(!empty($kpi))
    <div class="row g-3 mb-4 no-print">
        @foreach($kpi as $item)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-{{ $item['color'] }} border-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.75rem;">{{ $item['label'] }}</small>
                        <h5 class="fw-bold text-dark mb-0 mt-1">{{ $item['val'] }}</h5>
                    </div>
                    <div class="bg-{{ $item['color'] }} bg-opacity-10 text-{{ $item['color'] }} rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi {{ $item['icon'] }} fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Document Paper Preview Container (Printed Area) -->
    <div class="preview-paper" id="print-area">
        
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <h1 class="kop-title">NUSANTARA JAYA COMPUTER</h1>
            <p class="kop-sub">
                Pusat Layanan IT, Penjualan Komputer, Perlengkapan, dan Jasa Servis Profesional<br>
                Banjarmasin, Kalimantan Selatan | Email: admin@njk.com | Telp: 0851-8239-2525 / 0852-8239-2526
            </p>
        </div>

        <!-- JUDUL LAPORAN & BADGE PERIODE -->
        <div class="laporan-title-box">
            <div class="laporan-title">{{ $judul }}</div>
            <div class="badge-periode">
                <i class="bi bi-calendar3 me-1"></i> Periode: {{ $filter['periode_label'] }}
            </div>
        </div>

        <!-- META INFORMASI -->
        <div class="info-meta d-flex justify-content-between flex-wrap gap-2">
            <div>
                <strong>Tanggal Generate:</strong> {{ $waktu_cetak }}
            </div>
            <div>
                <strong>Dicetak Oleh:</strong> {{ $dicetak_oleh }}
            </div>
        </div>

        <!-- TABEL DATA / KONTEN KHUSUS BERDASARKAN TIPE LAPORAN -->
        @if($tipe === 'transaksi-penjualan' || $tipe === 'transaksi-servis')
            <div class="table-responsive">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="18%">Kode Transaksi</th>
                            <th width="18%">Tanggal</th>
                            <th width="24%">Nama Pelanggan</th>
                            <th width="15%">Metode Bayar</th>
                            <th width="10%">Status</th>
                            <th width="10%" class="text-end">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; $grand_total = 0; @endphp
                        @forelse($data as $d)
                            @php $grand_total += $d->total_bayar; @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-bold text-primary">{{ $d->kode_transaksi }}</td>
                                <td>{{ $d->created_at ? $d->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td>{{ $d->nama_pelanggan }}</td>
                                <td class="text-uppercase text-center">{{ $d->metode_pembayaran }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $d->status == 'Lunas' ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1">{{ $d->status }}</span>
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($d->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data transaksi yang sesuai dengan periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($data->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end">TOTAL KESELURUHAN:</td>
                            <td class="text-end text-success">Rp {{ number_format($grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

        @elseif($tipe === 'produk-terlaris')
            <div class="table-responsive">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="22%">Kategori</th>
                            <th width="42%">Nama Produk</th>
                            <th width="15%" class="text-center">Jumlah Terjual</th>
                            <th width="15%" class="text-end">Total Omzet (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; $tot_unit = 0; $tot_omzet = 0; @endphp
                        @forelse($data as $d)
                            @php 
                                $tot_unit += $d->total_terjual;
                                $tot_omzet += $d->total_pendapatan;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $d->produk->kategori->nama_kategori ?? '-' }}</td>
                                <td class="fw-bold">{{ $d->produk->nama_produk ?? 'Produk Terhapus' }}</td>
                                <td class="text-center">{{ $d->total_terjual }} unit</td>
                                <td class="text-end">Rp {{ number_format($d->total_pendapatan, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada data transaksi produk terlaris pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($data->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">TOTAL KESELURUHAN:</td>
                            <td class="text-center text-primary">{{ $tot_unit }} unit</td>
                            <td class="text-end text-success">Rp {{ number_format($tot_omzet, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

        @elseif($tipe === 'produk-stok')
            <div class="table-responsive">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Kategori</th>
                            <th width="33%">Nama Produk</th>
                            <th width="14%" class="text-end">Harga Beli</th>
                            <th width="14%" class="text-end">Harga Jual</th>
                            <th width="7%" class="text-center">Stok</th>
                            <th width="7%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($data as $d)
                            @php
                                $status_label = 'Cukup';
                                $badge_bg = 'bg-success';
                                if ($d->stok == 0) {
                                    $status_label = 'Habis';
                                    $badge_bg = 'bg-danger';
                                } elseif ($d->stok <= 5) {
                                    $status_label = 'Menipis';
                                    $badge_bg = 'bg-warning text-dark';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $d->kategori->nama_kategori ?? '-' }}</td>
                                <td class="fw-bold">{{ $d->nama_produk }}</td>
                                <td class="text-end">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center fw-bold">{{ $d->stok }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badge_bg }}">{{ $status_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data stok produk ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($tipe === 'servis-ringkasan')
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-2 text-dark">Ringkasan Status Servis Masuk</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                <span>Total Unit Servis:</span> <strong class="text-primary">{{ $total_unit }} Unit</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                <span>Selesai Dikerjakan:</span> <strong class="text-success">{{ $selesai }} Unit</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                <span>Telah Diambil Pelanggan:</span> <strong class="text-info">{{ $diambil }} Unit</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                <span>Sedang Dalam Proses:</span> <strong class="text-warning">{{ $proses }} Unit</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                <span>Klaim Garansi / Batal:</span> <strong class="text-danger">{{ $garansi + $batal }} Unit</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-2 text-dark">10 Jenis Kerusakan / Keluhan Terbanyak</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($kerusakan_terbanyak as $keluhan => $cnt)
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                    <span class="text-truncate me-2" style="max-width: 250px;">{{ $keluhan ?: 'Keluhan Servis Umum' }}</span>
                                    <span class="badge bg-secondary rounded-pill">{{ $cnt }} unit</span>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted text-center py-2">Belum ada catatan kerusakan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode TRX</th>
                            <th width="20%">Nama Barang</th>
                            <th width="25%">Keluhan</th>
                            <th width="15%">Estimasi Biaya</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($servis as $s)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-bold text-primary">{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                                <td>{{ $s->nama_barang ?? 'Servis Komputer/Laptop' }}</td>
                                <td>{{ $s->keluhan }}</td>
                                <td class="text-end">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark text-uppercase">{{ $s->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada riwayat servis pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($tipe === 'servis-rekap')
            <div class="table-responsive mb-4">
                <h6 class="fw-bold text-dark mb-2">Statistik Produktivitas & Bagi Hasil Teknisi</h6>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Nama Teknisi</th>
                            <th class="text-center">Total Unit</th>
                            <th class="text-center">Selesai</th>
                            <th class="text-center">Proses</th>
                            <th class="text-end">Total Omzet Servis</th>
                            <th class="text-end">Upah Teknisi</th>
                            <th class="text-end">Untung Bersih Toko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teknisi_stats as $ts)
                            <tr>
                                <td class="fw-bold text-dark">{{ $ts['name'] }}</td>
                                <td class="text-center">{{ $ts['total_unit'] }} unit</td>
                                <td class="text-center text-success fw-bold">{{ $ts['selesai'] }}</td>
                                <td class="text-center text-warning fw-bold">{{ $ts['proses'] }}</td>
                                <td class="text-end">Rp {{ number_format($ts['estimasi_revenue'], 0, ',', '.') }}</td>
                                <td class="text-end text-primary fw-bold">Rp {{ number_format($ts['estimasi_upah'], 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($ts['estimasi_keuntungan'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">Belum ada statistik teknisi pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <h6 class="fw-bold text-dark mb-2">Daftar Detail Servis Unit</h6>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode TRX</th>
                            <th width="18%">Teknisi</th>
                            <th width="20%">Barang</th>
                            <th width="14%" class="text-end">Biaya Total</th>
                            <th width="14%" class="text-end">Upah Teknisi</th>
                            <th width="14%" class="text-end">Untung Toko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($servis as $s)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-bold text-primary">{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                                <td>{{ $s->teknisi->name ?? 'Belum Ditugaskan' }}</td>
                                <td>{{ $s->nama_barang ?? 'Unit Servis' }}</td>
                                <td class="text-end">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</td>
                                <td class="text-end text-primary">Rp {{ number_format($s->upah_teknisi, 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($s->keuntungan_toko, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada rincian data servis pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($servis->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                            <td class="text-end">Rp {{ number_format($total_estimasi_biaya, 0, ',', '.') }}</td>
                            <td class="text-end text-primary">Rp {{ number_format($total_upah_teknisi, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($total_keuntungan_toko, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

        @elseif($tipe === 'keuangan')
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="border rounded-4 p-4 bg-light shadow-xs">
                        <h5 class="fw-bold text-dark text-center border-bottom pb-3 mb-4">
                            <i class="bi bi-bank me-2 text-primary"></i>Ringkasan Arus Kas & Laba Rugi Usaha
                        </h5>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong class="text-dark">A. Pendapatan Penjualan Barang</strong><br>
                                <small class="text-muted">{{ $jumlah_transaksi_penjualan }} transaksi penjualan lunas</small>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</h6>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong class="text-dark">B. Pendapatan Jasa Servis</strong><br>
                                <small class="text-muted">{{ $jumlah_transaksi_servis }} transaksi servis lunas</small>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">Rp {{ number_format($total_servis, 0, ',', '.') }}</h6>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white rounded-3 px-3 my-2 border">
                            <strong class="text-primary fs-6">TOTAL PENDAPATAN KOTOR (A + B)</strong>
                            <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($total_keseluruhan, 0, ',', '.') }}</h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom text-danger">
                            <div>
                                <strong>C. Total HPP (Harga Pokok Penjualan Produk)</strong><br>
                                <small class="text-muted">Total modal pembelian barang yang terjual</small>
                            </div>
                            <h6 class="fw-bold mb-0">- Rp {{ number_format($total_hpp, 0, ',', '.') }}</h6>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-3 bg-success bg-opacity-10 rounded-3 px-3 mt-3 border border-success">
                            <div>
                                <strong class="text-success fs-5">ESTIMASI LABA KOTOR BERSIH</strong><br>
                                <small class="text-muted">Penjualan dikurangi HPP (Belum termasuk biaya operasional)</small>
                            </div>
                            <h4 class="fw-bold text-success mb-0">Rp {{ number_format($laba_kotor, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($tipe === 'metode-pembayaran')
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-4 rounded-4 bg-light border text-center">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-cash me-2"></i>METODE CASH (TUNAI)</h6>
                        <h3 class="fw-bold text-dark">Rp {{ number_format($total_cash, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">{{ $count_cash }} Transaksi ({{ $persen_cash_omzet }}% dari total omzet)</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded-4 bg-light border text-center">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-credit-card-2-front me-2"></i>METODE TRANSFER / BANK</h6>
                        <h3 class="fw-bold text-dark">Rp {{ number_format($total_transfer, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">{{ $count_transfer }} Transaksi ({{ $persen_transfer_omzet }}% dari total omzet)</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <h6 class="fw-bold text-dark mb-2">Sample Riwayat Transaksi Terbaru</h6>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Metode</th>
                            <th class="text-end">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi_cash->take(8) as $tc)
                            <tr>
                                <td class="fw-bold">{{ $tc->kode_transaksi }}</td>
                                <td>{{ $tc->created_at ? $tc->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td>{{ $tc->nama_pelanggan }}</td>
                                <td><span class="badge bg-success">CASH</span></td>
                                <td class="text-end fw-bold">Rp {{ number_format($tc->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                        @endforelse
                        @forelse($transaksi_transfer->take(8) as $tt)
                            <tr>
                                <td class="fw-bold">{{ $tt->kode_transaksi }}</td>
                                <td>{{ $tt->created_at ? $tt->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td>{{ $tt->nama_pelanggan }}</td>
                                <td><span class="badge bg-primary">TRANSFER</span></td>
                                <td class="text-end fw-bold">Rp {{ number_format($tt->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($tipe === 'komplain')
            <div class="table-responsive mb-4">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-robot me-1 text-primary"></i>Keluhan & Komplain via Chatbot AI ({{ $total_komplain }} Chat)</h6>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="20%">Pengguna</th>
                            <th width="30%">Pesan Masukan / Komplain</th>
                            <th width="30%">Respon Chatbot AI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($logs as $l)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $l->created_at ? $l->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td>{{ $l->user->name ?? 'Pengunjung Tamu' }}</td>
                                <td><span class="text-danger fw-semibold">{{ $l->pesan }}</span></td>
                                <td class="text-muted small">{{ Str::limit($l->jawaban, 100) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Tidak ada riwayat komplain chatbot pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($tipe === 'chatbot-analitik')
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-2 text-dark">Kategori Pertanyaan Paling Banyak Ditanyakan</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($kategori_pertanyaan->take(6) as $kat => $cnt)
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                    <span>{{ $kat ?: 'Umum / Lainnya' }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $cnt }} chat</span>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted text-center py-2">Belum ada data percakapan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-2 text-dark">Aktivitas Percakapan Terbanyak Per Hari</h6>
                        <ul class="list-group list-group-flush bg-transparent">
                            @forelse($percakapan_per_hari->take(6) as $tgl => $cnt)
                                <li class="list-group-item d-flex justify-content-between bg-transparent px-0 py-1">
                                    <span>{{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}</span>
                                    <span class="badge bg-info text-dark rounded-pill">{{ $cnt }} pesan</span>
                                </li>
                            @empty
                                <li class="list-group-item bg-transparent text-muted text-center py-2">Belum ada data percakapan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <h6 class="fw-bold text-dark mb-2">Riwayat Percakapan Chatbot Terbaru</h6>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Waktu</th>
                            <th width="18%">Pengguna</th>
                            <th width="31%">Pertanyaan Pengguna</th>
                            <th width="31%">Jawaban AI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($logs->take(20) as $l)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $l->created_at ? $l->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td>{{ $l->user->name ?? 'Pengunjung Tamu' }}</td>
                                <td class="fw-medium text-dark">{{ $l->pesan }}</td>
                                <td class="text-muted small">{{ Str::limit($l->jawaban, 90) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada riwayat percakapan chatbot pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($tipe === 'margin')
            <div class="table-responsive">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Kategori</th>
                            <th width="30%">Nama Produk</th>
                            <th width="7%" class="text-center">Stok</th>
                            <th width="13%" class="text-end">Harga Beli</th>
                            <th width="13%" class="text-end">Harga Jual</th>
                            <th width="12%" class="text-end">Potensi Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($produk as $p)
                            @php $margin = ($p->harga_jual - $p->harga_beli) * $p->stok; @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                                <td class="fw-bold">{{ $p->nama_produk }}</td>
                                <td class="text-center">{{ $p->stok }}</td>
                                <td class="text-end">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($margin, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($produk->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                            <td class="text-end">Rp {{ number_format($total_harga_beli, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($total_harga_jual, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($total_margin, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        @endif

        <!-- TANDA TANGAN RESMI -->
        <div class="ttd-wrapper">
            <div class="ttd-box">
                Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y') }}<br>
                Mengetahui,<br>
                <strong>Pimpinan Toko NJK</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Nasrulia / Pemilik</div>
            </div>
        </div>

    </div>

    <!-- Script Kontrol Input Filter Interaktif -->
    <script>
        function toggleFilterInputs() {
            const mode = document.getElementById('filter_type').value;
            document.querySelectorAll('.filter-input-group').forEach(el => el.style.display = 'none');

            if (mode === 'harian') {
                const el = document.getElementById('input-harian');
                if (el) el.style.display = 'block';
            } else if (mode === 'bulanan') {
                const el = document.getElementById('input-bulanan');
                if (el) el.style.display = 'block';
            } else if (mode === 'tahunan') {
                const el = document.getElementById('input-tahunan');
                if (el) el.style.display = 'block';
            } else if (mode === 'custom') {
                const el = document.getElementById('input-custom');
                if (el) el.style.display = 'block';
            }
        }
    </script>
</x-app-layout>

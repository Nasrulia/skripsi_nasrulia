<x-app-layout>
    <style>
        #main-tabs .nav-link {
            border-bottom: 3px solid transparent !important;
            transition: all 0.3s ease;
            color: #6c757d;
        }
        #main-tabs .nav-link.active {
            border-bottom: 3px solid #0d6efd !important;
            background-color: transparent !important;
            color: #0d6efd !important;
        }
        #main-tabs .nav-link:not(.active):hover {
            border-bottom: 3px solid #dee2e6 !important;
            color: #495057;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Kelola Transaksi Kasir</h3>
            <p class="text-muted mb-0">Periksa detail pesanan dan konfirmasi pembayaran pelanggan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tab Utama: Pemisahan Penjualan dan Servis -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="main-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2 border-0" id="penjualan-tab" data-bs-toggle="tab" data-bs-target="#pills-penjualan" type="button" role="tab" aria-controls="pills-penjualan" aria-selected="true" style="font-size: 1.05rem; border-radius: 0;">
                <i class="bi bi-cart-check-fill me-2"></i> Transaksi Penjualan
                <span class="badge bg-primary bg-opacity-10 text-primary ms-2 rounded-pill" style="font-size: 0.8rem;">{{ $transaksi->where('tipe', 'penjualan')->count() }}</span>
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 border-0" id="servis-tab" data-bs-toggle="tab" data-bs-target="#pills-servis" type="button" role="tab" aria-controls="pills-servis" aria-selected="false" style="font-size: 1.05rem; border-radius: 0;">
                <i class="bi bi-tools me-2"></i> Transaksi Servis
                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2 rounded-pill" style="font-size: 0.8rem;">{{ $transaksi->where('tipe', 'servis')->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="main-tabs-content">
        <!-- 1. TAB TRANSAKSI PENJUALAN -->
        <div class="tab-pane fade show active" id="pills-penjualan" role="tabpanel" aria-labelledby="penjualan-tab" tabindex="0">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="penjualan-status-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 fw-bold shadow-sm" id="pills-penjualan-pending-tab" data-bs-toggle="pill" data-bs-target="#pills-penjualan-pending" type="button" role="tab" aria-controls="pills-penjualan-pending" aria-selected="true">
                            <i class="bi bi-clock-history me-1"></i> Pesanan Baru
                            <span class="badge bg-warning text-dark ms-2 rounded-circle">{{ $transaksi->where('tipe', 'penjualan')->where('status', '!=', 'Lunas')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link rounded-pill px-4 fw-bold shadow-sm text-success bg-success bg-opacity-10" id="pills-penjualan-lunas-tab" data-bs-toggle="pill" data-bs-target="#pills-penjualan-lunas" type="button" role="tab" aria-controls="pills-penjualan-lunas" aria-selected="false">
                            <i class="bi bi-check-circle-fill me-1"></i> Riwayat Lunas
                            <span class="badge bg-success ms-2 rounded-circle">{{ $transaksi->where('tipe', 'penjualan')->where('status', 'Lunas')->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="penjualan-status-tabContent">
                    <!-- Pesanan Baru Penjualan -->
                    <div class="tab-pane fade show active" id="pills-penjualan-pending" role="tabpanel" aria-labelledby="pills-penjualan-pending-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode TRX</th>
                                        <th>Pelanggan</th>
                                        <th>Total Bayar</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi Kasir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi->where('tipe', 'penjualan')->where('status', '!=', 'Lunas') as $t)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $t->kode_transaksi }}</td>
                                        <td class="fw-semibold text-dark">
                                            {{ $t->nama_pelanggan }}
                                            <div class="mt-1" style="font-size: 0.75rem;">
                                                @if($t->metode_pengambilan == 'diambil')
                                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><i class="bi bi-shop"></i> Ambil di Toko</span>
                                                    @if($t->metode_pembayaran == 'cash')
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1"><i class="bi bi-cash"></i> Cash</span>
                                                    @else
                                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="bi bi-credit-card"></i> Transfer</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-dark bg-opacity-10 text-dark px-2 py-1"><i class="bi bi-truck"></i> Dikirim</span>
                                                @endif
                                                @if($t->nominal_dp > 0)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="bi bi-wallet2"></i> DP: Rp {{ number_format($t->nominal_dp, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="fw-semibold">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Pending</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-3 me-1 px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                            <form action="{{ route('transaksi.konfirmasi', $t->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3" onclick="return confirm('Konfirmasi pembayaran Lunas untuk pesanan {{ $t->kode_transaksi }}?')">
                                                    <i class="bi bi-check2-all"></i> Konfirmasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <h6 class="text-muted fw-normal">Yey! Belum ada antrean pesanan baru.</h6>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Lunas Penjualan -->
                    <div class="tab-pane fade" id="pills-penjualan-lunas" role="tabpanel" aria-labelledby="pills-penjualan-lunas-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode TRX</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal Lunas</th>
                                        <th>Total Bayar</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi->where('tipe', 'penjualan')->where('status', 'Lunas') as $t)
                                    <tr>
                                        <td class="fw-bold text-success">{{ $t->kode_transaksi }}</td>
                                        <td class="fw-semibold text-dark">
                                            {{ $t->nama_pelanggan }}
                                            <div class="mt-1" style="font-size: 0.75rem;">
                                                @if($t->metode_pengambilan == 'diambil')
                                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><i class="bi bi-shop"></i> Ambil di Toko</span>
                                                    @if($t->metode_pembayaran == 'cash')
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1"><i class="bi bi-cash"></i> Cash</span>
                                                    @else
                                                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="bi bi-credit-card"></i> Transfer</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-dark bg-opacity-10 text-dark px-2 py-1"><i class="bi bi-truck"></i> Dikirim</span>
                                                @endif
                                                @if($t->nominal_dp > 0)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="bi bi-wallet2"></i> DP: Rp {{ number_format($t->nominal_dp, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($t->updated_at)->format('d M Y H:i') }}</td>
                                        <td class="fw-semibold">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                                <i class="bi bi-receipt"></i> Cek Nota
                                            </button>
                                            <a href="{{ route('transaksi.invoice', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-3 px-3">
                                                <i class="bi bi-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <h6 class="text-muted fw-normal">Belum ada transaksi yang lunas.</h6>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TAB TRANSAKSI SERVIS -->
        <div class="tab-pane fade" id="pills-servis" role="tabpanel" aria-labelledby="servis-tab" tabindex="0">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="servis-status-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 fw-bold shadow-sm" id="pills-servis-pending-tab" data-bs-toggle="pill" data-bs-target="#pills-servis-pending" type="button" role="tab" aria-controls="pills-servis-pending" aria-selected="true">
                            <i class="bi bi-clock-history me-1"></i> Antrean Servis (Baru)
                            <span class="badge bg-warning text-dark ms-2 rounded-circle">{{ $transaksi->where('tipe', 'servis')->where('status', '!=', 'Lunas')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item ms-2" role="presentation">
                        <button class="nav-link rounded-pill px-4 fw-bold shadow-sm text-success bg-success bg-opacity-10" id="pills-servis-lunas-tab" data-bs-toggle="pill" data-bs-target="#pills-servis-lunas" type="button" role="tab" aria-controls="pills-servis-lunas" aria-selected="false">
                            <i class="bi bi-check-circle-fill me-1"></i> Servis Lunas
                            <span class="badge bg-success ms-2 rounded-circle">{{ $transaksi->where('tipe', 'servis')->where('status', 'Lunas')->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="servis-status-tabContent">
                    <!-- Pesanan Baru Servis -->
                    <div class="tab-pane fade show active" id="pills-servis-pending" role="tabpanel" aria-labelledby="pills-servis-pending-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode TRX</th>
                                        <th>Pelanggan</th>
                                        <th>Barang Servis</th>
                                        <th>Total Bayar</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi Kasir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi->where('tipe', 'servis')->where('status', '!=', 'Lunas') as $t)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $t->kode_transaksi }}</td>
                                        <td class="fw-semibold text-dark">{{ $t->nama_pelanggan }}</td>
                                        <td class="fw-semibold text-dark">{{ $t->servisDetail->first()->nama_barang ?? '-' }}</td>
                                        <td class="fw-semibold">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Pending</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-3 me-1 px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                            <form action="{{ route('transaksi.konfirmasi', $t->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3" onclick="return confirm('Konfirmasi pembayaran Lunas untuk servis {{ $t->kode_transaksi }}?')">
                                                    <i class="bi bi-check2-all"></i> Konfirmasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <h6 class="text-muted fw-normal">Yey! Belum ada antrean servis baru.</h6>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Lunas Servis -->
                    <div class="tab-pane fade" id="pills-servis-lunas" role="tabpanel" aria-labelledby="pills-servis-lunas-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode TRX</th>
                                        <th>Pelanggan</th>
                                        <th>Barang Servis</th>
                                        <th>Tanggal Lunas</th>
                                        <th>Total Bayar</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi->where('tipe', 'servis')->where('status', 'Lunas') as $t)
                                    <tr>
                                        <td class="fw-bold text-success">{{ $t->kode_transaksi }}</td>
                                        <td class="fw-semibold text-dark">{{ $t->nama_pelanggan }}</td>
                                        <td class="fw-semibold text-dark">{{ $t->servisDetail->first()->nama_barang ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($t->updated_at)->format('d M Y H:i') }}</td>
                                        <td class="fw-semibold">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $t->id }}">
                                                <i class="bi bi-receipt"></i> Cek Nota
                                            </button>
                                            <a href="{{ route('transaksi.invoice', $t->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-3 px-3">
                                                <i class="bi bi-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                                            <h6 class="text-muted fw-normal">Belum ada transaksi servis yang lunas.</h6>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($transaksi as $t)
    <div class="modal fade" id="modalDetail{{ $t->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pesanan: <span class="text-primary">{{ $t->kode_transaksi }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <small class="text-muted d-block">Nama Pelanggan:</small>
                            <span class="fw-bold text-dark">{{ $t->nama_pelanggan }}</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Tanggal Pemesanan:</small>
                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    @if($t->tipe == 'servis')
                        @php $s = $t->servisDetail->first(); @endphp
                        @if($s)
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Barang Servis:</small>
                                <span class="fw-bold text-dark fs-5">{{ $s->nama_barang ?? '-' }}</span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted d-block">Status Pengerjaan:</small>
                                @if($s->status == 'proses')
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">Sedang Diproses</span>
                                @elseif($s->status == 'selesai')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Selesai (Siap Diambil)</span>
                                @elseif($s->status == 'diambil')
                                    <span class="badge bg-info px-3 py-2 rounded-pill">Sudah Diambil</span>
                                @elseif($s->status == 'garansi')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Garansi</span>
                                @elseif($s->status == 'batal')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">Batal</span>
                                @endif
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4 p-4 mb-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Layanan / Jasa:</small>
                                    <span class="fw-semibold text-dark">{{ $s->jasaServis->nama_jasa ?? 'Custom Servis' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Estimasi Waktu:</small>
                                    <span class="fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i> {{ $s->estimasi_waktu ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Kendala Awal:</small>
                                    <span class="fw-semibold text-dark">{{ $s->keluhan }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Teknisi Penanggung Jawab:</small>
                                    <span class="fw-semibold text-dark"><i class="bi bi-person-badge me-1"></i> {{ $s->teknisi->name ?? 'Mengantre' }}</span>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Metode Pembayaran:</small>
                                    <span class="fw-bold text-dark text-uppercase">{{ $t->metode_pembayaran == 'cash' ? 'Cash di Toko' : 'Transfer Bank' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Estimasi Biaya Servis:</small>
                                    <span class="fw-bold text-primary fs-5">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($s->catatan_teknisi)
                        <div class="alert alert-info border-0 rounded-4 p-3 mb-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-chat-left-text-fill me-2"></i>Catatan Perkembangan Servis (Teknisi):</h6>
                            <p class="mb-0 text-dark small">{{ $s->catatan_teknisi }}</p>
                            <small class="text-muted d-block text-end mt-1" style="font-size: 0.75rem;">Terakhir diperbarui: {{ $s->updated_at->diffForHumans() }}</small>
                        </div>
                        @endif
                        @endif
                    @else
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Harga Satuan</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($t->detail as $d)
                                    <tr class="border-bottom">
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $d->produk->nama_produk ?? 'Produk Dihapus' }}</div>
                                        </td>
                                        <td class="text-center text-muted">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center fw-bold">{{ $d->jumlah }}</td>
                                        <td class="text-end fw-semibold text-primary">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3">TOTAL KESELURUHAN:</td>
                                        <td class="text-end fw-bold text-primary fs-5 py-3">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                    
                    @if($t->metode_pengambilan == 'diantar' && $t->alamat_pengiriman)
                    <div class="alert alert-info border-0 mt-3 mb-0 rounded-3">
                        <small class="fw-bold d-block"><i class="bi bi-truck me-1"></i> Informasi Pengiriman:</small>
                        <small class="text-dark">Ekspedisi: {{ $t->ekspedisi->nama_ekspedisi ?? '-' }} | Jarak: {{ $t->jarak_km ?? '-' }} km | Ongkir: Rp {{ number_format($t->ongkir, 0, ',', '.') }}</small>
                        <small class="text-dark d-block mb-1">Alamat: {{ $t->alamat_pengiriman }}</small>
                        @if($t->no_resi)
                        <small class="text-success d-block fw-bold mt-1"><i class="bi bi-tag-fill me-1"></i> Nomor Resi: {{ $t->no_resi }}</small>
                        @endif
                        @if($t->status_pengiriman)
                        <small class="text-secondary d-block fw-bold mt-1"><i class="bi bi-info-circle-fill me-1"></i> Status Pengiriman: 
                            @if($t->status_pengiriman == 'diproses')
                                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Diproses</span>
                            @elseif($t->status_pengiriman == 'dikirim')
                                <span class="badge bg-primary px-2 py-1 rounded-pill">Dikirim</span>
                            @elseif($t->status_pengiriman == 'diterima')
                                <span class="badge bg-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Diterima (Selesai)</span>
                            @endif
                        </small>
                        @endif
                    </div>

                    <div class="card border-0 bg-light mt-3 p-3 rounded-3">
                        <form action="{{ route('transaksi.update-resi', $t->id) }}" method="POST">
                            @csrf
                            <label class="form-label fw-bold small text-dark"><i class="bi bi-pencil-square me-1"></i> {{ $t->no_resi ? 'Update' : 'Input' }} Nomor Resi Pengiriman</label>
                            <div class="input-group">
                                <input type="text" name="no_resi" class="form-control form-control-sm" placeholder="Contoh: REG123456789" value="{{ $t->no_resi }}" required>
                                <button type="submit" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    @elseif($t->metode_pengambilan == 'diambil')
                    <div class="alert alert-success border-0 mt-3 mb-0 rounded-3">
                        <small class="fw-bold d-block"><i class="bi bi-shop me-1"></i> Metode Pengambilan: Ambil di Toko</small>
                        <small class="text-dark d-block">Metode Pembayaran: <strong>{{ $t->metode_pembayaran == 'cash' ? 'Cash di Toko' : 'Transfer Bank' }}</strong></small>
                        @if($t->estimasi_diambil)
                        <small class="text-dark d-block">Estimasi Pengambilan: {{ \Carbon\Carbon::parse($t->estimasi_diambil)->format('d M Y H:i') }}</small>
                        @endif
                        @if($t->metode_pembayaran == 'cash' && $t->batas_waktu_pengambilan)
                        <small class="text-danger d-block fw-bold"><i class="bi bi-calendar-x me-1"></i> Batas Waktu Pengambilan: {{ \Carbon\Carbon::parse($t->batas_waktu_pengambilan)->format('d M Y H:i') }}</small>
                        @endif
                        @if($t->nominal_dp > 0)
                        <small class="text-dark d-block">Down Payment (DP) Wajib: Rp {{ number_format($t->nominal_dp, 0, ',', '.') }}</small>
                        <small class="text-dark d-block">Sisa Pelunasan: Rp {{ number_format($t->total_bayar - $t->nominal_dp, 0, ',', '.') }}</small>
                        @endif
                    </div>
                    @endif

                    @if($t->bukti_bayar)
                    <div class="mt-3 text-center">
                        <small class="fw-bold d-block mb-2">Bukti Pembayaran:</small>
                        <img src="{{ asset('storage/' . $t->bukti_bayar) }}" class="rounded-3 shadow-sm" style="max-height: 200px; object-fit: contain; cursor: pointer;" onclick="window.open(this.src)" alt="Bukti Bayar">
                    </div>
                    @endif

                    @if($t->status == 'Lunas')
                    <div class="text-center mt-4">
                        <span class="badge bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill fs-6"><i class="bi bi-check-circle-fill me-2"></i>Status: LUNAS</span>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <a href="{{ route('transaksi.invoice', $t->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4 me-2">
                        <i class="bi bi-file-pdf me-1"></i> Cetak Invoice PDF
                    </a>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

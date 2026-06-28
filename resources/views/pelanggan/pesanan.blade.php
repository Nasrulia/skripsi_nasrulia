<x-app-layout>
    <style>
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Pesanan Saya</h3>
            <p class="text-muted mb-0">Pantau status pesanan dan detail transaksi Anda di sini.</p>
        </div>
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-shop me-1"></i> Belanja Lagi
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="18%">Kode Transaksi</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Total Belanja</th>
                        <th width="22%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td class="fw-bold text-primary">{{ $p->kode_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}</td>
                        <td class="fw-semibold">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($p->status == 'Lunas')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Lunas & Diproses</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Menunggu Konfirmasi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id }}">
                                <i class="bi bi-eye me-1"></i> Detail
                            </button>
                            @if($p->status == 'Lunas')
                            <a href="{{ route('pesanan.invoice', $p->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-file-pdf"></i> Invoice
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-bag-x fs-1 text-muted opacity-50 d-block mb-3"></i>
                            <h6 class="text-muted fw-normal">Belum ada riwayat pesanan.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($pesanan as $p)
    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pesanan: <span class="text-primary">{{ $p->kode_transaksi }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($p->tipe == 'servis')
                        @php $s = $p->servisDetail->first(); @endphp
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
                                    <span class="fw-bold text-dark text-uppercase">{{ $p->metode_pembayaran == 'cash' ? 'Cash di Toko' : 'Transfer Bank' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Estimasi Biaya Servis:</small>
                                    <span class="fw-bold text-primary fs-5">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @if($p->status != 'Lunas')
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted fw-semibold">Ubah Pilihan Pembayaran:</small>
                                <form action="{{ route('cek-servis.ubah-metode', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($p->metode_pembayaran == 'transfer')
                                        <input type="hidden" name="metode_pembayaran" value="cash">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-cash me-1"></i> Pilih Cash di Toko
                                        </button>
                                    @else
                                        <input type="hidden" name="metode_pembayaran" value="transfer">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-credit-card me-1"></i> Pilih Transfer Bank
                                        </button>
                                    @endif
                                </form>
                            </div>
                            @endif
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
                                    @foreach($p->detail as $d)
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
                                        <td class="text-end fw-bold text-primary fs-5 py-3">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                    
                    @if($p->metode_pengambilan == 'diantar' && $p->alamat_pengiriman)
                    <div class="alert alert-info border-0 mt-3 mb-3 d-flex align-items-center rounded-3">
                        <i class="bi bi-truck fs-4 me-3 text-info"></i>
                        <div>
                            <small class="fw-bold d-block">Pengiriman:</small>
                            <small class="text-dark">{{ $p->ekspedisi->nama_ekspedisi ?? 'Ekspedisi' }} - Rp {{ number_format($p->ongkir, 0, ',', '.') }} ({{ $p->jarak_km }} km)</small>
                            <small class="text-dark d-block">Alamat: {{ $p->alamat_pengiriman }}</small>
                            @if($p->no_resi)
                            <small class="text-success d-block fw-bold mt-1"><i class="bi bi-tag-fill me-1"></i> Nomor Resi: {{ $p->no_resi }}</small>
                            @endif
                        </div>
                    </div>

                    @if($p->no_resi)
                    {{-- Visual Stepper Timeline --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-light">
                        <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-geo-alt-fill text-danger me-2"></i> Lacak Status Paket</h6>
                        
                        <div class="d-flex justify-content-between position-relative mb-5" style="z-index: 1;">
                            <div class="position-absolute start-0 end-0 top-50 translate-middle-y bg-secondary bg-opacity-25" style="height: 4px; z-index: -1;"></div>
                            <div class="position-absolute start-0 top-50 translate-middle-y bg-success" style="width: {{ $p->status_pengiriman == 'diterima' ? '100' : ($p->status_pengiriman == 'dikirim' ? '66' : '0') }}%; height: 4px; z-index: -1;"></div>
                            
                            {{-- Step 1: Lunas --}}
                            <div class="text-center" style="width: 25%;">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 35px; height: 35px;">
                                    <i class="bi bi-check-lg fs-5"></i>
                                </div>
                                <span class="d-block small fw-bold text-dark mt-2" style="font-size: 0.8rem;">Lunas</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($p->updated_at)->format('d M H:i') }}</small>
                            </div>
                            
                            {{-- Step 2: Dikirim --}}
                            <div class="text-center" style="width: 25%;">
                                <div class="{{ in_array($p->status_pengiriman, ['dikirim', 'diterima']) ? 'bg-success text-white' : 'bg-white border text-secondary' }} rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 35px; height: 35px;">
                                    <i class="bi bi-box-seam-fill fs-6"></i>
                                </div>
                                <span class="d-block small {{ in_array($p->status_pengiriman, ['dikirim', 'diterima']) ? 'fw-bold text-dark' : 'fw-semibold text-muted' }} mt-2" style="font-size: 0.8rem;">Dikirim</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ in_array($p->status_pengiriman, ['dikirim', 'diterima']) ? \Carbon\Carbon::parse($p->updated_at)->format('d M H:i') : 'Menunggu Resi' }}</small>
                            </div>
                            
                            {{-- Step 3: Dalam Perjalanan --}}
                            <div class="text-center" style="width: 25%;">
                                <div class="{{ in_array($p->status_pengiriman, ['dikirim', 'diterima']) ? 'bg-success text-white' : 'bg-white border text-secondary' }} rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 35px; height: 35px; {{ $p->status_pengiriman == 'dikirim' ? 'animation: pulse 2s infinite;' : '' }}">
                                    <i class="bi bi-truck fs-5"></i>
                                </div>
                                <span class="d-block small {{ in_array($p->status_pengiriman, ['dikirim', 'diterima']) ? 'fw-bold text-dark' : 'fw-semibold text-muted' }} mt-2" style="font-size: 0.8rem;">Perjalanan</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->status_pengiriman == 'diterima' ? 'Selesai' : ($p->status_pengiriman == 'dikirim' ? 'Dalam Perjalanan' : 'Menunggu Pengiriman') }}</small>
                            </div>
                            
                            {{-- Step 4: Diterima --}}
                            <div class="text-center" style="width: 25%;">
                                <div class="{{ $p->status_pengiriman == 'diterima' ? 'bg-success text-white' : 'bg-white border text-secondary' }} rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 35px; height: 35px;">
                                    <i class="bi bi-house-door-fill fs-6"></i>
                                </div>
                                <span class="d-block small {{ $p->status_pengiriman == 'diterima' ? 'fw-bold text-dark' : 'fw-semibold text-muted' }} mt-2" style="font-size: 0.8rem;">Diterima</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->status_pengiriman == 'diterima' ? 'Diterima Pelanggan' : 'Sampai di Alamat' }}</small>
                            </div>
                        </div>

                        {{-- Simulated Live Tracking Details Logs --}}
                        <div class="border-start border-success border-2 ps-3 ms-2 text-start">
                            @if($p->status_pengiriman == 'diterima')
                            <div class="mb-3 position-relative">
                                <div class="position-absolute bg-success rounded-circle" style="width: 10px; height: 10px; left: -21px; top: 6px;"></div>
                                <span class="fw-bold small text-dark d-block" style="font-size: 0.85rem;">Paket telah diterima oleh penerima</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($p->updated_at)->format('d M Y, H:i') }} | Pesanan selesai dikonfirmasi oleh Anda.</small>
                            </div>
                            @endif

                            @if(in_array($p->status_pengiriman, ['dikirim', 'diterima']))
                            <div class="mb-3 position-relative">
                                <div class="position-absolute {{ $p->status_pengiriman == 'dikirim' ? 'bg-success' : 'bg-secondary' }} rounded-circle" style="width: {{ $p->status_pengiriman == 'dikirim' ? '10px' : '8px' }}; height: {{ $p->status_pengiriman == 'dikirim' ? '10px' : '8px' }}; left: {{ $p->status_pengiriman == 'dikirim' ? '-21px' : '-20px' }}; top: 6px;"></div>
                                <span class="fw-bold small {{ $p->status_pengiriman == 'dikirim' ? 'text-dark' : 'text-muted' }} d-block" style="font-size: 0.85rem;">Paket sedang dibawa kurir menuju alamat penerima</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($p->updated_at)->format('d M Y, H:i') }} | Kurir {{ $p->ekspedisi->nama_ekspedisi ?? '' }} dalam perjalanan mengirim paket Anda.</small>
                            </div>
                            <div class="mb-3 position-relative">
                                <div class="position-absolute bg-secondary rounded-circle" style="width: 8px; height: 8px; left: -20px; top: 6px;"></div>
                                <span class="fw-bold small text-muted d-block" style="font-size: 0.85rem;">Paket keluar dari Hub Sortir Nusantara Jaya Komputer</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($p->updated_at)->subMinutes(15)->format('d M Y, H:i') }} | Paket disiapkan untuk pengantar ekspedisi.</small>
                            </div>
                            <div class="position-relative">
                                <div class="position-absolute bg-secondary rounded-circle" style="width: 8px; height: 8px; left: -20px; top: 6px;"></div>
                                <span class="fw-bold small text-muted d-block" style="font-size: 0.85rem;">Paket berhasil diserahkan ke ekspedisi</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($p->updated_at)->subMinutes(30)->format('d M Y, H:i') }} | Nomor resi *{{ $p->no_resi }}* diinput oleh Admin.</small>
                            </div>
                            @else
                            <div class="position-relative">
                                <div class="position-absolute bg-success rounded-circle" style="width: 10px; height: 10px; left: -21px; top: 6px;"></div>
                                <span class="fw-bold small text-dark d-block" style="font-size: 0.85rem;">Paket sedang dipersiapkan untuk pengiriman</span>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($p->updated_at)->format('d M Y, H:i') }} | Menunggu penyerahan paket ke pihak ekspedisi.</small>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($p->status_pengiriman == 'dikirim')
                    <div class="mt-3 mb-3 text-center">
                        <form action="{{ route('transaksi.diterima', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan sudah sampai dan diterima dengan baik?')">
                            @csrf
                            <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm w-100 py-2">
                                <i class="bi bi-house-door-fill me-1"></i> Konfirmasi Pesanan Diterima
                            </button>
                        </form>
                    </div>
                    @elseif($p->status_pengiriman == 'diterima')
                    <div class="alert alert-success border-0 mt-3 mb-3 d-flex align-items-center rounded-3 shadow-sm">
                        <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                        <div>
                            <small class="fw-bold d-block text-success">Pesanan Selesai / Diterima</small>
                            <small class="text-dark">Terima kasih! Anda telah mengonfirmasi bahwa pesanan ini telah diterima dengan baik.</small>
                        </div>
                    </div>
                    @endif
                    @endif
                    @elseif($p->metode_pengambilan == 'diambil')
                    <div class="alert alert-success border-0 mt-3 mb-0 d-flex align-items-center rounded-3">
                        <i class="bi bi-shop fs-4 me-3 text-success"></i>
                        <small class="text-dark">Metode: <strong>Ambil di Toko</strong></small>
                    </div>
                    @endif

                    @if($p->status != 'Lunas')
                        @if($p->bukti_bayar)
                        <div class="alert alert-info border-0 mt-3 mb-0 d-flex align-items-center rounded-3">
                            <i class="bi bi-check-circle-fill fs-4 me-3 text-info"></i>
                            <small class="text-dark">Bukti pembayaran sudah diupload. Menunggu konfirmasi admin.</small>
                        </div>
                        <div class="text-center mt-3">
                            <img src="{{ asset('storage/' . $p->bukti_bayar) }}" class="rounded-3 shadow-sm" style="max-height: 200px; object-fit: contain;" alt="Bukti Bayar">
                        </div>
                        @else
                        <div class="alert alert-warning border-0 mt-3 mb-0 d-flex align-items-center rounded-3">
                            <i class="bi bi-info-circle-fill fs-4 me-3 text-warning"></i>
                            <small class="text-dark">Silakan upload bukti pembayaran untuk mempercepat konfirmasi.</small>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('pembayaran.form', $p->id) }}" class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-upload me-1"></i> Upload Bukti Bayar
                            </a>
                        </div>
                        @endif
                    @else
                    <div class="text-center mt-3">
                        <span class="badge bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill fs-6"><i class="bi bi-check-circle-fill me-2"></i>Status: LUNAS</span>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <a href="{{ route('pesanan.invoice', $p->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4 me-2">
                        <i class="bi bi-file-pdf me-1"></i> Cetak Invoice PDF
                    </a>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>

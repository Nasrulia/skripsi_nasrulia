<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
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

            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo NJK" class="img-fluid rounded-circle shadow-sm mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <h4 class="fw-bold text-primary mb-1">Lacak Progres Servis Anda</h4>
                        <p class="text-muted small">Nusantara Jaya Computer</p>
                    </div>

                    <form method="POST" action="{{ route('cek-servis.post') }}" class="mb-2">
                        @csrf
                        <label for="kode_transaksi" class="form-label fw-semibold text-muted small">Masukkan Nomor Transaksi Anda</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-receipt"></i></span>
                            <input type="text" name="kode_transaksi" id="kode_transaksi" class="form-control" placeholder="Contoh: TRX-SRV-xxxxxxxxxx" value="{{ $kode_transaksi ?? old('kode_transaksi') }}" required>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Cari</button>
                        </div>
                        <small class="text-muted d-block mt-2">Gunakan nomor transaksi (kode transaksi) yang tertera di tanda terima servis Anda.</small>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold"><i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login</a>
                    </div>

                </div>
            </div>

            @if(isset($servis))
                <h5 class="fw-bold text-dark mb-3 mt-4"><i class="bi bi-search text-primary me-2"></i>Hasil Pencarian ({{ $servis->count() }})</h5>

                @forelse($servis as $s)
                    <div class="card border-0 shadow rounded-4 mb-4">
                        <div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Kode Transaksi:</span>
                                <span class="fw-bold text-primary ms-1">{{ $s->transaksi->kode_transaksi }}</span>
                            </div>
                            <div>
                                @if($s->status == 'proses')
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">Sedang Diproses</span>
                                @elseif($s->status == 'selesai')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Selesai (Siap Diambil)</span>
                                @elseif($s->status == 'diambil')
                                    <span class="badge bg-info px-3 py-2 rounded-pill">Sudah Diambil</span>
                                @elseif($s->status == 'garansi')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Dalam Garansi</span>
                                @elseif($s->status == 'batal')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Barang Servis:</small>
                                    <span class="fw-bold text-dark fs-5">{{ $s->nama_barang ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <small class="text-muted d-block">Tanggal Pendaftaran:</small>
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            
                            <hr class="my-3">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Kendala Awal:</small>
                                    <span class="fw-semibold text-dark">{{ $s->keluhan }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Estimasi Waktu Pengerjaan:</small>
                                    <span class="fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i>{{ $s->estimasi_waktu ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Estimasi Biaya Servis:</small>
                                    <span class="fw-bold text-primary fs-5">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Teknisi Penanggung Jawab:</small>
                                    <span class="fw-semibold text-dark"><i class="bi bi-person-badge me-1"></i>{{ $s->teknisi->name ?? 'Mengantre' }}</span>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Diterima Oleh:</small>
                                    <span class="fw-semibold text-dark"><i class="bi bi-person-check me-1"></i>{{ $s->penerima ?? '-' }}</span>
                                </div>
                            </div>
                            @if($s->catatan_teknisi)
                                <div class="alert alert-info border-0 rounded-3 mb-3">
                                    <small class="fw-bold d-block"><i class="bi bi-chat-left-text-fill me-1"></i> Catatan Perkembangan (Teknisi):</small>
                                    <p class="mb-0 text-dark" style="font-size: 0.9rem;">{{ $s->catatan_teknisi }}</p>
                                    <small class="text-muted d-block text-end mt-1" style="font-size: 0.75rem;">Terakhir diperbarui: {{ $s->updated_at->diffForHumans() }}</small>
                                </div>
                            @endif

                            <div class="card bg-light border-0 rounded-3 p-3">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-wallet2 me-2 text-primary"></i>Informasi Pembayaran</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Metode Pembayaran:</span>
                                    <span class="fw-bold text-uppercase">{{ $s->transaksi->metode_pembayaran == 'cash' ? 'Cash di Toko' : 'Transfer Bank' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Status Pembayaran:</span>
                                    @if($s->transaksi->status == 'Lunas')
                                        <span class="badge bg-success px-3 py-1 rounded-pill">Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Pending (Belum Bayar)</span>
                                    @endif
                                </div>

                                @if($s->transaksi->status != 'Lunas')
                                    <hr class="my-2">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <small class="text-muted fw-semibold">Ubah Pilihan Pembayaran:</small>
                                        <form action="{{ route('cek-servis.ubah-metode', $s->transaksi->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if($s->transaksi->metode_pembayaran == 'transfer')
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

                                    @if($s->transaksi->metode_pembayaran == 'transfer')
                                        @if($s->transaksi->bukti_bayar)
                                            <div class="text-center p-2">
                                                <small class="text-success fw-bold d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> Bukti transfer telah diunggah</small>
                                                <img src="{{ asset('storage/' . $s->transaksi->bukti_bayar) }}" class="rounded shadow-sm mt-1" style="max-height: 120px; object-fit: contain;">
                                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Menunggu konfirmasi kasir/admin untuk mencatat sebagai Lunas.</small>
                                            </div>
                                        @else
                                            <form action="{{ route('cek-servis.upload-pembayaran', $s->transaksi->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                @csrf
                                                <label class="form-label small fw-bold text-dark"><i class="bi bi-upload me-1 text-primary"></i> Unggah Bukti Pembayaran Transfer</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="file" name="bukti_bayar" class="form-control" accept="image/*" required>
                                                    <button type="submit" class="btn btn-primary fw-semibold">Upload</button>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format gambar (JPG, PNG, WebP), maks. 2MB.</small>
                                            </form>
                                        @endif
                                    @else
                                        <div class="alert alert-warning border-0 rounded-3 mb-0 py-2 mt-1">
                                            <small class="text-dark"><i class="bi bi-info-circle me-1"></i> Silakan lakukan pembayaran langsung di kasir toko saat mengambil barang Anda.</small>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if($s->transaksi->status == 'Lunas' || in_array($s->status, ['selesai', 'diambil', 'garansi']))
                                <div class="mt-3">
                                    <a href="{{ route('cek-servis.nota', $s->id) }}" target="_blank" class="btn btn-outline-danger w-100 rounded-3 fw-bold">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh Nota Transaksi (PDF)
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 text-center mt-3">
                        <i class="bi bi-info-circle fs-3 text-warning d-block mb-2"></i>
                        <h6 class="fw-bold mb-1">Data Servis Tidak Ditemukan</h6>
                        <span class="text-muted small">Nomor Transaksi <strong>{{ $kode_transaksi }}</strong> tidak ditemukan atau belum terdaftar dalam riwayat servis. Pastikan nomor transaksi yang dimasukkan sudah benar.</span>
                    </div>
                @endforelse
            @endif

        </div>
    </div>
</x-guest-layout>

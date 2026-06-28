<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Kelola Servis - Layanan Perbaikan</h3>
            <p class="text-muted mb-0">Pantau, daftarkan, dan perbarui perkembangan servis barang pelanggan.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalInputServis">
                <i class="bi bi-plus-lg me-1"></i> Input Servis Baru
            </button>
            <a href="{{ route('teknisi.semua-servis') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-list-ul me-1"></i> Semua Servis
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4 border-bottom pb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold" id="pills-masuk-tab" data-bs-toggle="pill" data-bs-target="#pills-masuk" type="button" role="tab" aria-selected="true">
                <i class="bi bi-box-arrow-in-down me-1"></i> Servis Masuk
                <span class="badge bg-warning text-dark ms-2 rounded-circle">{{ $servis->where('status', 'proses')->whereNull('teknisi_id')->count() }}</span>
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold" id="pills-proses-tab" data-bs-toggle="pill" data-bs-target="#pills-proses" type="button" role="tab" aria-selected="false">
                <i class="bi bi-tools me-1"></i> Sedang Dikerjakan
                <span class="badge bg-primary ms-2 rounded-circle">{{ $servis->where('status', 'proses')->whereNotNull('teknisi_id')->count() }}</span>
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold" id="pills-keluar-tab" data-bs-toggle="pill" data-bs-target="#pills-keluar" type="button" role="tab" aria-selected="false">
                <i class="bi bi-box-arrow-up me-1"></i> Servis Keluar / Selesai
                <span class="badge bg-success ms-2 rounded-circle">{{ $servis->whereIn('status', ['selesai', 'diambil', 'garansi', 'batal'])->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- 1. SERVIS MASUK (Tersedia / Unassigned) -->
        <div class="tab-pane fade show active" id="pills-masuk" role="tabpanel" tabindex="0">
            <div class="row g-3">
                @forelse($servis->where('status', 'proses')->whereNull('teknisi_id') as $s)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-warning border-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $s->nama_barang ?? $s->jasaServis->nama_jasa ?? 'Barang Servis' }}</h6>
                                    <small class="text-muted d-block">{{ $s->transaksi->kode_transaksi ?? '-' }}</small>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Masuk</span>
                            </div>
                            <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i> <strong>Pelanggan:</strong> {{ $s->transaksi->nama_pelanggan ?? '-' }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-person-check me-1"></i> <strong>Diterima Oleh:</strong> {{ $s->penerima ?? '-' }}</p>
                            @if($s->no_seri)
                            <p class="text-muted small mb-2"><i class="bi bi-hash me-1"></i> <strong>No. Seri:</strong> {{ $s->no_seri }}</p>
                            @endif
                            <p class="text-muted small mb-2"><i class="bi bi-chat-dots me-1"></i> <strong>Kendala:</strong> {{ Str::limit($s->keluhan, 80) }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-cash me-1"></i> <strong>Estimasi Biaya:</strong> Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</p>
                            <p class="text-muted small mb-3"><i class="bi bi-alarm me-1"></i> <strong>Waktu Pengerjaan:</strong> {{ $s->estimasi_waktu ?? '-' }}</p>
                            <div class="bg-light rounded-3 p-2 mb-3 small d-flex justify-content-between">
                                <span>Bayar: <strong class="text-uppercase">{{ $s->transaksi->metode_pembayaran ?? 'transfer' }}</strong></span>
                                <span>Status: <strong class="text-danger">{{ $s->transaksi->status ?? 'Pending' }}</strong></span>
                            </div>
                            @if(Auth::user()->peran == 'teknisi')
                            <form action="{{ route('teknisi.ambil', $s->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">
                                    <i class="bi bi-hand-index me-1"></i> Ambil Servis
                                </button>
                            </form>
                            @else
                            <button type="button" class="btn btn-outline-primary w-100 rounded-3 fw-semibold mb-2" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $s->id }}">
                                <i class="bi bi-arrow-up-circle me-1"></i> Update Status
                            </button>
                            @endif
                            <a href="{{ route('teknisi.servis.tanda-terima', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Tanda Terima (PDF)
                            </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-smile fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Tidak ada antrean servis baru masuk.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 2. SEDANG DIKERJAKAN -->
        <div class="tab-pane fade" id="pills-proses" role="tabpanel" tabindex="0">
            <div class="row g-3">
                @forelse($servis->where('status', 'proses')->whereNotNull('teknisi_id') as $s)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-primary border-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $s->nama_barang ?? $s->jasaServis->nama_jasa ?? 'Barang Servis' }}</h6>
                                    <small class="text-muted d-block">{{ $s->transaksi->kode_transaksi ?? '-' }}</small>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Proses</span>
                            </div>
                            <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i> <strong>Pelanggan:</strong> {{ $s->transaksi->nama_pelanggan ?? '-' }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-person-check me-1"></i> <strong>Diterima Oleh:</strong> {{ $s->penerima ?? '-' }}</p>
                            @if($s->no_seri)
                            <p class="text-muted small mb-2"><i class="bi bi-hash me-1"></i> <strong>No. Seri:</strong> {{ $s->no_seri }}</p>
                            @endif
                            <p class="text-muted small mb-2"><i class="bi bi-chat-dots me-1"></i> <strong>Kendala:</strong> {{ Str::limit($s->keluhan, 80) }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-person-badge me-1"></i> <strong>Teknisi:</strong> {{ $s->teknisi->name ?? '-' }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-cash me-1"></i> <strong>Estimasi Biaya:</strong> Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</p>
                            <p class="text-muted small mb-3"><i class="bi bi-alarm me-1"></i> <strong>Waktu Pengerjaan:</strong> {{ $s->estimasi_waktu ?? '-' }}</p>
                            <div class="bg-light rounded-3 p-2 mb-3 small d-flex justify-content-between">
                                <span>Bayar: <strong class="text-uppercase">{{ $s->transaksi->metode_pembayaran ?? 'transfer' }}</strong></span>
                                <span>Status: <strong class="{{ $s->transaksi->status == 'Lunas' ? 'text-success' : 'text-danger' }}">{{ $s->transaksi->status ?? 'Pending' }}</strong></span>
                            </div>

                            @if($s->catatan_teknisi)
                            <div class="bg-light rounded-3 p-2 mb-3">
                                <small class="text-muted fw-semibold">Catatan Terakhir:</small>
                                <small class="text-dark d-block">{{ Str::limit($s->catatan_teknisi, 100) }}</small>
                            </div>
                            @endif

                            <button type="button" class="btn btn-outline-primary w-100 rounded-3 fw-semibold mb-2" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $s->id }}">
                                <i class="bi bi-arrow-up-circle me-1"></i> Update Status
                            </button>
                            <a href="{{ route('teknisi.servis.tanda-terima', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Tanda Terima (PDF)
                            </a>
                        </div>
                    </div>
                </div>

                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-tools fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Tidak ada barang servis yang sedang aktif dikerjakan.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 3. SERVIS KELUAR / SELESAI -->
        <div class="tab-pane fade" id="pills-keluar" role="tabpanel" tabindex="0">
            <div class="row g-3">
                @forelse($servis->whereIn('status', ['selesai', 'diambil', 'garansi', 'batal']) as $s)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-success border-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $s->nama_barang ?? $s->jasaServis->nama_jasa ?? 'Barang Servis' }}</h6>
                                    <small class="text-muted d-block">{{ $s->transaksi->kode_transaksi ?? '-' }}</small>
                                </div>
                                @if($s->status == 'selesai')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Selesai</span>
                                @elseif($s->status == 'diambil')
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Diambil</span>
                                @elseif($s->status == 'garansi')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Garansi</span>
                                @elseif($s->status == 'batal')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Batal</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i> <strong>Pelanggan:</strong> {{ $s->transaksi->nama_pelanggan ?? '-' }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-person-check me-1"></i> <strong>Diterima Oleh:</strong> {{ $s->penerima ?? '-' }}</p>
                            @if($s->no_seri)
                            <p class="text-muted small mb-2"><i class="bi bi-hash me-1"></i> <strong>No. Seri:</strong> {{ $s->no_seri }}</p>
                            @endif
                            <p class="text-muted small mb-2"><i class="bi bi-person-badge me-1"></i> <strong>Teknisi:</strong> {{ $s->teknisi->name ?? '-' }}</p>
                            <p class="text-muted small mb-2"><i class="bi bi-cash me-1"></i> <strong>Total Biaya:</strong> Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</p>
                            @if($s->tanggal_selesai)
                            <p class="text-muted small mb-2"><i class="bi bi-calendar-check me-1"></i> <strong>Selesai Pada:</strong> {{ \Carbon\Carbon::parse($s->tanggal_selesai)->format('d M Y H:i') }}</p>
                            @endif
                            
                            <div class="bg-light rounded-3 p-2 mb-3 small d-flex justify-content-between">
                                <span>Bayar: <strong class="text-uppercase">{{ $s->transaksi->metode_pembayaran ?? 'transfer' }}</strong></span>
                                <span>Status: <strong class="{{ $s->transaksi->status == 'Lunas' ? 'text-success' : 'text-danger' }}">{{ $s->transaksi->status ?? 'Pending' }}</strong></span>
                            </div>

                            @if($s->catatan_teknisi)
                            <div class="bg-light rounded-3 p-2 mb-3">
                                <small class="text-muted fw-semibold">Catatan Akhir:</small>
                                <small class="text-dark d-block">{{ Str::limit($s->catatan_teknisi, 100) }}</small>
                            </div>
                            @endif

                            @if($s->transaksi && $s->transaksi->status != 'Lunas' && (Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir'))
                            <form action="{{ route('transaksi.konfirmasi', $s->transaksi->id) }}" method="POST" class="mt-2 mb-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100 rounded-3 fw-semibold" onclick="return confirm('Konfirmasi Pembayaran Lunas untuk Servis ini?')">
                                    <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pembayaran Lunas
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('teknisi.servis.tanda-terima', $s->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Tanda Terima (PDF)
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-check2-all fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Belum ada riwayat servis selesai.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Input Servis Baru -->
    <div class="modal fade" id="modalInputServis" tabindex="-1" aria-labelledby="modalInputServisLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="modalInputServisLabel"><i class="bi bi-tools text-primary me-2"></i>Daftarkan Servis Masuk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teknisi.servis.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Data Pelanggan -->
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-person-fill me-1"></i> Informasi Pelanggan</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Pilih Pelanggan Terdaftar</label>
                                    <select id="user_select" class="form-select">
                                        <option value="">-- Pelanggan Baru (Input Manual) --</option>
                                        @foreach($pelangganList as $p)
                                            <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-whatsapp="{{ $p->no_whatsapp }}">{{ $p->name }} ({{ $p->no_whatsapp ?? 'Tidak ada WA' }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" placeholder="Nama lengkap pelanggan" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Nomor WhatsApp Pelanggan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">+62 / 08</span>
                                        <input type="text" name="no_whatsapp" id="no_whatsapp" class="form-control" placeholder="Contoh: 08123456789" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Akan digunakan pelanggan untuk memantau status servis & mendapatkan notifikasi otomatis.</small>
                                </div>
                            </div>

                            <!-- Data Barang & Kerusakan -->
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-laptop-fill me-1"></i> Detail Kerusakan & Estimasi</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Nama Barang (Laptop, Printer, dll) <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Laptop Asus ROG STRIX G15" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">No. Seri / Serial Number <span class="text-muted">(Apabila Ada)</span></label>
                                    <input type="text" name="no_seri" class="form-control" placeholder="Contoh: SN-ROG123456789">
                                </div>



                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Kendala Awal / Keluhan <span class="text-danger">*</span></label>
                                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Deskripsikan keluhan barang..." required></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label small fw-semibold">Estimasi Biaya <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="estimasi_biaya" id="estimasi_biaya" class="form-control" placeholder="0" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label small fw-semibold">Estimasi Waktu <span class="text-danger">*</span></label>
                                        <input type="text" name="estimasi_waktu" class="form-control" placeholder="Contoh: 3 Hari" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label small fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                                        <select name="metode_pembayaran" class="form-select" required>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="cash">Cash di Toko</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label small fw-semibold">Diterima Oleh <span class="text-danger">*</span></label>
                                        <input type="text" name="penerima" class="form-control" value="{{ Auth::user()->name }}" required placeholder="Nama penerima barang">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">Daftarkan Servis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($servis as $s)
    <!-- Modal Update Status -->
    <div class="modal fade" id="modalUpdate{{ $s->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Update Status Servis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teknisi.update-status', $s->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Status Terkini</label>
                            <select name="status" class="form-select form-select-lg" required>
                                <option value="proses" {{ $s->status == 'proses' ? 'selected' : '' }}>Proses (Sedang Dikerjakan)</option>
                                <option value="selesai" {{ $s->status == 'selesai' ? 'selected' : '' }}>Selesai (Siap Diambil)</option>
                                <option value="diambil" {{ $s->status == 'diambil' ? 'selected' : '' }}>Diambil (Sudah Diambil Pelanggan)</option>
                                <option value="garansi" {{ $s->status == 'garansi' ? 'selected' : '' }}>Garansi</option>
                                <option value="batal" {{ $s->status == 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Tugaskan Teknisi</label>
                            <select name="teknisi_id" class="form-select form-select-lg">
                                <option value="">-- Belum Ditugaskan (Masuk Antrean) --</option>
                                @foreach($teknisiList as $t)
                                    <option value="{{ $t->id }}" {{ $s->teknisi_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Catatan Perkembangan</label>
                            <textarea name="catatan_teknisi" class="form-control" rows="4" placeholder="Tulis catatan perkembangan servis agar pelanggan mengetahuinya..." required>{{ $s->catatan_teknisi }}</textarea>
                        </div>
                        @if(Auth::user()->peran == 'admin')
                        <div class="mb-3 border-top pt-3">
                            <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-cash-coin me-1"></i>Pembagian Biaya Servis (Admin)</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">Total Biaya Jasa Servis (Rp)</label>
                                <input type="number" name="estimasi_biaya" id="estimasi_biaya_{{ $s->id }}" class="form-control" value="{{ intval($s->estimasi_biaya) }}" required min="0" oninput="hitungBagiHasil_{{ $s->id }}()">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-muted">Upah Teknisi (Rp)</label>
                                    <input type="number" name="upah_teknisi" id="upah_teknisi_{{ $s->id }}" class="form-control" value="{{ intval($s->upah_teknisi ?? $s->estimasi_biaya * 0.5) }}" required min="0" oninput="adjustKeuntungan_{{ $s->id }}()">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-muted">Keuntungan Toko (Rp)</label>
                                    <input type="number" name="keuntungan_toko" id="keuntungan_toko_{{ $s->id }}" class="form-control" value="{{ intval($s->keuntungan_toko ?? $s->estimasi_biaya * 0.5) }}" required min="0" oninput="adjustUpah_{{ $s->id }}()">
                                </div>
                            </div>
                        </div>
                        <script>
                            function hitungBagiHasil_{{ $s->id }}() {
                                const total = parseFloat(document.getElementById('estimasi_biaya_{{ $s->id }}').value) || 0;
                                const upahInput = document.getElementById('upah_teknisi_{{ $s->id }}');
                                const tokoInput = document.getElementById('keuntungan_toko_{{ $s->id }}');
                                
                                upahInput.value = Math.floor(total * 0.5);
                                tokoInput.value = total - parseInt(upahInput.value);
                            }
                            function adjustKeuntungan_{{ $s->id }}() {
                                const total = parseFloat(document.getElementById('estimasi_biaya_{{ $s->id }}').value) || 0;
                                const upah = parseFloat(document.getElementById('upah_teknisi_{{ $s->id }}').value) || 0;
                                const tokoInput = document.getElementById('keuntungan_toko_{{ $s->id }}');
                                
                                if (upah > total) {
                                    document.getElementById('upah_teknisi_{{ $s->id }}').value = total;
                                    tokoInput.value = 0;
                                } else {
                                    tokoInput.value = total - upah;
                                }
                            }
                            function adjustUpah_{{ $s->id }}() {
                                const total = parseFloat(document.getElementById('estimasi_biaya_{{ $s->id }}').value) || 0;
                                const toko = parseFloat(document.getElementById('keuntungan_toko_{{ $s->id }}').value) || 0;
                                const upahInput = document.getElementById('upah_teknisi_{{ $s->id }}');
                                
                                if (toko > total) {
                                    document.getElementById('keuntungan_toko_{{ $s->id }}').value = total;
                                    upahInput.value = 0;
                                } else {
                                    upahInput.value = total - toko;
                                }
                            }
                        </script>
                        @endif
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- JS Form Toggle Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect = document.getElementById('user_select');
            const namaInput = document.getElementById('nama_pelanggan');
            const waInput = document.getElementById('no_whatsapp');
            const biayaInput = document.getElementById('estimasi_biaya');

            // Handling User Selector
            userSelect.addEventListener('change', function () {
                const selectedOption = userSelect.options[userSelect.selectedIndex];
                if (selectedOption.value !== "") {
                    namaInput.value = selectedOption.getAttribute('data-name');
                    waInput.value = selectedOption.getAttribute('data-whatsapp') || '';
                    namaInput.readOnly = true;
                    waInput.readOnly = true;
                } else {
                    namaInput.value = '';
                    waInput.value = '';
                    namaInput.readOnly = false;
                    waInput.readOnly = false;
                }
            });


        });
    </script>
</x-app-layout>

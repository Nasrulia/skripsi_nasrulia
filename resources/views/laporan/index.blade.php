<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Laporan & Pratinjau Toko
            </h3>
            <p class="text-muted mb-0">Pilih periode transaksi, lihat pratinjau (preview), dan cetak laporan resmi dalam format PDF.</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-person-badge me-1"></i> Akses: {{ ucfirst(Auth::user()->peran) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Global Filter Period Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-sliders text-primary me-2"></i>Filter Rentang Periode Laporan
            </h5>
            <p class="text-muted small mb-0">Atur periode waktu untuk melihat pratinjau atau mencetak seluruh laporan di bawah ini.</p>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('laporan.index') }}" method="GET" id="global-filter-form">
                <div class="row g-3 align-items-end">
                    <!-- Mode Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-dark">Mode Filter</label>
                        <select name="filter_type" id="filter_type_index" class="form-select rounded-3 border shadow-xs" onchange="toggleFilterInputsIndex()">
                            <option value="semua" {{ $filter['filter_type'] == 'semua' ? 'selected' : '' }}>Semua Data (All Time)</option>
                            <option value="harian" {{ $filter['filter_type'] == 'harian' ? 'selected' : '' }}>Harian (Tanggal Tertentu)</option>
                            <option value="bulanan" {{ $filter['filter_type'] == 'bulanan' ? 'selected' : '' }}>Bulanan (Bulan & Tahun)</option>
                            <option value="tahunan" {{ $filter['filter_type'] == 'tahunan' ? 'selected' : '' }}>Tahunan (Tahun Tertentu)</option>
                            <option value="custom" {{ $filter['filter_type'] == 'custom' ? 'selected' : '' }}>Rentang Tanggal Custom</option>
                        </select>
                    </div>

                    <!-- Filter Harian -->
                    <div class="col-lg-3 col-md-6 filter-grp" id="idx-harian" style="{{ $filter['filter_type'] == 'harian' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-bold text-dark">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control rounded-3 border" value="{{ $filter['tanggal'] }}">
                    </div>

                    <!-- Filter Bulanan -->
                    <div class="col-lg-3 col-md-6 filter-grp" id="idx-bulanan" style="{{ $filter['filter_type'] == 'bulanan' ? '' : 'display: none;' }}">
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

                    <!-- Filter Tahunan -->
                    <div class="col-lg-3 col-md-6 filter-grp" id="idx-tahunan" style="{{ $filter['filter_type'] == 'tahunan' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-bold text-dark">Pilih Tahun</label>
                        <select name="tahun_tahunan" class="form-select rounded-3 border" onchange="document.querySelector('#idx-bulanan select[name=tahun]').value = this.value">
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $filter['tahun'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Filter Custom -->
                    <div class="col-lg-4 col-md-6 filter-grp" id="idx-custom" style="{{ $filter['filter_type'] == 'custom' ? '' : 'display: none;' }}">
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

                    <!-- Tombol Aksi -->
                    <div class="col-lg-auto ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-xs fw-semibold">
                            <i class="bi bi-funnel-fill me-1"></i> Terapkan Periode
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-light border rounded-3 px-3 shadow-xs text-muted" title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="small text-muted">
                    <i class="bi bi-info-circle me-1 text-primary"></i> Filter aktif saat ini:
                    <strong class="text-dark">{{ $filter['periode_label'] }}</strong>
                </span>
                <span class="small text-muted">
                    Semua tombol "Preview" dan "Cetak PDF" otomatis menggunakan filter ini.
                </span>
            </div>
        </div>
    </div>

    @php
        $currentParams = request()->query();
    @endphp

    <div class="row g-4">
        <!-- Kelompok 1: Penjualan & Stock -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-primary text-white fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-bag-check-fill fs-5 me-2"></i> 1. Penjualan & Barang
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-3">
                        <!-- Item 1: Transaksi Penjualan -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">1. Transaksi Penjualan</h6>
                                <span class="badge bg-success">Lunas</span>
                            </div>
                            <p class="text-muted small mb-3">Rekapitulasi penjualan produk yang telah lunas beserta metode pembayaran.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'transaksi-penjualan'], $currentParams)) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'transaksi-penjualan'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 2: Barang Terlaris -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">2. Produk Terlaris</h6>
                                <span class="badge bg-danger">Best Seller</span>
                            </div>
                            <p class="text-muted small mb-3">Peringkat produk terlaris berdasarkan kuantitas transaksi penjualan.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'produk-terlaris'], $currentParams)) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'produk-terlaris'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 3: Stock Barang Toko -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">3. Stock Barang Toko</h6>
                                <span class="badge bg-info text-dark">Inventori</span>
                            </div>
                            <p class="text-muted small mb-3">Kondisi stok fisik barang, harga beli modal, harga jual, dan status ketersediaan.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'produk-stok'], $currentParams)) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'produk-stok'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelompok 2: Jasa Servis & Teknisi -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-success text-white fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-tools fs-5 me-2"></i> 2. Jasa Servis & Teknisi
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-3">
                        <!-- Item 4: Transaksi Servis -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">4. Transaksi Servis</h6>
                                <span class="badge bg-success">Servis</span>
                            </div>
                            <p class="text-muted small mb-3">Daftar penerimaan servis pelanggan beserta status pengerjaan dan biaya.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'transaksi-servis'], $currentParams)) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'transaksi-servis'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 5: Teknisi / Data Service -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">5. Kinerja Teknisi</h6>
                                <span class="badge bg-primary">Bagi Hasil</span>
                            </div>
                            <p class="text-muted small mb-3">Bagi hasil pendapatan jasa teknisi (Upah Teknisi vs Laba Bersih Toko).</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'servis-rekap'], $currentParams)) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'servis-rekap'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 6: Ringkasan Servis & Kerusakan -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">6. Ringkasan Kerusakan</h6>
                                <span class="badge bg-warning text-dark">Analisis</span>
                            </div>
                            <p class="text-muted small mb-3">Statistik jenis keluhan & kerusakan unit yang paling sering ditangani toko.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'servis-ringkasan'], $currentParams)) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'servis-ringkasan'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 7: Laporan Komplain -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">7. Laporan Komplain</h6>
                                <span class="badge bg-danger">Keluhan</span>
                            </div>
                            <p class="text-muted small mb-3">Rekap komplain resmi pelanggan dan keluhan yang terdeteksi via Chatbot AI.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'komplain'], $currentParams)) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'komplain'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelompok 3: Analitik, Keuangan & Pembayaran -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-warning text-dark fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-graph-up-arrow fs-5 me-2"></i> 3. Analitik & Keuangan
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-3">
                        <!-- Item 8: Laporan Chatbot -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">8. Laporan Chatbot AI</h6>
                                <span class="badge bg-info text-dark">AI Log</span>
                            </div>
                            <p class="text-muted small mb-3">Log percakapan, topik pertanyaan pelanggan, dan efektivitas respon chatbot.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'chatbot-analitik'], $currentParams)) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'chatbot-analitik'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 9: Keuangan Ringkas -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">9. Keuangan Ringkas</h6>
                                <span class="badge bg-success">Laba Rugi</span>
                            </div>
                            <p class="text-muted small mb-3">Total omzet penjualan, pendapatan servis, perhitungan HPP, dan laba kotor.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'keuangan'], $currentParams)) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'keuangan'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 10: Metode Pembayaran -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">10. Metode Pembayaran</h6>
                                <span class="badge bg-primary">Cash vs Transfer</span>
                            </div>
                            <p class="text-muted small mb-3">Perbandingan persentase transaksi tunai (cash) versus transfer bank.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'metode-pembayaran'], $currentParams)) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'metode-pembayaran'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>

                        <!-- Item 11: Margin Keuntungan Produk -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">11. Margin Keuntungan</h6>
                                <span class="badge bg-warning text-dark">Margin %</span>
                            </div>
                            <p class="text-muted small mb-3">Analisis persentase potensi keuntungan per produk dari selisih harga jual & beli.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('laporan.preview', array_merge(['tipe' => 'margin'], $currentParams)) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ route('laporan.cetak', array_merge(['tipe' => 'margin'], $currentParams)) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 shadow-xs" title="Cetak PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Kontrol Input Filter Interaktif di Index -->
    <script>
        function toggleFilterInputsIndex() {
            const mode = document.getElementById('filter_type_index').value;
            document.querySelectorAll('.filter-grp').forEach(el => el.style.display = 'none');

            if (mode === 'harian') {
                const el = document.getElementById('idx-harian');
                if (el) el.style.display = 'block';
            } else if (mode === 'bulanan') {
                const el = document.getElementById('idx-bulanan');
                if (el) el.style.display = 'block';
            } else if (mode === 'tahunan') {
                const el = document.getElementById('idx-tahunan');
                if (el) el.style.display = 'block';
            } else if (mode === 'custom') {
                const el = document.getElementById('idx-custom');
                if (el) el.style.display = 'block';
            }
        }
    </script>
</x-app-layout>

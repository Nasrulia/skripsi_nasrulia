<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Cetak Laporan Toko</h3>
            <p class="text-muted mb-0">Pilih jenis laporan yang ingin Anda unduh dalam format PDF.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kelompok 1: Penjualan & Stock -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-primary text-white fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-bag-check-fill fs-5 me-2"></i> Penjualan & Barang
                </div>
                <div class="card-body py-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'transaksi-penjualan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 1. Transaksi Penjualan</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'produk-terlaris') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 2. Transaksi Barang Terlaris</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'produk-stok') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 3. Stock Barang Toko</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelompok 2: Jasa Servis & Teknisi -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-success text-white fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-tools fs-5 me-2"></i> Jasa Servis & Teknisi
                </div>
                <div class="card-body py-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'transaksi-servis') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 4. Transaksi Service</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'servis-rekap') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 5. Teknisi / Data Service</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'servis-ringkasan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 6. Ringkasan Servis & Kerusakan</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'komplain') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 7. Laporan Komplain</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelompok 3: Analitik, Keuangan & Pembayaran -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-warning text-dark fw-bold rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-graph-up-arrow fs-5 me-2"></i> Analitik & Keuangan
                </div>
                <div class="card-body py-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'chatbot-analitik') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 8. Laporan Chatbot</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'keuangan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 9. Keuangan Ringkas</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'metode-pembayaran') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center shadow-xs-hover">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 10. Metode Pembayaran</span>
                            <i class="bi bi-printer text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

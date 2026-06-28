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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-primary text-white fw-bold rounded-top-4 py-3">
                    <i class="bi bi-box-seam me-2"></i> Laporan Master Data
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'produk-semua') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 1. Laporan Data Produk</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'produk-menipis') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 2. Laporan Stok Menipis</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'chatbot') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 3. Laporan Aturan Chatbot AI</span>
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-success text-white fw-bold rounded-top-4 py-3">
                    <i class="bi bi-cash-coin me-2"></i> Laporan Penjualan
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'transaksi-semua') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 5. Laporan Seluruh Transaksi</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'transaksi-lunas') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 6. Laporan Transaksi Lunas</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'transaksi-pending') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 7. Laporan Transaksi Pending</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'pendapatan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center border-primary bg-primary bg-opacity-10 fw-bold">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 8. Laporan Pendapatan Keseluruhan</span>
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-warning text-dark fw-bold rounded-top-4 py-3">
                    <i class="bi bi-graph-up-arrow me-2"></i> Laporan Analitik & Keuangan
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('laporan.cetak', 'margin') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 9. Laporan Margin Keuntungan</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'servis-ringkasan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 10. Laporan Ringkasan Servis</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'servis-rekap') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 11. Laporan Rekapitulasi Servis</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'keuangan') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 12. Laporan Keuangan Ringkas</span>
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('laporan.cetak', 'chatbot-analitik') }}" target="_blank" class="btn btn-outline-dark text-start p-3 rounded-3 d-flex justify-content-between align-items-center border-warning bg-warning bg-opacity-10 fw-bold">
                            <span><i class="bi bi-file-earmark-pdf text-danger fs-4 me-3"></i> 13. Laporan Analitik Chatbot AI</span>
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

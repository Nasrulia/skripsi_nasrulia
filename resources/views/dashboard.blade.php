<x-app-layout>
    @php
        $total_produk = \App\Models\Produk::count();
        $penjualan_hari_ini = \App\Models\Transaksi::whereDate('created_at', \Carbon\Carbon::today())->count();
        $servis_berjalan = \App\Models\ServisDetail::where('status', 'proses')->count();
        $pendapatan_bulan_ini = \App\Models\Transaksi::where('status', 'Lunas')
            ->whereMonth('updated_at', \Carbon\Carbon::now()->month)
            ->whereYear('updated_at', \Carbon\Carbon::now()->year)
            ->sum('total_bayar');
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <div class="card-body p-4 d-flex align-items-center text-white position-relative overflow-hidden">
                    <i class="bi bi-shop position-absolute opacity-25" style="font-size: 10rem; right: -20px; top: -30px;"></i>
                    <div class="position-relative z-index-1">
                        <h3 class="fw-bold mb-1">Selamat datang kembali, {{ Auth::user()->name }}!</h3>
                        <p class="mb-0 opacity-75">Kelola penjualan, servis, dan produk Nusantara Jaya Computer dalam satu sistem terintegrasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->peran == 'teknisi')
    <div class="row g-4 mb-4">
        @php
            $servis_saya = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->count();
            $servis_proses = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->where('status', 'proses')->count();
            $servis_selesai = \App\Models\ServisDetail::where('teknisi_id', Auth::id())->where('status', 'selesai')->count();
            $servis_tersedia = \App\Models\ServisDetail::whereNull('teknisi_id')->count();
        @endphp
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Servis Saya</h6>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_saya }}</h2>
                    <small class="text-muted fw-semibold">Total servis ditangani</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Sedang Diproses</h6>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-gear fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_proses }}</h2>
                    <small class="text-muted fw-semibold">Masih dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Selesai</h6>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_selesai }}</h2>
                    <small class="text-success fw-semibold">Sudah selesai</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Tersedia</h6>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-inbox fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_tersedia }}</h2>
                    <small class="text-muted fw-semibold">Belum diambil</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagi Hasil & Pendapatan Saya (Teknisi) -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Total Upah Saya (Lunas)</h6>
                        <i class="bi bi-wallet2 text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($teknisi_stats['total_upah'] ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Uang upah dari servis yang sudah selesai & lunas</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-success border-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Kontribusi Untung Toko</h6>
                        <i class="bi bi-shop-window text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($teknisi_stats['total_toko'] ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Setoran laba masuk keuntungan toko</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header border-0 bg-transparent pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-cash-coin text-primary me-2"></i>Rincian Pendapatan & Bagi Hasil Saya
                    </h5>
                    <p class="text-muted small mb-0">Statistik bagi hasil pengerjaan servis Anda (Upah Kerja vs Keuntungan Toko)</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <!-- Chart Column -->
                        <div class="col-lg-5">
                            <div class="p-3 bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height: 280px;">
                                <div style="height: 240px; width: 100%; max-width: 240px;">
                                    <canvas id="teknisiChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Table Column -->
                        <div class="col-lg-7">
                            <div class="table-responsive rounded-3 border border-light-subtle">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small text-muted fw-bold border-0 py-2 px-3">Kode TRX</th>
                                            <th class="small text-muted fw-bold border-0 py-2 px-3">Tanggal</th>
                                            <th class="small text-muted fw-bold border-0 py-2 px-3">Barang Servis</th>
                                            <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Total Jasa</th>
                                            <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Upah Saya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($teknisi_stats['list'] ?? [] as $ls)
                                        <tr>
                                            <td class="py-2 px-3 fw-bold text-primary text-nowrap" style="font-size: 0.85rem;">{{ $ls['kode'] }}</td>
                                            <td class="py-2 px-3 text-muted text-nowrap" style="font-size: 0.8rem;">{{ $ls['tanggal'] }}</td>
                                            <td class="py-2 px-3 fw-medium text-dark text-nowrap" style="font-size: 0.85rem;">{{ $ls['barang'] }}</td>
                                            <td class="py-2 px-3 text-end text-nowrap" style="font-size: 0.85rem;">Rp {{ number_format($ls['total'], 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 text-end text-success fw-bold text-nowrap" style="font-size: 0.85rem;">Rp {{ number_format($ls['upah'], 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">Belum ada riwayat pengerjaan servis yang diselesaikan & lunas.</td>
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
    
    <!-- Script for Technician Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tekCtx = document.getElementById('teknisiChart').getContext('2d');
        const totalUpah = {{ $teknisi_stats['total_upah'] ?? 0 }};
        const totalToko = {{ $teknisi_stats['total_toko'] ?? 0 }};

        const dataValues = [totalUpah, totalToko];
        const hasData = totalUpah > 0 || totalToko > 0;

        new Chart(tekCtx, {
            type: 'doughnut',
            data: {
                labels: ['Upah Saya', 'Untung Toko'],
                datasets: [{
                    data: hasData ? dataValues : [1, 1],
                    backgroundColor: hasData ? ['#0d6efd', '#198754'] : ['#e9ecef', '#dee2e6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: "'Inter', sans-serif", weight: 'bold', size: 11 },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        enabled: hasData,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    @endif

    @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir')
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Total Produk</h6>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $total_produk }}</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Tersedia di gudang</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Penjualan Hari Ini</h6>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $penjualan_hari_ini }}</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Transaksi sukses</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Servis Berjalan</h6>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-tools fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $servis_berjalan }}</h2>
                    <small class="text-muted fw-semibold">Sedang dikerjakan</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Pendapatan Bulan Ini</h6>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($pendapatan_bulan_ini, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Transaksi Lunas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Tabel Ringkasan Keuangan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-graph-up text-primary me-2"></i>Grafik & Ringkasan Keuangan
                        </h5>
                        <p class="text-muted small mb-0">Statistik penjualan dan keuntungan bersih toko Nusantara Jaya Computer</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="filter-periode" class="text-muted small fw-semibold mb-0 d-none d-sm-block">Periode:</label>
                        <select id="filter-periode" class="form-select border-0 bg-light rounded-3 shadow-sm py-2 px-3 text-dark fw-bold" style="width: 180px; cursor: pointer;">
                            <option value="hari" selected>Per Hari (15 Hari)</option>
                            <option value="minggu">Per Minggu (8 Minggu)</option>
                            <option value="bulan">Per Bulan (12 Bulan)</option>
                        </select>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <!-- Chart Column -->
                        <div class="col-lg-8">
                            <div class="p-3 bg-light rounded-4 position-relative" style="min-height: 380px;">
                                <!-- Loading overlay -->
                                <div id="chart-loading" class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 10;">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <div class="text-muted small mt-2 fw-semibold">Memuat data...</div>
                                </div>
                                <div style="height: 340px; width: 100%;">
                                    <canvas id="statChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Table / Summary Column -->
                        <div class="col-lg-4 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-table me-2 text-success"></i>Tabel Rincian Keuangan</h6>
                                <div class="table-responsive rounded-3 border border-light-subtle" style="max-height: 240px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0" id="stat-table">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="small text-muted fw-bold border-0 py-2 px-3">Periode</th>
                                                <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Penjualan</th>
                                                <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Keuntungan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Cumulative summary -->
                            <div class="pt-3 border-top mt-3">
                                <div class="p-3 bg-light rounded-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-semibold">Total Penjualan:</span>
                                        <span class="fw-bold text-primary fs-6" id="total-penjualan-sum">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-semibold">Total Keuntungan:</span>
                                        <span class="fw-bold text-success fs-5" id="total-keuntungan-sum">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kinerja & Statistik Servis Teknisi (Admin & Kasir) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header border-0 bg-transparent pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i>Kinerja & Statistik Servis Teknisi
                    </h5>
                    <p class="text-muted small mb-0">Perbandingan jumlah tugas servis dan pendapatan bagi hasil per teknisi</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <!-- Chart Column -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height: 320px;">
                                <div style="height: 280px; width: 100%;">
                                    <canvas id="teknisiAdminChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Table Column -->
                        <div class="col-lg-6">
                            <div class="table-responsive rounded-3 border border-light-subtle">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small text-muted fw-bold border-0 py-2 px-3">Teknisi</th>
                                            <th class="small text-muted fw-bold text-center border-0 py-2 px-3">Proses</th>
                                            <th class="small text-muted fw-bold text-center border-0 py-2 px-3">Selesai</th>
                                            <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Upah Kerja</th>
                                            <th class="small text-muted fw-bold text-end border-0 py-2 px-3">Untung Toko</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($admin_teknisi_stats as $stat)
                                        <tr>
                                            <td class="py-2 px-3 fw-bold text-dark" style="font-size: 0.85rem;">{{ $stat['nama'] }}</td>
                                            <td class="py-2 px-3 text-center text-warning fw-bold" style="font-size: 0.85rem;">{{ $stat['proses'] }}</td>
                                            <td class="py-2 px-3 text-center text-success fw-bold" style="font-size: 0.85rem;">{{ $stat['selesai'] }}</td>
                                            <td class="py-2 px-3 text-end text-primary fw-medium" style="font-size: 0.85rem;">Rp {{ number_format($stat['upah'], 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 text-end text-success fw-bold" style="font-size: 0.85rem;">Rp {{ number_format($stat['toko'], 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">Belum ada data teknisi yang tercatat.</td>
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
    </div>

    <!-- Chart.js and Custom Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('statChart').getContext('2d');
        const filterSelect = document.getElementById('filter-periode');
        const loadingOverlay = document.getElementById('chart-loading');
        const tableBody = document.querySelector('#stat-table tbody');
        const totalPenjualanSum = document.getElementById('total-penjualan-sum');
        const totalKeuntunganSum = document.getElementById('total-keuntungan-sum');
        
        let myChart = null;

        // Currency Formatter
        function formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(value);
        }

        function updateUI(data) {
            // 1. Update Chart
            const labels = data.map(item => item.label);
            const penjualanData = data.map(item => item.penjualan);
            const keuntunganData = data.map(item => item.keuntungan);

            if (myChart) {
                myChart.destroy();
            }

            // Create gradients for elegant background area fills
            const salesGradient = ctx.createLinearGradient(0, 0, 0, 300);
            salesGradient.addColorStop(0, 'rgba(13, 110, 253, 0.25)');
            salesGradient.addColorStop(1, 'rgba(13, 110, 253, 0.00)');

            const profitGradient = ctx.createLinearGradient(0, 0, 0, 300);
            profitGradient.addColorStop(0, 'rgba(25, 135, 84, 0.25)');
            profitGradient.addColorStop(1, 'rgba(25, 135, 84, 0.00)');

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Penjualan',
                            data: penjualanData,
                            borderColor: '#0d6efd',
                            backgroundColor: salesGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0d6efd',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Total Keuntungan',
                            data: keuntunganData,
                            borderColor: '#198754',
                            backgroundColor: profitGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#198754',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    weight: 'bold',
                                    size: 11
                                },
                                color: '#495057',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            padding: 12,
                            backgroundColor: 'rgba(30, 30, 45, 0.95)',
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += formatRupiah(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.04)'
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 10
                                },
                                color: '#6c757d',
                                callback: function (value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + 'jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + 'rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 10
                                },
                                color: '#6c757d'
                            }
                        }
                    }
                }
            });

            // 2. Update Table
            tableBody.innerHTML = '';
            
            // Show newest data first in the table for better readability
            const reversedData = [...data].reverse();
            
            reversedData.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-2 px-3 fw-semibold text-dark text-nowrap" style="font-size: 0.85rem;">${item.label}</td>
                    <td class="py-2 px-3 text-end text-primary fw-medium text-nowrap" style="font-size: 0.85rem;">${formatRupiah(item.penjualan)}</td>
                    <td class="py-2 px-3 text-end text-success fw-bold text-nowrap" style="font-size: 0.85rem;">${formatRupiah(item.keuntungan)}</td>
                `;
                tableBody.appendChild(row);
            });

            // 3. Update Sums
            const totalPenjualan = penjualanData.reduce((a, b) => a + b, 0);
            const totalKeuntungan = keuntunganData.reduce((a, b) => a + b, 0);
            totalPenjualanSum.textContent = formatRupiah(totalPenjualan);
            totalKeuntunganSum.textContent = formatRupiah(totalKeuntungan);
        }

        function fetchData(periode) {
            loadingOverlay.classList.remove('d-none');
            fetch(`/api/dashboard/statistiks?filter=${periode}`)
                .then(response => response.json())
                .then(data => {
                    updateUI(data);
                    loadingOverlay.classList.add('d-none');
                })
                .catch(error => {
                    console.error('Error fetching statistiks:', error);
                    loadingOverlay.classList.add('d-none');
                });
        }

        // Event Listener
        filterSelect.addEventListener('change', function () {
            fetchData(this.value);
        });

        // Init
        fetchData('hari');

        // Chart untuk Kinerja Servis Teknisi (Admin & Kasir)
        const tekAdminCtx = document.getElementById('teknisiAdminChart').getContext('2d');
        const adminTeknisiData = @json($admin_teknisi_stats);
        
        const labelsTeknisi = adminTeknisiData.map(item => item.nama);
        const prosesTeknisi = adminTeknisiData.map(item => item.proses);
        const selesaiTeknisi = adminTeknisiData.map(item => item.selesai);

        new Chart(tekAdminCtx, {
            type: 'bar',
            data: {
                labels: labelsTeknisi,
                datasets: [
                    {
                        label: 'Sedang Diproses',
                        data: prosesTeknisi,
                        backgroundColor: '#ffc107',
                        borderRadius: 5,
                    },
                    {
                        label: 'Selesai / Diambil',
                        data: selesaiTeknisi,
                        backgroundColor: '#198754',
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: "'Inter', sans-serif", weight: 'bold', size: 11 },
                            usePointStyle: true
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.04)' },
                        ticks: { stepSize: 1, precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

    });
    </script>
    @endif


    @if(Auth::user()->peran == 'pelanggan')
    <!-- Tampilan Menu Sidebar (Menu Cepat) -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-dark mb-3"><i class="bi bi-compass me-2"></i>Akses Cepat Menu</h4>
        </div>
        <!-- Katalog Produk -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white menu-card" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <a href="{{ route('pelanggan.katalog') }}" class="text-decoration-none text-dark p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-shop fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Katalog Produk</h6>
                            <small class="text-muted">Cari & belanja barang</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
        <!-- Keranjang Saya -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white menu-card" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <a href="{{ route('keranjang.index') }}" class="text-decoration-none text-dark p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart3 fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Keranjang Saya</h6>
                            <small class="text-muted">{{ count((array) session('keranjang')) }} barang terpilih</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
        <!-- Pesanan Saya -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white menu-card" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <a href="{{ route('pesanan.saya') }}" class="text-decoration-none text-dark p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        @php
                            $total_pesanan = \App\Models\Transaksi::where('user_id', Auth::id())->count();
                        @endphp
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-bag-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Pesanan Saya</h6>
                            <small class="text-muted">{{ $total_pesanan }} total transaksi</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
        <!-- Konsultasi AI -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white menu-card" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <a href="{{ route('konsultasi') }}" class="text-decoration-none text-dark p-4 d-flex align-items-center justify-content-between h-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-robot fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Konsultasi AI</h6>
                            <small class="text-muted">Chatbot asisten cerdas</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Barang Best Seller -->
    @php
        $bestSellers = \App\Models\Produk::with('kategori')
            ->withSum(['detail as total_terjual' => function ($query) {
                $query->whereHas('transaksi', function ($q) {
                    $q->where('status', 'Lunas');
                });
            }], 'jumlah')
            ->having('total_terjual', '>', 0)
            ->orderByDesc('total_terjual')
            ->limit(4)
            ->get();
    @endphp

    @if($bestSellers->isNotEmpty())
    <div class="row mb-3 mt-5">
        <div class="col-12">
            <div class="d-flex align-items-center gap-2">
                <span class="p-2 bg-danger bg-opacity-10 text-danger rounded-3">
                    <i class="bi bi-fire fs-4"></i>
                </span>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Produk Terlaris (Best Seller)</h4>
                    <p class="text-muted small mb-0">Produk terpopuler yang paling banyak diminati pelanggan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($bestSellers as $bp)
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative best-seller-card overflow-hidden" style="border: 1.5px solid rgba(220, 53, 69, 0.2) !important; transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease;">
                    <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                        <span class="badge bg-danger shadow-sm px-3 py-2 rounded-pill d-flex align-items-center gap-1">
                            <i class="bi bi-fire"></i> Best Seller
                        </span>
                    </div>
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <span class="badge bg-warning text-dark shadow-sm px-2 py-1 rounded-pill fw-bold">
                            Terjual {{ $bp->total_terjual }}
                        </span>
                    </div>
                    <div class="position-relative">
                        @if($bp->foto)
                            <img src="{{ Storage::url($bp->foto) }}" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($bp->merk)
                            <small class="text-info fw-semibold">{{ $bp->merk }}</small>
                        @endif
                        <h6 class="fw-bold mb-1 text-dark">{{ $bp->nama_produk }}</h6>
                        <p class="text-muted small mb-3 text-truncate">{{ $bp->deskripsi ?? 'Ready stock unit premium.' }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Rp {{ number_format($bp->harga_jual, 0, ',', '.') }}</span>
                            <span class="small text-muted">Stok: {{ $bp->stok }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4 px-3">
                        @if($bp->stok > 0)
                            <form action="{{ route('keranjang.tambah', $bp->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 rounded-3 fw-semibold">
                                    <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100 rounded-3" disabled>Stok Habis</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="row mt-4">
        <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border border-light-subtle">
            <i class="bi bi-fire fs-1 text-muted opacity-50 d-block mb-3"></i>
            <h5 class="text-muted fw-bold">Belum ada produk terlaris saat ini.</h5>
            <p class="text-muted small mb-0">Transaksi sukses (Lunas) belum tercatat di sistem.</p>
        </div>
    </div>
    @endif

    <style>
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }
        .best-seller-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(220, 53, 69, 0.12) !important;
        }
    </style>
    @endif
</x-app-layout>

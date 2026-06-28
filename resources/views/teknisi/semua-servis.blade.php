<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Semua Data Servis</h3>
            <p class="text-muted mb-0">Pantau seluruh data servis yang ada di sistem.</p>
        </div>
        <a href="{{ route('teknisi.servis') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode TRX</th>
                            <th>Pelanggan</th>
                            <th>Nama Barang</th>
                            <th>Jasa Servis</th>
                            <th>Estimasi Biaya</th>
                            <th>Estimasi Waktu</th>
                            <th>Status Servis</th>
                            <th>Teknisi</th>
                            <th>Diterima Oleh</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semuaServis as $s)
                        <tr>
                            <td class="fw-bold text-primary">{{ $s->transaksi->kode_transaksi ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $s->transaksi->nama_pelanggan ?? '-' }}</div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $s->transaksi->user->no_whatsapp ?? '-' }}</small>
                            </td>
                            <td class="fw-semibold">{{ $s->nama_barang ?? '-' }}</td>
                            <td>{{ $s->jasaServis->nama_jasa ?? 'Custom Servis' }}</td>
                            <td class="fw-semibold">Rp {{ number_format($s->estimasi_biaya, 0, ',', '.') }}</td>
                            <td>{{ $s->estimasi_waktu ?? '-' }}</td>
                            <td>
                                @if($s->status == 'proses')
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">Proses</span>
                                @elseif($s->status == 'selesai')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Selesai</span>
                                @elseif($s->status == 'diambil')
                                    <span class="badge bg-info px-3 py-2 rounded-pill">Diambil</span>
                                @elseif($s->status == 'garansi')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Garansi</span>
                                @elseif($s->status == 'batal')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">Batal</span>
                                @endif
                            </td>
                            <td>{{ $s->teknisi->name ?? '-' }}</td>
                            <td>{{ $s->penerima ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-tools fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data servis.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

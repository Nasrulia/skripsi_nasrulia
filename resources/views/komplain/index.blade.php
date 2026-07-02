<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Manajemen Komplain Pelanggan</h3>
            <p class="text-muted mb-0">Daftar laporan komplain yang diajukan oleh pelanggan melalui Chatbot AI.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <a href="{{ route('komplain.index') }}" class="btn btn-sm {{ !request()->filled('status') ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3">Semua</a>
                    <a href="{{ route('komplain.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-light text-muted' }} rounded-pill px-3">Pending</a>
                    <a href="{{ route('komplain.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ request('status') === 'selesai' ? 'btn-success' : 'btn-light text-muted' }} rounded-pill px-3">Selesai</a>
                </div>
                <div class="text-muted small">
                    Menampilkan {{ $komplain->firstItem() ?? 0 }} - {{ $komplain->lastItem() ?? 0 }} dari {{ $komplain->total() }} komplain
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th width="15%">Kode TRX</th>
                            <th width="20%">Pelanggan</th>
                            <th width="15%">Kategori</th>
                            <th>Rincian Keluhan</th>
                            <th width="12%">Status</th>
                            <th class="pe-4 text-center" width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($komplain as $index => $k)
                            <tr>
                                <td class="ps-4 text-muted">{{ $komplain->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $k->kode_transaksi }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $k->nama_pelanggan }}</div>
                                    <small class="text-muted"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $k->no_whatsapp }}</small>
                                </td>
                                <td>
                                    @if($k->tipe === 'penjualan')
                                        <span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 0.75rem;">Penjualan</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 0.75rem;">Servis</span>
                                    @endif
                                </td>
                                <td>
                                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $k->deskripsi }}</p>
                                    <small class="text-muted" style="font-size: 0.75rem;">Masuk: {{ \Carbon\Carbon::parse($k->created_at)->diffForHumans() }} ({{ \Carbon\Carbon::parse($k->created_at)->format('d M Y H:i') }})</small>
                                </td>
                                <td>
                                    @if($k->status === 'pending')
                                        <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>Pending</span>
                                    @else
                                        <span class="badge bg-success px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    @php
                                        $phone = preg_replace('/[^0-9]/', '', $k->no_whatsapp);
                                        if (str_starts_with($phone, '0')) {
                                            $phone = '62' . substr($phone, 1);
                                        }
                                        $waMessage = "Halo {$k->nama_pelanggan}, kami dari Nusantara Jaya Computer ingin menindaklanjuti keluhan Anda terkait transaksi {$k->kode_transaksi} (\"{$k->deskripsi}\"). Kami siap membantu menyelesaikan kendala Anda.";
                                        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($waMessage);
                                    @endphp
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill d-inline-flex align-items-center gap-1.5 px-3">
                                            <i class="bi bi-whatsapp"></i> Hubungi
                                        </a>
                                        @if($k->status === 'pending')
                                            <form action="{{ route('komplain.selesai', $k->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin komplain ini telah selesai ditangani?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill d-inline-flex align-items-center gap-1.5 px-3">
                                                    <i class="bi bi-check-lg"></i> Selesai
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-left-dots fs-1 d-block mb-3 text-secondary"></i>
                                    Tidak ada data komplain yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($komplain->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $komplain->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

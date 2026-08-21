<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-wallet2 fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Pembayaran Pesanan</h4>
                        <p class="text-muted">Kode: <strong class="text-primary">{{ $transaksi->kode_transaksi }}</strong></p>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                        <small>
                            @if($transaksi->nominal_dp > 0)
                                Silakan transfer **DP minimal Rp {{ number_format($transaksi->nominal_dp, 0, ',', '.') }}** ke rekening BNI: <strong>1234567890</strong> a.n. <strong>Nusantara Jaya Computer</strong> lalu upload bukti transfer di bawah. Sisa pembayaran dapat dilunasi saat pengambilan barang.
                            @else
                                Silakan transfer ke rekening BNI: <strong>1234567890</strong> a.n. <strong>Nusantara Jaya Computer</strong> lalu upload bukti transfer di bawah.
                            @endif
                        </small>
                    </div>

                    @if($transaksi->nominal_dp > 0)
                    <div class="d-flex justify-content-between py-3 border-bottom bg-light px-2 rounded-3 mb-2">
                        <span class="text-muted fw-bold">Nominal DP Wajib:</span>
                        <span class="fw-bold text-danger fs-5">Rp {{ number_format($transaksi->nominal_dp, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Subtotal Produk:</span>
                        <span class="fw-semibold text-dark">Rp {{ number_format($transaksi->total_bayar - $transaksi->ongkir - $transaksi->biaya_packing, 0, ',', '.') }}</span>
                    </div>

                    @if($transaksi->metode_pengambilan == 'diantar')
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ekspedisi & Layanan:</span>
                        <span class="fw-bold text-dark">{{ $transaksi->ekspedisi->nama_ekspedisi ?? 'Ekspedisi' }} {{ $transaksi->layanan_ekspedisi ? '(' . $transaksi->layanan_ekspedisi . ')' : '' }}</span>
                    </div>
                    @if($transaksi->kota_tujuan)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Tujuan Pengiriman:</span>
                        <span class="fw-semibold text-dark">{{ $transaksi->kota_tujuan }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ongkos Kirim:</span>
                        <span class="fw-bold text-primary">Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</span>
                    </div>
                    @if($transaksi->biaya_packing > 0)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ongkos Packing Proteksi:</span>
                        <span class="fw-bold text-warning">Rp {{ number_format($transaksi->biaya_packing, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @else
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Metode:</span>
                        <span class="fw-bold text-success"><i class="bi bi-shop me-1"></i> Ambil di Toko</span>
                    </div>
                    @if($transaksi->estimasi_diambil)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Estimasi Ambil:</span>
                        <span class="fw-bold text-dark"><i class="bi bi-clock me-1 text-primary"></i> {{ \Carbon\Carbon::parse($transaksi->estimasi_diambil)->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    @endif

                    <div class="d-flex justify-content-between py-3 border-bottom bg-light px-2 rounded-3 mt-2">
                        <span class="text-dark fw-bold fs-5">Total Pembayaran:</span>
                        <span class="fw-bold text-primary fs-5">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
                    </div>

                    @if($transaksi->bukti_bayar)
                    <div class="mt-4 text-center">
                        <small class="text-muted d-block mb-2">Bukti pembayaran yang sudah diupload:</small>
                        <img src="{{ asset('storage/' . $transaksi->bukti_bayar) }}" class="rounded-3 shadow-sm" style="max-height: 200px; object-fit: contain;">
                        <div class="alert alert-success mt-3 border-0 rounded-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Bukti sudah diupload, menunggu konfirmasi admin.
                        </div>
                    </div>
                    @else
                    <form action="{{ route('pembayaran.upload', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Bukti Transfer</label>
                            <input type="file" name="bukti_bayar" class="form-control form-control-lg @error('bukti_bayar') is-invalid @enderror" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG, WEBP. Maks: 2MB.</small>
                            @error('bukti_bayar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                            <i class="bi bi-upload me-2"></i> Upload Bukti Pembayaran
                        </button>
                    </form>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('pesanan.saya') }}" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

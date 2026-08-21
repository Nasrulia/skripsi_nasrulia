<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Ekspedisi & RajaOngkir</h3>
            <p class="text-muted mb-0">Integrasi API RajaOngkir untuk perhitungan ongkos kirim dan proteksi packing otomatis.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahEkspedisi">
            <i class="bi bi-plus-lg me-1"></i> Tambah Ekspedisi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Integrasi RajaOngkir Banner -->
    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success bg-opacity-75 text-white px-3 py-2 rounded-pill me-2">
                        <i class="bi bi-check-circle-fill me-1"></i> RajaOngkir API Aktif
                    </span>
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill">
                        Origin: Kota Banjarmasin (ID: 36)
                    </span>
                </div>
                <h5 class="fw-bold mb-1">Perhitungan Ongkos Kirim Otomatis & Akurat</h5>
                <p class="text-white-50 mb-0 small">
                    Pelanggan dapat memilih Provinsi, Kota Tujuan, Kurir Ekspedisi (JNE, POS Indonesia, TIKI, SiCepat), dan Paket Layanan secara real-time berdasarkan berat barang tanpa input jarak manual.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="bg-white bg-opacity-10 p-3 rounded-4">
                    <small class="text-white-50 d-block">Toko Asal Pengiriman:</small>
                    <strong class="fs-6 d-block">CV Nusantara Jaya Computer</strong>
                    <small class="text-white-50">Kampung Melayu, Banjarmasin</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Skema Ongkos Packing Ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-1 rounded-pill fw-bold">Ukuran Kecil</span>
                    <span class="fw-bold text-dark fs-5">Rp 15.000</span>
                </div>
                <small class="text-muted">Aksesoris, Tinta Printer, Mouse, Flashdisk, RAM, SSD.</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold">Ukuran Sedang</span>
                    <span class="fw-bold text-primary fs-5">Rp 25.000</span>
                </div>
                <small class="text-muted">Keyboard, Headset, Motherboard, Power Supply, Router.</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill fw-bold">Ukuran Besar</span>
                    <span class="fw-bold text-warning fs-5">Rp 40.000</span>
                </div>
                <small class="text-muted">Laptop, Monitor, Printer Standar, Casing PC Standar.</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill fw-bold">Ekstra Besar</span>
                    <span class="fw-bold text-danger fs-5">Rp 50.000</span>
                </div>
                <small class="text-muted">PC Rakitan Full Tower, Printer Besar / Packing Kayu.</small>
            </div>
        </div>
    </div>

    <!-- Tabel Master Ekspedisi -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2 text-primary"></i> Daftar Kurir Ekspedisi Terdaftar</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center rounded-start">No</th>
                            <th>Nama Ekspedisi</th>
                            <th>Status Integrasi API</th>
                            <th width="20%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ekspedisi as $index => $e)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">
                                <i class="bi bi-box-seam me-2 text-primary"></i> {{ $e->nama_ekspedisi }}
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                    <i class="bi bi-check2-all me-1"></i> RajaOngkir Otomatis
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $e->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('ekspedisi.destroy', $e->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus ekspedisi {{ $e->nama_ekspedisi }}?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Ekspedisi -->
                        <div class="modal fade" id="modalEdit{{ $e->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Ekspedisi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('ekspedisi.update', $e->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nama Ekspedisi</label>
                                                <input type="text" name="nama_ekspedisi" class="form-control form-control-lg" value="{{ $e->nama_ekspedisi }}" required>
                                            </div>
                                            <input type="hidden" name="ongkir_per_km" value="{{ intval($e->ongkir_per_km) }}">
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data ekspedisi.<br>Silakan tambah ekspedisi baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Ekspedisi -->
    <div class="modal fade" id="modalTambahEkspedisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Ekspedisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ekspedisi.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nama Ekspedisi (Contoh: JNE, TIKI, SiCepat, POS)</label>
                            <input type="text" name="nama_ekspedisi" class="form-control form-control-lg" required autofocus>
                        </div>
                        <input type="hidden" name="ongkir_per_km" value="0">
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Ekspedisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

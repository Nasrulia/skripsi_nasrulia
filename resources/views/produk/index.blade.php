<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Produk</h3>
            <p class="text-muted mb-0">Kelola stok, harga, dan informasi produk toko.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <!-- Fitur Pencarian -->
            <form action="{{ route('produk.index') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light-subtle text-muted px-3" id="search-icon">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-light border-light-subtle py-2 fs-6" placeholder="Cari nama produk, merk, kategori..." value="{{ request('search') }}" aria-describedby="search-icon">
                            @if(request('search'))
                                <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary border-light-subtle d-flex align-items-center" title="Bersihkan Pencarian">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    @if(request('search'))
                        <div class="col-md-6 col-lg-8">
                            <span class="text-muted small">
                                Menampilkan hasil pencarian untuk: <strong class="text-dark">"{{ request('search') }}"</strong> 
                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">{{ $produk->count() }} barang</span>
                            </span>
                        </div>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="7%" class="text-center rounded-start">Foto</th>
                            <th>Nama Produk</th>
                            <th>Merk</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th>Harga Jual</th>
                            <th width="13%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produk as $p)
                        <tr>
                            <td class="text-center">
                                @if($p->foto)
                                   <img src="{{ asset('storage/' . $p->foto) }}" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $p->nama_produk }}">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto text-muted shadow-sm" style="width: 50px; height: 50px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">{{ $p->deskripsi ?? 'Tidak ada deskripsi' }}</small>
                            </td>
                            <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">{{ $p->merk ?? '-' }}</span></td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">{{ $p->kategori->nama_kategori }}</span></td>
                            <td class="text-center">
                                @if($p->stok <= 5)
                                    <span class="badge bg-danger px-2 py-2 rounded-pill shadow-sm" title="Stok Hampir Habis!">{{ $p->stok }}</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">{{ $p->stok }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-primary">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form action="{{ route('produk.destroy', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus produk {{ $p->nama_produk }}?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Produk: {{ $p->nama_produk }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('produk.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-muted small">Kategori Produk</label>
                                                    <select name="kategori_id" class="form-select form-select-lg" required>
                                                        @foreach($kategori as $k)
                                                            <option value="{{ $k->id }}" {{ $p->kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-muted small">Nama Produk</label>
                                                    <input type="text" name="nama_produk" class="form-control form-control-lg" value="{{ $p->nama_produk }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-muted small">Merk / Brand</label>
                                                    <input type="text" name="merk" class="form-control form-control-lg" value="{{ $p->merk }}" placeholder="Contoh: Asus, Samsung, Epson">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-muted small">Stok</label>
                                                    <input type="number" name="stok" class="form-control form-control-lg" value="{{ $p->stok }}" required min="0">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-muted small">Harga Beli (Rp)</label>
                                                    <input type="number" name="harga_beli" class="form-control form-control-lg" value="{{ intval($p->harga_beli) }}" required min="0">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-muted small">Harga Jual (Rp)</label>
                                                    <input type="number" name="harga_jual" class="form-control form-control-lg" value="{{ intval($p->harga_jual) }}" required min="0">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Update Foto Produk (Opsional)</label>
                                                    <input type="file" name="foto" class="form-control" accept="image/*">
                                                    <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengubah foto. Maks: 2MB.</small>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold text-muted small">Deskripsi Produk</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3">{{ $p->deskripsi }}</textarea>
                                                </div>
                                            </div>
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data produk.<br>Klik tombol "Tambah Produk" untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Kategori Produk</label>
                                <select name="kategori_id" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($kategori as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control form-control-lg" placeholder="Contoh: Asus ROG Zephyrus..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Merk / Brand</label>
                                <input type="text" name="merk" class="form-control form-control-lg" placeholder="Contoh: Asus, Samsung, Epson">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Stok</label>
                                <input type="number" name="stok" class="form-control form-control-lg" placeholder="0" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Harga Beli (Rp)</label>
                                <input type="number" name="harga_beli" class="form-control form-control-lg" placeholder="10000000" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" class="form-control form-control-lg" placeholder="12500000" required min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small">Foto Produk</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="text-muted mt-1 d-block">Format: JPG, PNG, WEBP. Maks: 2MB.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Spesifikasi singkat produk..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
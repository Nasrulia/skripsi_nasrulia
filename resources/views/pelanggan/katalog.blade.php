<x-app-layout>
    <style>
        .product-card, .best-seller-card {
            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }
        .best-seller-card {
            background: linear-gradient(145deg, #ffffff, #fffef9);
        }
        .best-seller-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(220, 53, 69, 0.12) !important;
        }
        .text-orange {
            color: #fd7e14;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Katalog Produk NJK</h3>
            <p class="text-muted mb-0">Temukan produk kebutuhan komputer Anda.</p>
        </div>
        
        @if(Auth::user()->peran == 'pelanggan')
        <a href="{{ route('keranjang.index') }}" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-cart3 me-2"></i> Keranjang 
            <span class="badge bg-primary ms-1">{{ count((array) session('keranjang')) }}</span>
        </a>
        @endif
    </div>

    <div class="row g-3 mb-4 align-items-center">
        <!-- Kolom Kategori (Kiri/Atas) -->
        <div class="col-lg-8 order-2 order-lg-1">
            <div class="d-flex gap-2 overflow-auto pb-2 flex-wrap">
                <a href="{{ route('pelanggan.katalog', request()->has('search') ? ['search' => request('search')] : []) }}" class="btn btn-sm btn-outline-primary rounded-pill {{ !request('kategori') ? 'active' : '' }}">Semua</a>
                @foreach($kategori as $k)
                    <a href="{{ route('pelanggan.katalog', array_merge(request()->query(), ['kategori' => $k->id])) }}" class="btn btn-sm btn-outline-primary rounded-pill {{ request('kategori') == $k->id ? 'active' : '' }}">{{ $k->nama_kategori }}</a>
                @endforeach
            </div>
        </div>
        <!-- Kolom Pencarian (Kanan/Atas) -->
        <div class="col-lg-4 order-1 order-lg-2">
            <form action="{{ route('pelanggan.katalog') }}" method="GET">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control ps-5 pe-5 bg-white border-light-subtle py-2 fs-6 rounded-pill shadow-sm" placeholder="Cari barang komputer..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('pelanggan.katalog', request()->except('search')) }}" class="position-absolute top-50 end-0 translate-middle-y pe-3 text-muted text-decoration-none hover-text-dark" title="Bersihkan Pencarian">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(request('search'))
        <div class="mb-3 text-muted small">
            Menampilkan hasil pencarian untuk: <strong class="text-dark">"{{ request('search') }}"</strong>
            <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">{{ $produk->count() }} produk ditemukan</span>
        </div>
    @endif

    @if(!request('kategori') && !request('search') && $bestSellers->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="p-2 bg-danger bg-opacity-10 text-danger rounded-3">
                    <i class="bi bi-fire fs-4"></i>
                </span>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Produk Terlaris (Best Seller)</h4>
                    <p class="text-muted small mb-0">Produk paling populer dan paling banyak dibeli oleh pelanggan</p>
                </div>
            </div>
            <div class="row g-4">
                @foreach($bestSellers as $bp)
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative best-seller-card overflow-hidden" style="border: 1.5px solid rgba(220, 53, 69, 0.2) !important;">
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
                                @if(Auth::user()->peran == 'pelanggan')
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
                                @else
                                    <button class="btn btn-light w-100 rounded-3 text-muted" disabled>
                                        Hanya Mode Tampil
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <hr class="my-5 border-light-subtle">
        </div>
    @endif

    <div class="row g-4">
        @forelse($produk as $p)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 product-card">
                <div class="position-relative">
                    @if($bestSellers->contains('id', $p->id))
                        <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                            <span class="badge bg-danger shadow-sm px-2 py-1.5 rounded-pill d-flex align-items-center gap-1">
                                <i class="bi bi-fire"></i> Best Seller
                            </span>
                        </div>
                    @endif
                    @if($p->foto)
                        <img src="{{ Storage::url($p->foto) }}" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif
                    <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm" style="z-index: 10;">{{ $p->kategori->nama_kategori }}</span>
                </div>
                <div class="card-body">
                    @if($p->merk)
                        <small class="text-info fw-semibold">{{ $p->merk }}</small>
                    @endif
                    <h6 class="fw-bold mb-1 text-dark">{{ $p->nama_produk }}</h6>
                    <p class="text-muted small mb-3 text-truncate">{{ $p->deskripsi ?? 'Ready stock unit premium.' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</span>
                        <span class="small text-muted">Stok: {{ $p->stok }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 px-3">
                    @if(Auth::user()->peran == 'pelanggan')
                        @if($p->stok > 0)
                        <form action="{{ route('keranjang.tambah', $p->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100 rounded-3 fw-semibold">
                                + Keranjang
                            </button>
                        </form>
                        @else
                        <button class="btn btn-secondary w-100 rounded-3" disabled>Stok Habis</button>
                        @endif
                    @else
                        <button class="btn btn-light w-100 rounded-3 text-muted" disabled>
                            Hanya Mode Tampil
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="bi bi-box-seam fs-1 text-muted opacity-50 d-block mb-3"></i>
            <h5 class="text-muted">Produk tidak ditemukan.</h5>
        </div>
        @endforelse
    </div>
</x-app-layout>
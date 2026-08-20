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
        /* Highlight produk dari chatbot */
        @keyframes chatbotHighlight {
            0%   { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.6); }
            50%  { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0.15); }
            100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.0); }
        }
        .product-highlight {
            animation: chatbotHighlight 1.2s ease 0.3s 2;
            border: 2px solid #0d6efd !important;
        }
        /* === MODAL DETAIL PRODUK (Ala Shopee) === */
        .product-img-wrapper {
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .product-img-wrapper .img-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0);
            color: white;
            font-size: 2rem;
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: inherit;
        }
        .product-img-wrapper:hover .img-overlay {
            background: rgba(0,0,0,0.32);
            opacity: 1;
        }
        #modalDetailProduk .modal-dialog {
            max-width: 820px;
        }
        #modalDetailProduk .detail-img {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 12px;
            background: #f8f9fa;
            padding: 8px;
            transition: opacity 0.2s ease;
        }
        #modalDetailProduk .detail-img-placeholder {
            width: 100%;
            height: 240px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 4rem;
        }
        .detail-harga {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0d6efd;
            line-height: 1;
        }
        .divider-dashed {
            border-top: 2px dashed #dee2e6;
            margin: 14px 0;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: 0.78rem;
            color: #6c757d;
            min-width: 80px;
            padding-top: 2px;
        }
        .info-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #212529;
        }
        .btn-beli-modal {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 10px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13,110,253,0.3);
        }
        .btn-beli-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13,110,253,0.4);
        }
        .btn-keranjang-modal {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid #0d6efd;
            transition: all 0.3s ease;
        }
        .stok-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }

        /* === SHOPEE-STYLE VARIANT CHIPS === */
        .variant-chip-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            background: #ffffff;
            font-size: 0.84rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
            outline: none;
        }
        .variant-chip-btn:hover {
            border-color: #0d6efd;
            background: #f8faff;
            color: #0d6efd;
            transform: translateY(-1px);
        }
        .variant-chip-btn.active {
            border-color: #0d6efd;
            background: #eff6ff;
            color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
            font-weight: 700;
        }
        .variant-chip-btn.out-of-stock {
            opacity: 0.45;
            border-style: dashed;
            cursor: not-allowed;
            background: #f8fafc;
        }
        .color-dot-indicator {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid rgba(0,0,0,0.15);
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
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

    @if(isset($highlightId) && $highlightId)
        <div class="alert alert-primary alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 mb-4 py-2 px-3" role="alert" style="font-size:0.9rem;">
            <i class="bi bi-robot fs-5"></i>
            <span>Produk ini direkomendasikan oleh <strong>NJK Assistant</strong>. Klik tombol <strong>+ Keranjang</strong> untuk membelinya.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            @if(isset($bp->total_terjual) && $bp->total_terjual > 0)
                            <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                                <span class="badge bg-warning text-dark shadow-sm px-2 py-1 rounded-pill fw-bold">
                                    Terjual {{ $bp->total_terjual }}
                                </span>
                            </div>
                            @endif
                            <div class="position-relative product-img-wrapper"
                                 data-product="{{ json_encode($bp) }}"
                                 onclick="bukaDetailProdukFromElement(this)"
                                 title="Lihat Detail Produk">
                                @if($bp->foto)
                                    <img src="{{ Storage::url($bp->foto) }}" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                    </div>
                                @endif
                                <div class="img-overlay rounded-top-4">
                                    <i class="bi bi-zoom-in"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($bp->merk)
                                    <small class="text-info fw-semibold">{{ $bp->merk }}</small>
                                @endif
                                <h6 class="fw-bold mb-1 text-dark">{{ $bp->nama_produk }}</h6>
                                
                                @if($bp->is_variant_group && count($bp->variants) > 0)
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        @foreach($bp->variants as $v)
                                            <span class="d-inline-block rounded-circle" style="width:11px;height:11px;background:{{ $v->color_hex }};border:1px solid rgba(0,0,0,0.2);" title="{{ $v->variant_label }}"></span>
                                        @endforeach
                                        <span class="text-muted small ms-1" style="font-size:0.75rem;">({{ count($bp->variants) }} Varian Warna)</span>
                                    </div>
                                @else
                                    <p class="text-muted small mb-3 text-truncate">{{ $bp->deskripsi ?? 'Ready stock unit premium.' }}</p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">Rp {{ number_format($bp->harga_jual, 0, ',', '.') }}</span>
                                    <span class="small text-muted">Stok: {{ $bp->stok }}</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pb-4 px-3">
                                @if(Auth::user()->peran == 'pelanggan')
                                    @if($bp->stok > 0)
                                        @if($bp->is_variant_group)
                                            <button type="button" class="btn btn-danger w-100 rounded-3 fw-semibold" data-product="{{ json_encode($bp) }}" onclick="bukaDetailProdukFromElement(this)">
                                                <i class="bi bi-palette me-1"></i> Pilih Variasi
                                            </button>
                                        @else
                                            <form action="{{ route('keranjang.tambah', $bp->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger w-100 rounded-3 fw-semibold">
                                                    <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                                                </button>
                                            </form>
                                        @endif
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
        @php
            $isHighlighted = isset($highlightId) && (
                $highlightId == $p->id || 
                ($p->is_variant_group && in_array($highlightId, $p->variant_ids ?? []))
            );
        @endphp
        <div class="col-md-3" id="produk-{{ $p->id }}">
            <div class="card border-0 shadow-sm rounded-4 h-100 product-card {{ $isHighlighted ? 'product-highlight' : ''}}">
                <div class="position-relative">
                    @if($p->is_variant_group)
                        <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                            <span class="badge bg-dark bg-opacity-75 shadow-sm px-2.5 py-1.5 rounded-pill d-flex align-items-center gap-1">
                                <i class="bi bi-palette"></i> {{ count($p->variants) }} Variasi Warna
                            </span>
                        </div>
                    @elseif($bestSellers->contains('id', $p->id))
                        <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                            <span class="badge bg-danger shadow-sm px-2 py-1.5 rounded-pill d-flex align-items-center gap-1">
                                <i class="bi bi-fire"></i> Best Seller
                            </span>
                        </div>
                    @endif

                    <div class="product-img-wrapper"
                         data-product="{{ json_encode($p) }}"
                         onclick="bukaDetailProdukFromElement(this)"
                         title="Lihat Detail Produk">
                        @if($p->foto)
                            <img src="{{ Storage::url($p->foto) }}" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 200px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                        <div class="img-overlay rounded-top-4">
                            <i class="bi bi-zoom-in"></i>
                        </div>
                    </div>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm" style="z-index: 10;">{{ $p->kategori->nama_kategori }}</span>
                </div>
                <div class="card-body">
                    @if($p->merk)
                        <small class="text-info fw-semibold">{{ $p->merk }}</small>
                    @endif
                    <h6 class="fw-bold mb-1 text-dark">{{ $p->nama_produk }}</h6>

                    @if($p->is_variant_group && count($p->variants) > 0)
                        <div class="d-flex align-items-center gap-1 mb-2">
                            @foreach($p->variants as $v)
                                <span class="d-inline-block rounded-circle" style="width:11px;height:11px;background:{{ $v->color_hex }};border:1px solid rgba(0,0,0,0.2);" title="{{ $v->variant_label }}"></span>
                            @endforeach
                            <span class="text-muted small ms-1" style="font-size:0.75rem;">(B, C, M, Y)</span>
                        </div>
                    @else
                        <p class="text-muted small mb-3 text-truncate">{{ $p->deskripsi ?? 'Ready stock unit premium.' }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</span>
                        <span class="small text-muted">Stok: {{ $p->stok }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 px-3">
                    @if(Auth::user()->peran == 'pelanggan')
                        @if($p->stok > 0)
                            @if($p->is_variant_group)
                                <button type="button" class="btn btn-outline-primary w-100 rounded-3 fw-semibold" data-product="{{ json_encode($p) }}" onclick="bukaDetailProdukFromElement(this)">
                                    <i class="bi bi-palette me-1"></i> Pilih Variasi
                                </button>
                            @else
                                <form action="{{ route('keranjang.tambah', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-3 fw-semibold">
                                        + Keranjang
                                    </button>
                                </form>
                            @endif
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

    {{-- ============================================================ --}}
    {{-- MODAL DETAIL PRODUK (Ala Shopee)                             --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalDetailProduk" tabindex="-1" aria-labelledby="modalDetailProdukLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="modal-header border-0 pb-0 px-4 pt-4 align-items-start">
                    <div style="flex:1;">
                        <span id="detail-badge-kategori" class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small fw-semibold mb-1 d-inline-block"></span>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetailProdukLabel" style="font-size: 1.15rem;"></h5>
                        <small id="detail-merk" class="text-info fw-semibold"></small>
                    </div>
                    <button type="button" class="btn-close ms-3 mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body px-4 pb-0">
                    <div class="row g-4">
                        <!-- Kiri: Gambar + Info Cepat -->
                        <div class="col-md-5">
                            <div id="detail-img-container" class="mb-3 text-center"></div>
                            <div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-tag me-1"></i>Kategori</span>
                                    <span class="info-value" id="detail-info-kategori">-</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-building me-1"></i>Merk</span>
                                    <span class="info-value" id="detail-info-merk">-</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-boxes me-1"></i>Stok</span>
                                    <span class="info-value" id="detail-info-stok">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kanan: Harga, Variasi, Deskripsi -->
                        <div class="col-md-7">
                            <!-- Harga -->
                            <div class="mb-3">
                                <p class="text-muted small mb-1 fw-semibold" style="letter-spacing:.5px;">HARGA PRODUK</p>
                                <div class="detail-harga" id="detail-harga">Rp 0</div>
                            </div>

                            <!-- Pilihan Variasi Warna (Shopee Style) -->
                            <div id="section-variasi" class="mb-3" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-0" style="letter-spacing: .5px;">
                                        <i class="bi bi-palette-fill me-1 text-primary"></i> Pilihan Variasi Warna:
                                    </label>
                                    <span id="label-variasi-terpilih" class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1" style="font-size:0.82rem;">-</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2" id="container-chips-variasi">
                                    {{-- Diisi secara dinamis oleh JavaScript --}}
                                </div>
                            </div>

                            <div class="divider-dashed"></div>

                            <!-- Deskripsi -->
                            <div class="mb-3">
                                <p class="text-muted small fw-semibold mb-1" style="letter-spacing:.5px;"><i class="bi bi-file-text me-1"></i>DESKRIPSI PRODUK & KOMPATIBILITAS</p>
                                <p class="text-dark" id="detail-deskripsi" style="font-size: 0.86rem; line-height: 1.65; white-space: pre-line;"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer: Tombol Aksi -->
                <div class="modal-footer border-0 pt-2 pb-4 px-4 gap-2" id="detail-footer-aksi">
                    {{-- Diisi oleh JavaScript --}}
                </div>
            </div>
        </div>
    </div>

    @if(isset($highlightId) && $highlightId)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const targetId = {{ $highlightId }};
            // Find card containing target ID
            let el = document.getElementById('produk-' + targetId);
            if (!el) {
                // Search inside cards
                const allCards = document.querySelectorAll('[data-product]');
                for (const c of allCards) {
                    try {
                        const p = JSON.parse(c.getAttribute('data-product'));
                        if (p.id === targetId || (p.variant_ids && p.variant_ids.includes(targetId))) {
                            el = c.closest('.col-md-3');
                            // Open modal with preselected variant
                            bukaDetailProduk(p, targetId);
                            break;
                        }
                    } catch (e) {}
                }
            } else {
                const wrapper = el.querySelector('[data-product]');
                if (wrapper) {
                    try {
                        const p = JSON.parse(wrapper.getAttribute('data-product'));
                        bukaDetailProduk(p, targetId);
                    } catch (e) {}
                }
            }

            if (el) {
                setTimeout(() => {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    </script>
    @endif

    <script>
    let activeSelectedProductId = null;
    let currentProductData = null;

    function bukaDetailProdukFromElement(el) {
        try {
            const raw = el.getAttribute('data-product');
            const p = JSON.parse(raw);
            bukaDetailProduk(p);
        } catch (e) {
            console.error('Error parsing product data:', e);
        }
    }

    function bukaDetailProduk(p, preselectedVariantId = null) {
        currentProductData = p;

        // Header
        document.getElementById('modalDetailProdukLabel').textContent = p.nama_produk;
        document.getElementById('detail-badge-kategori').textContent  = (p.kategori && p.kategori.nama_kategori) ? p.kategori.nama_kategori : 'PRODUK';
        document.getElementById('detail-merk').textContent            = p.merk ? p.merk : '';
        document.getElementById('detail-info-kategori').textContent   = (p.kategori && p.kategori.nama_kategori) ? p.kategori.nama_kategori : '-';
        document.getElementById('detail-info-merk').textContent       = p.merk ? p.merk : '-';
        document.getElementById('detail-deskripsi').textContent       = p.deskripsi || 'Tidak ada deskripsi untuk produk ini.';

        const sectionVariasi   = document.getElementById('section-variasi');
        const containerVariasi = document.getElementById('container-chips-variasi');

        if (p.is_variant_group && p.variants && p.variants.length > 0) {
            sectionVariasi.style.display = 'block';
            containerVariasi.innerHTML = '';

            let defaultIdx = 0;
            if (preselectedVariantId) {
                const foundIdx = p.variants.findIndex(v => v.id === preselectedVariantId);
                if (foundIdx !== -1) defaultIdx = foundIdx;
            }

            p.variants.forEach((v, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `variant-chip-btn ${idx === defaultIdx ? 'active' : ''} ${v.stok <= 0 ? 'out-of-stock' : ''}`;
                btn.id = `chip-variant-${idx}`;
                btn.onclick = () => selectVariant(idx);

                btn.innerHTML = `
                    <span class="color-dot-indicator" style="background:${v.color_hex}"></span>
                    <span>${v.variant_label}</span>
                    <small class="text-muted" style="font-size:0.72rem;">(${v.stok > 0 ? v.stok : 'Habis'})</small>
                `;
                containerVariasi.appendChild(btn);
            });

            selectVariant(defaultIdx);
        } else {
            // Single Product (Non-Variant)
            sectionVariasi.style.display = 'none';
            activeSelectedProductId = p.id;

            // Foto
            renderImage(p.foto ? `/storage/${p.foto}` : null, p.nama_produk);

            // Harga & Stok
            document.getElementById('detail-harga').textContent = 'Rp ' + formatRupiah(p.harga_jual);
            document.getElementById('detail-info-stok').innerHTML = formatStokBadge(p.stok);

            updateFooterActions(p.stok);
        }

        new bootstrap.Modal(document.getElementById('modalDetailProduk')).show();
    }

    function selectVariant(idx) {
        if (!currentProductData || !currentProductData.variants || !currentProductData.variants[idx]) return;

        const v = currentProductData.variants[idx];
        activeSelectedProductId = v.id;

        // Update active chip styling
        currentProductData.variants.forEach((_, i) => {
            const chip = document.getElementById(`chip-variant-${i}`);
            if (chip) {
                if (i === idx) {
                    chip.classList.add('active');
                } else {
                    chip.classList.remove('active');
                }
            }
        });

        // Update variant label badge
        document.getElementById('label-variasi-terpilih').textContent = `${v.variant_label} (${v.nama_produk.replace(/^TINTA\s+/i, '')})`;

        // Update image
        renderImage(v.foto_url || (v.foto ? `/storage/${v.foto}` : null), v.nama_produk);

        // Update price & stock
        document.getElementById('detail-harga').textContent = 'Rp ' + formatRupiah(v.harga_jual);
        document.getElementById('detail-info-stok').innerHTML = formatStokBadge(v.stok);

        // Update description if variant has specific description
        if (v.deskripsi) {
            document.getElementById('detail-deskripsi').textContent = v.deskripsi;
        }

        updateFooterActions(v.stok);
    }

    function renderImage(fotoUrl, altText) {
        const imgContainer = document.getElementById('detail-img-container');
        if (fotoUrl) {
            imgContainer.innerHTML = `<img src="${fotoUrl}" alt="${altText}" class="detail-img shadow-sm">`;
        } else {
            imgContainer.innerHTML = `<div class="detail-img-placeholder"><i class="bi bi-image"></i></div>`;
        }
    }

    function formatStokBadge(stok) {
        if (stok <= 0) {
            return `<span class="stok-dot" style="background:#dc3545;"></span><span class="text-danger fw-semibold">Stok Habis</span>`;
        } else if (stok <= 5) {
            return `<span class="stok-dot" style="background:#fd7e14;"></span><span class="text-warning fw-semibold">${stok} unit (hampir habis!)</span>`;
        } else {
            return `<span class="stok-dot" style="background:#198754;"></span><span class="text-success fw-semibold">${stok} unit tersedia</span>`;
        }
    }

    function updateFooterActions(stok) {
        const footer      = document.getElementById('detail-footer-aksi');
        const isPelanggan = {{ Auth::user()->peran == 'pelanggan' ? 'true' : 'false' }};

        if (isPelanggan && stok > 0) {
            footer.innerHTML = `
                <button type="button" class="btn btn-outline-primary btn-keranjang-modal flex-fill" onclick="submitKeranjangActive()">
                    <i class="bi bi-cart-plus me-1"></i> + Keranjang
                </button>
                <button type="button" class="btn btn-primary btn-beli-modal flex-fill text-white" onclick="submitKeranjangActive()">
                    <i class="bi bi-bag-check me-1"></i> Beli Sekarang
                </button>
            `;
        } else if (isPelanggan && stok <= 0) {
            footer.innerHTML = `<button class="btn btn-secondary w-100 rounded-3" disabled><i class="bi bi-x-circle me-1"></i> Variasi Ini Stok Habis</button>`;
        } else {
            footer.innerHTML = `<p class="text-muted small w-100 text-center mb-0">Login sebagai pelanggan untuk membeli produk ini.</p>`;
        }
    }

    function submitKeranjangActive() {
        if (!activeSelectedProductId) return;
        submitKeranjang(activeSelectedProductId);
    }

    function submitKeranjang(produkId) {
        const form  = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('/keranjang/tambah') }}/${produkId}`;
        const csrf  = document.createElement('input');
        csrf.type   = 'hidden';
        csrf.name   = '_token';
        csrf.value  = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    function formatRupiah(angka) {
        return Math.round(angka).toLocaleString('id-ID');
    }
    </script>

</x-app-layout>
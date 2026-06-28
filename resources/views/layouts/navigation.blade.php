<nav id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo NJK" class="img-fluid mb-2" id="sidebarLogo" style="max-height: 55px; object-fit: contain;">
    </div>

    <ul class="list-unstyled components">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard Utama
            </a>
        </li>

        @if(Auth::user()->peran == 'admin')
            <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Master Data</li>
            <li>
                <a href="{{ route('kategori.index') }}" class="{{ request()->is('kategori*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i> Kategori Produk
                </a>
            </li>
            <li>
                <a href="{{ route('produk.index') }}" class="{{ request()->is('produk*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Data Produk
                </a>
            </li>
            <li>
                <a href="{{ route('ekspedisi.index') }}" class="{{ request()->is('ekspedisi*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Ekspedisi
                </a>
            </li>
            <li>
                <a href="{{ route('data-teknisi.index') }}" class="{{ request()->is('data-teknisi*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Data Teknisi
                </a>
            </li>

            <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Laporan Toko</li>
            <li>
                <a href="{{ route('laporan.index') }}" class="{{ request()->is('laporan*') ? 'active' : '' }}">
                    <i class="bi bi-printer-fill"></i> Cetak Laporan
                </a>
            </li>
        @endif

        @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir')
            <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Transaksi Penjualan</li>
            <li>
                <a href="{{ route('transaksi.index') }}" class="{{ request()->is('transaksi*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check-fill"></i> Kasir & Transaksi
                </a>
            </li>
        @endif

        @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'kasir' || Auth::user()->peran == 'teknisi')
            <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Layanan Teknisi</li>
            <li>
                <a href="{{ route('teknisi.servis') }}" class="{{ request()->is('teknisi/servis') || request()->is('teknisi/semua-servis') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i> Kelola Servis
                </a>
            </li>
        @endif

        @if(Auth::user()->peran != 'teknisi')
            <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Belanja</li>
            <li>
                <a href="{{ route('pelanggan.katalog') }}" class="{{ request()->is('katalog*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Katalog Produk
                </a>
            </li>
        @endif

        @if(Auth::user()->peran == 'pelanggan')
            <li>
                <a href="{{ route('keranjang.index') }}" class="{{ request()->is('keranjang*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Keranjang Saya
                </a>
            </li>
            <li>
                <a href="{{ route('pesanan.saya') }}" class="{{ request()->routeIs('pesanan.saya') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i> Pesanan Saya
                </a>
            </li>
        @endif

        <li class="px-4 mt-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #565674; letter-spacing: 1px;">Fitur AI</li>
        
        @if(Auth::user()->peran == 'admin')
            <li>
                <a href="{{ route('aturan-chatbot.index') }}" class="{{ request()->is('aturan-chatbot*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Kelola Aturan Chatbot
                </a>
            </li>
        @endif
        
        <li>
            <a href="{{ route('konsultasi') }}" class="{{ request()->is('konsultasi*') ? 'active' : '' }}">
                <i class="bi bi-robot"></i> {{ Auth::user()->peran == 'pelanggan' ? 'Konsultasi AI' : 'Tes Chatbot AI' }}
            </a>
        </li>
    </ul>
</nav>

<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Keranjang Belanja</h3>
            <p class="text-muted mb-0">Periksa pesanan Anda dan pilih metode pengiriman atau ambil di toko.</p>
        </div>
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Lanjut Belanja
        </a>
    </div>

    @php 
        $subtotal = 0;
        $cartCount = count((array) session('keranjang'));
    @endphp
    @forelse((array) session('keranjang') as $id => $details)
        @php $subtotal += $details['harga'] * $details['jumlah'] @endphp
    @empty
    @endforelse

    <div class="row">
        <div class="col-lg-7">
            <!-- 1. DAFTAR PRODUK -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cart3 me-2 text-primary"></i> Daftar Produk</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse((array) session('keranjang') as $id => $details)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ Storage::url($details['foto']) }}" width="45" height="45" class="rounded-3 me-3 object-fit-cover shadow-sm" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23f1f5f9%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%2394a3b8%22 font-size=%2228%22>?</text></svg>'">
                                            <span class="fw-semibold text-dark">{{ $details['nama'] }}</span>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($details['harga'], 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold">{{ $details['jumlah'] }}</td>
                                    <td class="text-end fw-bold text-primary">Rp {{ number_format($details['harga'] * $details['jumlah'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('keranjang.hapus', $id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle p-2" title="Hapus Item"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-cart-x fs-1 text-muted opacity-50 d-block mb-2"></i>
                                        <span class="text-muted">Keranjang belanja Anda masih kosong.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. FORM CHECKOUT & PENGIRIMAN -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2 text-primary"></i> Pilihan Pengiriman & Pengambilan</h6>
                
                <form action="{{ route('checkout') }}" method="POST" id="formCheckout">
                    @csrf
                    
                    <!-- Hidden Form Inputs for Shipping Details -->
                    <input type="hidden" name="nama_ekspedisi" id="inputNamaEkspedisi" value="">
                    <input type="hidden" name="layanan_ekspedisi" id="inputLayananEkspedisi" value="">
                    <input type="hidden" name="estimasi_pengiriman" id="inputEstimasiPengiriman" value="">
                    <input type="hidden" name="provinsi_tujuan" id="inputProvinsiTujuan" value="">
                    <input type="hidden" name="kota_tujuan" id="inputKotaTujuan" value="">
                    <input type="hidden" name="ongkir" id="inputOngkir" value="0">
                    <input type="hidden" name="biaya_packing" id="inputBiayaPacking" value="0">

                    <!-- Opsi Metode Pengambilan -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">Pilih Metode</label>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="card h-100 p-3 border-2 rounded-4 cursor-pointer radio-card active-method" id="cardMetodeDiambil" for="metode_diambil" style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <input class="form-check-input me-3" type="radio" name="metode_pengambilan" id="metode_diambil" value="diambil" checked onchange="togglePengiriman()">
                                        <div>
                                            <span class="fw-bold d-block text-dark"><i class="bi bi-shop text-success me-1"></i> Ambil di Toko</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">Gratis tanpa ongkir / packing</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="card h-100 p-3 border-2 rounded-4 cursor-pointer radio-card" id="cardMetodeDiantar" for="metode_diantar" style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <input class="form-check-input me-3" type="radio" name="metode_pengambilan" id="metode_diantar" value="diantar" onchange="togglePengiriman()">
                                        <div>
                                            <span class="fw-bold d-block text-dark"><i class="bi bi-box-seam-fill text-primary me-1"></i> Kirim Ekspedisi</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">Otomatis via API RajaOngkir</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 2A. FORM AMBIL DI TOKO -->
                    <div id="formAmbilToko" style="display: block;">
                        <div class="alert alert-success border-0 rounded-4 p-3 mb-3 d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill fs-4 text-success me-3"></i>
                            <div>
                                <small class="fw-bold d-block text-success">Lokasi Toko Nusantara Jaya Computer:</small>
                                <small class="text-dark">Jl. Pahlawan No. 88 (Kampung Melayu), Banjarmasin, Kalimantan Selatan.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Estimasi Tanggal & Waktu Pengambilan</label>
                            <input type="datetime-local" name="estimasi_diambil" id="estimasiDiambil" class="form-control form-control-lg rounded-3" min="{{ now()->format('Y-m-d\TH:i') }}" onchange="checkPickupRules()" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode Pembayaran di Toko</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="pembayaran_cash" value="cash" checked onchange="checkPickupRules()">
                                    <label class="form-check-label fw-semibold" for="pembayaran_cash">
                                        <i class="bi bi-cash me-1 text-success"></i> Cash di Toko (Bayar Saat Ambil)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="pembayaran_transfer" value="transfer" onchange="checkPickupRules()">
                                    <label class="form-check-label fw-semibold" for="pembayaran_transfer">
                                        <i class="bi bi-credit-card me-1 text-primary"></i> Transfer Bank
                                    </label>
                                </div>
                            </div>
                            <div id="pickupNotice" class="alert alert-info py-2 px-3 small mt-3 mb-0 border-0 rounded-3" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- 2B. FORM PENGIRIMAN RAJAONGKIR -->
                    <div id="formPengiriman" style="display: none;">
                        <div class="alert alert-primary border-0 rounded-4 p-3 mb-4 d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 text-primary me-3"></i>
                            <div>
                                <small class="fw-bold d-block text-primary">Pengiriman Resmi dari Banjarmasin:</small>
                                <small class="text-dark">Ongkos kirim dihitung otomatis oleh API RajaOngkir berdasarkan tujuan, kurir, dan berat barang.</small>
                            </div>
                        </div>

                        <!-- Langkah 1: Pilih Provinsi dan Kota Tujuan -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <span class="badge bg-primary me-1">1</span> Provinsi Tujuan
                                </label>
                                <select class="form-select form-select-lg rounded-3" id="selectProvinsi" onchange="onProvinsiChange()">
                                    <option value="" disabled selected>-- Pilih Provinsi --</option>
                                    @foreach($provinces as $p)
                                        <option value="{{ $p['province_id'] }}" data-name="{{ $p['province'] }}">{{ $p['province'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <span class="badge bg-primary me-1">2</span> Kota / Kabupaten Tujuan
                                </label>
                                <select class="form-select form-select-lg rounded-3" id="selectKota" onchange="onKotaChange()" disabled>
                                    <option value="" disabled selected>-- Pilih Provinsi Dulu --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Langkah 2: Pilih Kurir Ekspedisi -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <span class="badge bg-primary me-1">3</span> Pilih Kurir Ekspedisi
                            </label>
                            <div class="row g-2" id="courierRadioGroup">
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="kurir_code" id="kurir_jne" value="jne" autocomplete="off" checked onchange="fetchShippingRates()">
                                    <label class="btn btn-outline-primary w-100 py-2 rounded-3 text-center fw-bold" for="kurir_jne">
                                        <i class="bi bi-truck d-block mb-1 fs-5"></i> JNE
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="kurir_code" id="kurir_pos" value="pos" autocomplete="off" onchange="fetchShippingRates()">
                                    <label class="btn btn-outline-primary w-100 py-2 rounded-3 text-center fw-bold" for="kurir_pos">
                                        <i class="bi bi-envelope-paper d-block mb-1 fs-5"></i> POS Indo
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="kurir_code" id="kurir_tiki" value="tiki" autocomplete="off" onchange="fetchShippingRates()">
                                    <label class="btn btn-outline-primary w-100 py-2 rounded-3 text-center fw-bold" for="kurir_tiki">
                                        <i class="bi bi-send d-block mb-1 fs-5"></i> TIKI
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="kurir_code" id="kurir_sicepat" value="sicepat" autocomplete="off" onchange="fetchShippingRates()">
                                    <label class="btn btn-outline-primary w-100 py-2 rounded-3 text-center fw-bold" for="kurir_sicepat">
                                        <i class="bi bi-lightning-charge d-block mb-1 fs-5"></i> SiCepat
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 3: Pilih Paket Layanan Ongkir -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center">
                                <span><span class="badge bg-primary me-1">4</span> Pilihan Paket Layanan Ongkir</span>
                                <span id="loadingOngkir" class="text-primary small fw-semibold" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menghitung ongkir...
                                </span>
                            </label>
                            
                            <div id="serviceRatesContainer">
                                <div class="alert alert-light border text-muted py-3 text-center rounded-3">
                                    <i class="bi bi-geo-alt me-1"></i> Silakan pilih Provinsi dan Kota Tujuan untuk melihat pilihan paket pengiriman ekspedisi.
                                </div>
                            </div>
                        </div>

                        <!-- 2C. RINCIAN ONGKOS PACKING BARANG -->
                        <div class="card border border-warning-subtle bg-warning bg-opacity-10 rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning text-dark rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-box2 me-1"></i> Ongkos Packing Proteksi Barang</h6>
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6" id="packingBadgeText">
                                            Rp {{ number_format($packingInfo['biaya'] ?? 15000, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="small text-dark mb-1" id="packingDescText">
                                        <strong>{{ $packingInfo['nama'] ?? 'Ukuran Sedang' }}</strong>: {{ $packingInfo['deskripsi'] ?? 'Bubble wrap + Kardus tebal' }}
                                    </p>
                                    <div class="d-flex gap-3 text-muted small mt-2">
                                        <span><i class="bi bi-speedometer2 me-1"></i> Total Berat: <strong><span id="beratCartText">{{ number_format(($totalBeratGram ?? 1000) / 1000, 2) }}</span> kg</strong></span>
                                        <span><i class="bi bi-shield-lock me-1"></i> Bubble Wrap + Kardus Aman</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 4: Detail Alamat Lengkap -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <span class="badge bg-primary me-1">5</span> Alamat Lengkap Pengiriman
                            </label>
                            <div class="alert alert-info py-2 px-3 small mb-2 border-0 rounded-3">
                                <i class="bi bi-info-circle-fill me-1"></i> Mohon tuliskan alamat selengkap mungkin (Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Patokan Terdekat) agar paket sampai dengan cepat.
                            </div>
                            <textarea name="alamat_pengiriman" id="alamatPengiriman" class="form-control rounded-3" rows="3" placeholder="Contoh: Jl. Ahmad Yani Km 5.5, Komp. Tirta Dharma No. 12 RT 04 (Pagar Hitam Depan Masjid)..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" id="btnCheckoutSubmit" class="btn btn-primary btn-lg fw-bold rounded-4 px-5 py-3 shadow-sm" {{ $subtotal == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-2"></i> CHECKOUT SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. RINGKASAN PESANAN (STICKY SIDEBAR) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white position-sticky" style="top: 80px;">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i> Ringkasan Pembayaran</h5>
                <hr class="opacity-25">
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal ({{ $cartCount }} produk):</span>
                    <span class="fw-bold" id="subtotalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2" id="ongkirRow" style="display: none;">
                    <span id="labelOngkir">Ongkir Ekspedisi:</span>
                    <span class="fw-bold" id="ongkirText">Rp 0</span>
                </div>

                <div class="d-flex justify-content-between mb-2" id="packingRow" style="display: none;">
                    <span>Ongkos Packing:</span>
                    <span class="fw-bold" id="packingSummaryText">Rp 0</span>
                </div>
                
                <hr class="opacity-25">
                
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Total Bayar:</h5>
                    <h4 class="fw-bold mb-0" id="totalBayarText">Rp {{ number_format($subtotal, 0, ',', '.') }}</h4>
                </div>

                <div class="alert alert-light text-dark mt-3 mb-0 rounded-4 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-whatsapp text-success fs-4 me-3"></i>
                        <small class="lh-sm">Pesanan akan diverifikasi oleh Admin. Notifikasi dan rincian transaksi otomatis dikirimkan via WhatsApp.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const subtotalProduk = {{ $subtotal }};
    let currentOngkir = 0;
    let currentBiayaPacking = {{ $packingInfo['biaya'] ?? 15000 }};
    let selectedCourierService = null;

    function togglePengiriman() {
        const metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        const formPengiriman = document.getElementById('formPengiriman');
        const formAmbilToko = document.getElementById('formAmbilToko');
        const ongkirRow = document.getElementById('ongkirRow');
        const packingRow = document.getElementById('packingRow');
        const cardDiambil = document.getElementById('cardMetodeDiambil');
        const cardDiantar = document.getElementById('cardMetodeDiantar');
        const inputOngkir = document.getElementById('inputOngkir');
        const inputBiayaPacking = document.getElementById('inputBiayaPacking');

        if (metode === 'diantar') {
            formPengiriman.style.display = 'block';
            formAmbilToko.style.display = 'none';
            ongkirRow.style.display = 'flex';
            packingRow.style.display = 'flex';
            cardDiantar.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            cardDiambil.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');

            document.getElementById('estimasiDiambil').required = false;
            document.getElementById('alamatPengiriman').required = true;

            inputBiayaPacking.value = currentBiayaPacking;
            document.getElementById('packingSummaryText').innerText = 'Rp ' + currentBiayaPacking.toLocaleString('id-ID');

            updateTotalRingkasan();
        } else {
            formPengiriman.style.display = 'none';
            formAmbilToko.style.display = 'block';
            ongkirRow.style.display = 'none';
            packingRow.style.display = 'none';
            cardDiambil.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            cardDiantar.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');

            document.getElementById('estimasiDiambil').required = true;
            document.getElementById('alamatPengiriman').required = false;

            currentOngkir = 0;
            inputOngkir.value = 0;
            inputBiayaPacking.value = 0;
            document.getElementById('ongkirText').innerText = 'Rp 0';
            document.getElementById('packingSummaryText').innerText = 'Rp 0';

            updateTotalRingkasan();
        }

        checkPickupRules();
    }

    async function onProvinsiChange() {
        const provSelect = document.getElementById('selectProvinsi');
        const kotaSelect = document.getElementById('selectKota');
        const provId = provSelect.value;
        const provName = provSelect.options[provSelect.selectedIndex].getAttribute('data-name');
        
        document.getElementById('inputProvinsiTujuan').value = provName;

        kotaSelect.innerHTML = '<option value="" disabled selected>Memuat daftar kota...</option>';
        kotaSelect.disabled = true;

        try {
            const res = await fetch(`{{ url('/api/rajaongkir/cities') }}/${provId}`);
            const json = await res.json();

            if (json.status === 'success' && json.data.length > 0) {
                kotaSelect.innerHTML = '<option value="" disabled selected>-- Pilih Kota / Kabupaten --</option>';
                json.data.forEach(city => {
                    const cityName = `${city.type} ${city.city_name}`;
                    const opt = document.createElement('option');
                    opt.value = city.city_id;
                    opt.setAttribute('data-name', cityName);
                    opt.textContent = cityName;
                    kotaSelect.appendChild(opt);
                });
                kotaSelect.disabled = false;
            } else {
                kotaSelect.innerHTML = '<option value="" disabled selected>Gagal memuat kota</option>';
            }
        } catch (e) {
            console.error('Error fetching cities:', e);
            kotaSelect.innerHTML = '<option value="" disabled selected>Koneksi error</option>';
        }
    }

    function onKotaChange() {
        const kotaSelect = document.getElementById('selectKota');
        const cityName = kotaSelect.options[kotaSelect.selectedIndex].getAttribute('data-name');
        document.getElementById('inputKotaTujuan').value = cityName;
        fetchShippingRates();
    }

    async function fetchShippingRates() {
        const kotaSelect = document.getElementById('selectKota');
        const destinationCityId = kotaSelect.value;
        if (!destinationCityId) return;

        const courierInput = document.querySelector('input[name="kurir_code"]:checked');
        const courier = courierInput ? courierInput.value : 'jne';

        const loading = document.getElementById('loadingOngkir');
        const container = document.getElementById('serviceRatesContainer');

        loading.style.display = 'inline-block';
        container.innerHTML = `
            <div class="text-center py-3 text-muted">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                Mengambil opsi tarif pengiriman ${courier.toUpperCase()} dari RajaOngkir...
            </div>
        `;

        try {
            const res = await fetch(`{{ route('rajaongkir.check-ongkir') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    destination_city_id: destinationCityId,
                    courier: courier
                })
            });

            const json = await res.json();
            loading.style.display = 'none';

            if (json.status === 'success' && json.shipping && json.shipping.services.length > 0) {
                // Update packing & weight info
                if (json.packing) {
                    currentBiayaPacking = json.packing.biaya;
                    document.getElementById('inputBiayaPacking').value = currentBiayaPacking;
                    document.getElementById('packingBadgeText').innerText = 'Rp ' + currentBiayaPacking.toLocaleString('id-ID');
                    document.getElementById('packingDescText').innerHTML = `<strong>${json.packing.nama}</strong>: ${json.packing.deskripsi}`;
                    document.getElementById('packingSummaryText').innerText = 'Rp ' + currentBiayaPacking.toLocaleString('id-ID');
                }
                if (json.total_weight_kg) {
                    document.getElementById('beratCartText').innerText = json.total_weight_kg;
                }

                // Render service cards
                container.innerHTML = '';
                const courierName = json.shipping.courier || courier.toUpperCase();

                json.shipping.services.forEach((srv, idx) => {
                    const isChecked = idx === 0 ? 'checked' : '';
                    if (idx === 0) {
                        selectServiceRate(courierName, srv.service, srv.cost, srv.etd);
                    }

                    const cardHtml = `
                        <label class="card border rounded-3 p-3 mb-2 cursor-pointer service-rate-card ${idx === 0 ? 'border-primary bg-light' : ''}" style="cursor: pointer;" for="srv_${idx}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-3" type="radio" name="selected_service" id="srv_${idx}" value="${srv.cost}" ${isChecked} onchange="selectServiceRate('${courierName}', '${srv.service}', ${srv.cost}, '${srv.etd}')">
                                    <div>
                                        <strong class="text-dark d-block">${courierName} - ${srv.service}</strong>
                                        <small class="text-muted">${srv.description} &bull; Estimasi: <span class="text-primary fw-semibold">${srv.etd}</span></small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-primary fs-5">Rp ${srv.cost.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                        </label>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHtml);
                });
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning py-3 rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> Tidak ditemukan layanan untuk kurir ${courier.toUpperCase()} ke kota tersebut. Silakan coba kurir lain.
                    </div>
                `;
            }
        } catch (e) {
            loading.style.display = 'none';
            console.error('Error calculating shipping:', e);
            container.innerHTML = `
                <div class="alert alert-danger py-3 rounded-3">
                    <i class="bi bi-x-circle me-1"></i> Gagal menghubungi layanan ongkos kirim. Silakan coba lagi.
                </div>
            `;
        }
    }

    function selectServiceRate(courierName, serviceName, cost, etd) {
        currentOngkir = parseInt(cost);
        document.getElementById('inputNamaEkspedisi').value = courierName;
        document.getElementById('inputLayananEkspedisi').value = serviceName;
        document.getElementById('inputEstimasiPengiriman').value = etd;
        document.getElementById('inputOngkir').value = currentOngkir;

        document.getElementById('labelOngkir').innerText = `Ongkir (${courierName} ${serviceName}):`;
        document.getElementById('ongkirText').innerText = 'Rp ' + currentOngkir.toLocaleString('id-ID');

        // Update active class on cards
        document.querySelectorAll('.service-rate-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-light');
        });
        const activeRadio = document.querySelector('input[name="selected_service"]:checked');
        if (activeRadio && activeRadio.closest('.service-rate-card')) {
            activeRadio.closest('.service-rate-card').classList.add('border-primary', 'bg-light');
        }

        updateTotalRingkasan();
    }

    function updateTotalRingkasan() {
        const metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        let total = subtotalProduk;

        if (metode === 'diantar') {
            total = subtotalProduk + currentOngkir + currentBiayaPacking;
        }

        document.getElementById('totalBayarText').innerText = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    }

    function checkPickupRules() {
        const metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        const pickupNotice = document.getElementById('pickupNotice');
        const estimasiInput = document.getElementById('estimasiDiambil');
        const cashRadio = document.getElementById('pembayaran_cash');
        const transferRadio = document.getElementById('pembayaran_transfer');
        
        if (metode !== 'diambil') {
            pickupNotice.style.display = 'none';
            return;
        }
        
        const total = subtotalProduk;
        const dateVal = estimasiInput.value;
        
        if (!dateVal) {
            pickupNotice.style.display = 'none';
            return;
        }
        
        const estimasi = new Date(dateVal);
        const now = new Date();
        const diffTime = estimasi - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (total < 500000) {
            if (diffDays > 3) {
                pickupNotice.style.display = 'block';
                pickupNotice.className = 'alert alert-danger py-2 px-3 small mt-2 mb-0 border-0 rounded-3';
                pickupNotice.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Pesanan di bawah Rp 500.000 tidak bisa diambil lebih dari 3 hari. Silakan pilih tanggal lain.';
                estimasiInput.value = '';
            } else {
                pickupNotice.style.display = 'block';
                pickupNotice.className = 'alert alert-info py-2 px-3 small mt-2 mb-0 border-0 rounded-3';
                pickupNotice.innerHTML = '<i class="bi bi-info-circle-fill me-1"></i> Pengambilan dalam 3 hari. Tanpa DP.';
            }
        } else {
            if (diffDays > 7) {
                pickupNotice.style.display = 'block';
                pickupNotice.className = 'alert alert-warning py-2 px-3 small mt-2 mb-0 border-0 rounded-3';
                
                let dpAmountText = total < 2000000 ? "Rp 200.000 (Flat)" : "Rp " + Math.round(total * 0.2).toLocaleString('id-ID') + " (20%)";
                pickupNotice.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i> Pengambilan lebih dari 1 minggu. <strong>Wajib DP sebesar ' + dpAmountText + ' via Transfer Bank</strong>.';
                
                cashRadio.disabled = true;
                transferRadio.checked = true;
            } else {
                pickupNotice.style.display = 'none';
                cashRadio.disabled = false;
            }
        }
    }
    </script>
</x-app-layout>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">Keranjang Belanja</h3>
            <p class="text-muted mb-0">Periksa kembali pesanan Anda sebelum checkout.</p>
        </div>
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Lanjut Belanja
        </a>
    </div>

    @php $total = 0; $subtotal = 0; @endphp
    @forelse((array) session('keranjang') as $id => $details)
        @php $subtotal += $details['harga'] * $details['jumlah'] @endphp
    @empty
    @endforelse

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cart me-2"></i> Daftar Produk</h6>
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse((array) session('keranjang') as $id => $details)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Storage::url($details['foto']) }}" width="50" class="rounded me-3" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2230%22>?</text></svg>'">
                                        <span class="fw-semibold">{{ $details['nama'] }}</span>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($details['harga'], 0, ',', '.') }}</td>
                                <td>{{ $details['jumlah'] }}</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($details['harga'] * $details['jumlah'], 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('keranjang.hapus', $id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5">
                                <i class="bi bi-cart-x fs-1 text-muted d-block mb-2"></i>
                                Keranjang belanja kosong.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i> Metode Pengiriman</h6>
                <form action="{{ route('checkout') }}" method="POST" id="formCheckout">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pengambilan</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="metode_diambil" value="diambil" checked onchange="togglePengiriman()">
                                <label class="form-check-label fw-semibold" for="metode_diambil">
                                    <i class="bi bi-shop me-1"></i> Ambil di Toko
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="metode_diantar" value="diantar" onchange="togglePengiriman()">
                                <label class="form-check-label fw-semibold" for="metode_diantar">
                                    <i class="bi bi-truck me-1"></i> Dikirim
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="formAmbilToko" style="display: block;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Estimasi Tanggal & Waktu Pengambilan</label>
                            <input type="datetime-local" name="estimasi_diambil" id="estimasiDiambil" class="form-control form-control-lg" min="{{ now()->format('Y-m-d\TH:i') }}" onchange="checkPickupRules()" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode Pembayaran</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="pembayaran_cash" value="cash" checked onchange="checkPickupRules()">
                                    <label class="form-check-label fw-semibold" for="pembayaran_cash">
                                        <i class="bi bi-cash me-1 text-success"></i> Cash di Toko
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="pembayaran_transfer" value="transfer" onchange="checkPickupRules()">
                                    <label class="form-check-label fw-semibold" for="pembayaran_transfer">
                                        <i class="bi bi-credit-card me-1 text-primary"></i> Transfer Bank
                                    </label>
                                </div>
                            </div>
                            <div id="pickupNotice" class="alert alert-info py-2 px-3 small mt-2 mb-0 border-0 rounded-3" style="display: none;"></div>
                        </div>
                    </div>

                    <div id="formPengiriman" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Ekspedisi</label>
                            <select name="ekspedisi_id" class="form-select form-select-lg" id="ekspedisiSelect" onchange="hitungOngkir()">
                                <option value="">-- Pilih Ekspedisi --</option>
                                @foreach($ekspedisi as $e)
                                    <option value="{{ $e->id }}" data-ongkir="{{ $e->ongkir_per_km }}">{{ $e->nama_ekspedisi }} (Rp {{ number_format($e->ongkir_per_km, 0, ',', '.') }}/km)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jarak (km)</label>
                            <input type="number" name="jarak_km" id="jarakKm" class="form-control form-control-lg bg-light" placeholder="Jarak dihitung otomatis dari peta..." min="0" step="0.01" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary"><i class="bi bi-map-fill me-1"></i> Tentukan Lokasi Pengiriman di Peta</label>
                            <div class="input-group mb-2">
                                <input type="text" id="alamatSearch" class="form-control" placeholder="Cari jalan, kelurahan, atau daerah di Banjarmasin...">
                                <button type="button" class="btn btn-primary" onclick="cariAlamat()">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                            <div id="map" class="rounded border shadow-sm mb-2" style="height: 300px; z-index: 1;"></div>
                            <small class="text-muted"><i class="bi bi-info-circle-fill me-1"></i> Geser penanda merah ke lokasi Anda. Jarak & ongkir akan dihitung otomatis.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Pengiriman</label>
                            <textarea name="alamat_pengiriman" id="alamatPengiriman" class="form-control" rows="3" placeholder="Masukkan alamat lengkap detail (RT/RW, No. Rumah)..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3 px-5" {{ $subtotal == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check2-circle me-2"></i> CHECKOUT SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white position-sticky" style="top: 80px;">
                <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i> Ringkasan Pesanan</h5>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal ({{ count((array) session('keranjang')) }} produk):</span>
                    <span class="fw-bold" id="subtotalText">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2" id="ongkirRow" style="display: none !important;">
                    <span>Ongkos Kirim:</span>
                    <span class="fw-bold" id="ongkirText">Rp 0</span>
                </div>
                <hr class="opacity-25">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold">Total Bayar:</h5>
                    <h4 class="fw-bold" id="totalBayarText">Rp {{ number_format($subtotal, 0, ',', '.') }}</h4>
                </div>
                <div class="alert alert-light text-dark mt-3 mb-0 rounded-3">
                    <small><i class="bi bi-info-circle me-1"></i> Pesanan akan diverifikasi oleh admin. Kami akan mengirimkan notifikasi melalui WhatsApp.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
    var map;
    var markerPelanggan;
    // Koordinat Toko: Nusantara Jaya Computer Banjarmasin
    const koordinatToko = [-3.3224, 114.5946];

    function togglePengiriman() {
        var metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        var formPengiriman = document.getElementById('formPengiriman');
        var formAmbilToko = document.getElementById('formAmbilToko');
        var ongkirRow = document.getElementById('ongkirRow');
        
        if (metode === 'diantar') {
            formPengiriman.style.display = 'block';
            formAmbilToko.style.display = 'none';
            ongkirRow.style.display = 'flex !important';
            document.getElementById('estimasiDiambil').required = false;
            document.getElementById('alamatPengiriman').required = true;
            document.getElementById('jarakKm').required = true;
            setTimeout(function() {
                initMap();
            }, 200);
        } else {
            formPengiriman.style.display = 'none';
            formAmbilToko.style.display = 'block';
            ongkirRow.style.display = 'none !important';
            document.getElementById('estimasiDiambil').required = true;
            document.getElementById('alamatPengiriman').required = false;
            document.getElementById('jarakKm').required = false;
            document.getElementById('ongkirText').innerText = 'Rp 0';
            hitungTotal();
        }
        checkPickupRules();
    }

    function checkPickupRules() {
        var metode = document.querySelector('input[name="metode_pengambilan"]:checked').value;
        var pickupNotice = document.getElementById('pickupNotice');
        var estimasiInput = document.getElementById('estimasiDiambil');
        var cashRadio = document.getElementById('pembayaran_cash');
        var transferRadio = document.getElementById('pembayaran_transfer');
        
        if (metode !== 'diambil') {
            pickupNotice.style.display = 'none';
            return;
        }
        
        var total = {{ $subtotal }};
        var dateVal = estimasiInput.value;
        
        if (!dateVal) {
            pickupNotice.style.display = 'none';
            return;
        }
        
        var estimasi = new Date(dateVal);
        var now = new Date();
        var diffTime = estimasi - now;
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (total < 500000) {
            // Aksesoris < 500rb
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
            // >= 500rb
            if (diffDays > 7) {
                pickupNotice.style.display = 'block';
                pickupNotice.className = 'alert alert-warning py-2 px-3 small mt-2 mb-0 border-0 rounded-3';
                
                var dpAmountText = "";
                if (total < 2000000) {
                    dpAmountText = "Rp 200.000 (Flat)";
                } else {
                    dpAmountText = "Rp " + Math.round(total * 0.2).toLocaleString('id-ID') + " (20%)";
                }
                
                pickupNotice.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i> Pengambilan lebih dari 1 minggu. <strong>Wajib melakukan DP sebesar ' + dpAmountText + ' via Transfer Bank</strong>.';
                
                // Force transfer payment
                cashRadio.disabled = true;
                transferRadio.checked = true;
            } else {
                pickupNotice.style.display = 'none';
                cashRadio.disabled = false;
            }
        }
    }

    function initMap() {
        if (map) {
            map.invalidateSize();
            return;
        }

        // Setup Leaflet Map
        map = L.map('map').setView(koordinatToko, 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marker Toko (Blue)
        L.marker(koordinatToko, {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map)
        .bindPopup("<b>Nusantara Jaya Computer</b><br>Jalan Kamp Melayu No. 88, Banjarmasin")
        .openPopup();

        // Marker Pelanggan (Red, Draggable)
        markerPelanggan = L.marker(koordinatToko, {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map)
        .bindPopup("<b>Lokasi Pengiriman Anda</b><br>Geser ke alamat rumah Anda.")
        .openPopup();

        markerPelanggan.on('dragend', function(e) {
            updateJarakDanAlamat();
        });

        map.on('click', function(e) {
            markerPelanggan.setLatLng(e.latlng);
            updateJarakDanAlamat();
        });
    }

    function updateJarakDanAlamat() {
        var pos = markerPelanggan.getLatLng();
        var jarak = getDistanceFromLatLonInKm(koordinatToko[0], koordinatToko[1], pos.lat, pos.lng);
        document.getElementById('jarakKm').value = jarak.toFixed(2);

        // Reverse Geocoding Nominatim
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('alamatPengiriman').value = data.display_name;
                }
            })
            .catch(err => console.log(err));

        hitungOngkir();
    }

    function cariAlamat() {
        var query = document.getElementById('alamatSearch').value;
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ' Banjarmasin')}&limit=1`)
            .then(response => response.json())
            .then(results => {
                if (results && results.length > 0) {
                    var lat = parseFloat(results[0].lat);
                    var lon = parseFloat(results[0].lon);
                    map.setView([lat, lon], 16);
                    markerPelanggan.setLatLng([lat, lon]);
                    updateJarakDanAlamat();
                } else {
                    alert('Lokasi tidak ditemukan. Coba masukkan nama jalan/kelurahan yang lebih detail.');
                }
            })
            .catch(err => {
                console.log(err);
                alert('Pencarian gagal. Periksa koneksi internet Anda.');
            });
    }

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius bumi (km)
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c; 
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function hitungOngkir() {
        var subtotal = {{ $subtotal }};
        var select = document.getElementById('ekspedisiSelect');
        var jarak = parseFloat(document.getElementById('jarakKm').value) || 0;
        var ongkir = 0;
        if (select.value) {
            var ongkirPerKm = parseFloat(select.options[select.selectedIndex].getAttribute('data-ongkir')) || 0;
            ongkir = ongkirPerKm * jarak;
        }
        document.getElementById('ongkirText').innerText = 'Rp ' + Math.round(ongkir).toLocaleString('id-ID');
        document.getElementById('totalBayarText').innerText = 'Rp ' + Math.round(subtotal + ongkir).toLocaleString('id-ID');
    }

    function hitungTotal() {
        var subtotal = {{ $subtotal }};
        document.getElementById('totalBayarText').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    }
    </script>
</x-app-layout>
<div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="height: 80vh; display: flex; flex-direction: column;">
    
    <div class="card-header bg-primary text-white d-flex align-items-center p-3 border-0">
        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
            <i class="bi bi-robot text-primary fs-4"></i>
        </div>
        <div>
            <h5 class="mb-0 fw-bold">NJK Assistant</h5>
            <small class="text-white-50"><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> Online | Siap merekomendasikan produk</small>
        </div>
    </div>

    <div class="card-body bg-light p-4" id="chatArea" style="overflow-y: auto; flex-grow: 1;">
        
        <div class="d-flex mb-4">
            <div class="bg-white shadow-sm rounded-4 p-3" style="max-width: 80%; border-top-left-radius: 0 !important;">
                @auth
                    <p class="mb-1 text-dark fw-semibold">Halo, {{ Auth::user()->name }}! 👋</p>
                    <p class="mb-0 text-muted small">Selamat datang di Nusantara Jaya Computer. Ada yang bisa saya bantu? Ketik barang atau kendala yang sedang Anda cari (Contoh: "laptop gaming", "lcd", "servis").</p>
                @else
                    <p class="mb-1 text-dark fw-semibold">Halo, Pengunjung! 👋</p>
                    <p class="mb-0 text-muted small">Selamat datang di Nusantara Jaya Computer. Silakan ketik barang atau kendala yang sedang Anda cari (Contoh: "laptop", "lcd", "servis").
                    <br><br>
                    <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill"></i> Catatan Penting:</span> Untuk melakukan transaksi pembelian barang secara aman, Anda wajib <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-underline">Login</a> atau <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-underline">Daftar Akun</a> terlebih dahulu.</p>
                @endauth
            </div>
        </div>

        <div id="chatMessages"></div>

        <div id="typingIndicator" class="d-none mb-4">
            <div class="bg-white shadow-sm rounded-4 p-3 d-inline-block" style="border-top-left-radius: 0 !important;">
                <small class="text-muted fst-italic">NJK Assistant sedang mengetik...</small>
            </div>
        </div>

    </div>

    <div class="card-footer bg-white border-top-0 p-3">
        <form id="chatForm" class="d-flex gap-2">
            <input type="text" id="userInput" class="form-control form-control-lg rounded-pill px-4 bg-light border-0" placeholder="Ketik pesan di sini..." autocomplete="off" required>
            <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>

</div>

<script>
    const isLoggedIn = @json(Auth::check());

    // Intercept clicks on recommended products for guest users
    function handleProductClick(event, namaProduk) {
        if (!isLoggedIn) {
            event.preventDefault();
            // Tampilkan pesan chat dari user yang mensimulasikan klik
            appendUserMessage(`Saya ingin membeli ${namaProduk}`);
            
            // Simulasikan bot sedang mengetik
            document.getElementById('typingIndicator').classList.remove('d-none');
            scrollToBottom();
            
            setTimeout(() => {
                document.getElementById('typingIndicator').classList.add('d-none');
                appendBotMessage(
                    `Mohon maaf, Anda harus masuk atau membuat akun terlebih dahulu untuk membeli **${namaProduk}** agar transaksi tetap aman.\n\nSilakan klik link berikut untuk **[Login]({{ route('login') }})** atau **[Daftar Akun Baru]({{ route('register') }})**.`
                );
            }, 600);
        }
    }

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let inputField = document.getElementById('userInput');
        let message = inputField.value.trim();
        if(message === '') return;

        // 1. Tampilkan pesan dari User ke layar
        appendUserMessage(message);
        inputField.value = ''; // Kosongkan input
        
        // Tampilkan tulisan "sedang mengetik..."
        document.getElementById('typingIndicator').classList.remove('d-none');
        scrollToBottom();

        // 2. Kirim pesan ke Controller
        fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pesan: message })
        })
        .then(response => response.json())
        .then(data => {
            // Sembunyikan tulisan "sedang mengetik..."
            document.getElementById('typingIndicator').classList.add('d-none');
            
            // 3. Tampilkan balasan dari Bot
            appendBotMessage(data.jawaban, data.rekomendasi_produk, data.rekomendasi_jasa);
        })
        .catch(error => {
            document.getElementById('typingIndicator').classList.add('d-none');
            appendBotMessage("Maaf, sistem sedang sibuk. Coba lagi nanti.");
        });
    });

    // Fungsi untuk menggambar chat bubble milik User
    function appendUserMessage(text) {
        let html = `
            <div class="d-flex justify-content-end mb-4">
                <div class="bg-primary text-white shadow-sm rounded-4 p-3" style="max-width: 80%; border-top-right-radius: 0 !important;">
                    <p class="mb-0 small">${text}</p>
                </div>
            </div>
        `;
        document.getElementById('chatMessages').insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Fungsi untuk menggambar chat bubble milik Bot beserta Rekomendasi
    function appendBotMessage(text, produk = [], jasa = []) {
        let rekomendasiHtml = '';

        // Jika ada rekomendasi Produk
        if(produk.length > 0) {
            rekomendasiHtml += `<hr class="my-2 opacity-25"><p class="mb-2 fw-bold" style="font-size: 0.8rem;">Rekomendasi Produk (Klik untuk membeli):</p>`;
            produk.forEach(p => {
                rekomendasiHtml += `<a href="/katalog?search=${encodeURIComponent(p.nama_produk)}" onclick="handleProductClick(event, '${p.nama_produk.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-primary d-block text-start mb-2 rounded-3 text-truncate"><i class="bi bi-box me-1"></i> ${p.nama_produk} - Rp ${p.harga_jual.toLocaleString('id-ID')}</a>`;
            });
        }

        // Jika ada rekomendasi Jasa
        if(jasa.length > 0) {
            rekomendasiHtml += `<p class="mb-2 mt-3 fw-bold" style="font-size: 0.8rem;">Layanan Servis Terkait:</p>`;
            jasa.forEach(j => {
                rekomendasiHtml += `<div class="p-2 border border-warning rounded-3 mb-2 bg-warning bg-opacity-10 text-dark small"><i class="bi bi-tools text-warning me-1"></i> ${j.nama_jasa} (Biaya: Rp ${j.biaya_jasa.toLocaleString('id-ID')})</div>`;
            });
        }

        // Simple Markdown parser to bold/italicize, parse links, and keep formatting
        let formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="fw-bold text-decoration-underline">$1</a>');

        let html = `
            <div class="d-flex mb-4">
                <div class="bg-white shadow-sm rounded-4 p-3" style="max-width: 85%; border-top-left-radius: 0 !important;">
                    <p class="mb-1 text-dark small" style="white-space: pre-line;">${formattedText}</p>
                    ${rekomendasiHtml}
                </div>
            </div>
        `;
        document.getElementById('chatMessages').insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Fungsi untuk scroll otomatis ke bawah saat ada chat baru
    function scrollToBottom() {
        let chatArea = document.getElementById('chatArea');
        chatArea.scrollTop = chatArea.scrollHeight;
    }
</script>

<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <x-auth-session-status class="mb-4 alert alert-info" :status="session('status')" />

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo NJK" class="img-fluid rounded-circle shadow-sm mb-2" style="width: 70px; height: 70px; object-fit: cover;">
                        <h4 class="fw-bold text-primary mb-0" style="font-size: 1.25rem;">Login Sistem</h4>
                        <p class="text-muted small mb-0">Nusantara Jaya Computer</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@gmail.com" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                <label class="form-check-label text-muted small" for="remember_me">
                                    Ingat Saya
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none small">Lupa Password?</a>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Masuk</button>
                        </div>
                        
                        <div class="text-center mt-4">
                            <span class="text-muted small">Belum punya akun?</span>
                            <a href="{{ route('register') }}" class="text-decoration-none small fw-semibold">Daftar disini</a>
                        </div>

                        <div class="row g-2 mt-2 pt-2 border-top">
                            <div class="col-6">
                                <a href="{{ route('konsultasi') }}" class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold py-2" style="font-size: 0.8rem;">
                                    <i class="bi bi-robot"></i> Chatbot AI
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('cek-servis.public') }}" class="btn btn-sm btn-outline-success w-100 rounded-3 fw-semibold py-2" style="font-size: 0.8rem;">
                                    <i class="bi bi-search"></i> Lacak Servis
                                </a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} Aplikasi Penjualan NJK.
            </div>

        </div>
    </div>
</x-guest-layout>
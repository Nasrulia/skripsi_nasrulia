@if(Auth::check())
    <x-app-layout>
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    @include('chatbot.chat-box')
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    <x-guest-layout>
        <div class="row justify-content-center align-items-center" style="min-height: 100vh; width: 100%; margin: 0 auto;">
            <div class="col-md-8 col-lg-6 my-4">
                <div class="text-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login
                    </a>
                </div>
                @include('chatbot.chat-box')
            </div>
        </div>
    </x-guest-layout>
@endif
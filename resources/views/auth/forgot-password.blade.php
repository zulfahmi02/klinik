@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-key"></i>
        </div>
        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-subtitle">Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 py-2 px-3 small rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2 text-success"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 py-2 px-3 small rounded-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="fa-solid fa-envelope me-1 text-primary"></i> Alamat Email
            </label>
            <div class="input-group-modern">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="nama@klinik.com" 
                    required 
                    autofocus
                >
            </div>
        </div>

        <button type="submit" class="btn-auth-primary mb-3">
            <span>Kirim Tautan Reset</span>
            <i class="fa-solid fa-paper-plane"></i>
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-decoration-none text-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Login
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1 class="auth-title">Daftar Akun</h1>
        <p class="auth-subtitle">Registrasi pengguna baru Sistem Informasi Klinik</p>
    </div>

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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fa-solid fa-user me-1 text-primary"></i> Nama Lengkap
            </label>
            <div class="input-group-modern">
                <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nama pengguna" required autofocus autocomplete="name">
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fa-solid fa-envelope me-1 text-primary"></i> Alamat Email
            </label>
            <div class="input-group-modern">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email@klinik.com" required autocomplete="username">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fa-solid fa-lock me-1 text-primary"></i> Password
            </label>
            <div class="input-group-modern">
                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="fa-solid fa-shield-check me-1 text-primary"></i> Konfirmasi Password
            </label>
            <div class="input-group-modern">
                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn-auth-primary mb-3">
            <span>Daftar Sekarang</span>
            <i class="fa-solid fa-user-check"></i>
        </button>

        <div class="text-center">
            <span class="small text-secondary">Sudah punya akun? </span>
            <a href="{{ route('login') }}" class="small text-decoration-none text-primary fw-bold">
                Masuk di sini
            </a>
        </div>
    </form>
</div>
@endsection

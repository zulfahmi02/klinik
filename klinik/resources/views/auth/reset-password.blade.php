@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1 class="auth-title">Password Baru</h1>
        <p class="auth-subtitle">Silakan atur kata sandi baru untuk akun Anda.</p>
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
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
                    value="{{ old('email', $request->email) }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                >
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fa-solid fa-lock me-1 text-primary"></i> Password Baru
            </label>
            <div class="input-group-modern">
                <span class="input-group-text">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                >
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="fa-solid fa-shield-check me-1 text-primary"></i> Konfirmasi Password Baru
            </label>
            <div class="input-group-modern">
                <span class="input-group-text">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                >
            </div>
        </div>

        <button type="submit" class="btn-auth-primary mb-3">
            <span>Simpan Password Baru</span>
            <i class="fa-solid fa-check"></i>
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-decoration-none text-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Login
            </a>
        </div>
    </form>
</div>
@endsection

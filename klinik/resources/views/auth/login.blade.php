@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <!-- Header & Brand Logo -->
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-notes-medical"></i>
        </div>
        <h1 class="auth-title">Klinik App</h1>
        <p class="auth-subtitle">Sistem Informasi & Manajemen Pelayanan Pasien</p>
    </div>

    <!-- Session Status Notification -->
    @if (session('status'))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4 py-2 px-3 small rounded-3" role="alert">
            <i class="fa-solid fa-circle-info me-2 text-primary"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start mb-4 py-2 px-3 small rounded-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2 mt-1 text-danger"></i>
            <div>
                <strong>Gagal Masuk:</strong>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Username / Email Field -->
        <div class="mb-3">
            <label for="loginInput" class="form-label">
                <i class="fa-solid fa-user-doctor me-1 text-primary"></i> Email atau Username
            </label>
            <div class="input-group-modern {{ $errors->has('login') || $errors->has('email') ? 'border-danger' : '' }}">
                <span class="input-group-text">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input 
                    type="text" 
                    class="form-control" 
                    id="loginInput" 
                    name="login" 
                    value="{{ old('login') ?: old('email') }}" 
                    placeholder="admin@klinik.com atau admin" 
                    required 
                    autofocus 
                    autocomplete="username"
                >
            </div>
        </div>

        <!-- Password Field -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="passwordInput" class="form-label">
                    <i class="fa-solid fa-lock me-1 text-primary"></i> Password
                </label>
            </div>
            <div class="input-group-modern has-toggle {{ $errors->has('password') ? 'border-danger' : '' }}">
                <span class="input-group-text">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control" 
                    id="passwordInput" 
                    name="password" 
                    placeholder="Masukkan password Anda" 
                    required 
                    autocomplete="current-password"
                >
                <button type="button" class="btn-toggle-password" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan password">
                    <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small text-secondary" for="rememberMe">
                    Ingat saya
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary fw-medium">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-auth-primary" id="btnSubmit">
            <span>Masuk ke Sistem</span>
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
    </form>

    <!-- Demo Accounts Helper Card -->
    <div class="demo-credential-box">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-bold text-dark">
                <i class="fa-solid fa-shield-halved text-primary me-1"></i> Akun Demo Cepat:
            </span>
            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 10px;">Siap Pakai</span>
        </div>
        <div class="d-flex gap-2 flex-wrap mb-2">
            <button type="button" class="demo-badge-btn" onclick="fillDemo('admin', 'admin123')">
                <i class="fa-solid fa-user-shield me-1 text-primary"></i> Admin: <strong>admin123</strong>
            </button>
            <button type="button" class="demo-badge-btn" onclick="fillDemo('dokter', 'dokter123')">
                <i class="fa-solid fa-user-md me-1 text-success"></i> Dokter: <strong>dokter123</strong>
            </button>
        </div>
        <div class="text-muted" style="font-size: 11px;">
            <i class="fa-solid fa-circle-info me-1"></i> Klik akun di atas untuk mengisi otomatis kolom login & password.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('passwordInput');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                    toggleBtn.setAttribute('title', 'Sembunyikan password');
                } else {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                    toggleBtn.setAttribute('title', 'Tampilkan password');
                }
            });
        }
    });

    function fillDemo(username, password) {
        const loginInput = document.getElementById('loginInput');
        const passwordInput = document.getElementById('passwordInput');

        if (loginInput && passwordInput) {
            loginInput.value = username;
            passwordInput.value = password;

            // Highlight effect
            loginInput.closest('.input-group-modern').style.transition = 'all 0.3s ease';
            passwordInput.closest('.input-group-modern').style.transition = 'all 0.3s ease';
            
            loginInput.closest('.input-group-modern').style.borderColor = '#10b981';
            passwordInput.closest('.input-group-modern').style.borderColor = '#10b981';

            setTimeout(() => {
                loginInput.closest('.input-group-modern').style.borderColor = '';
                passwordInput.closest('.input-group-modern').style.borderColor = '';
            }, 800);
        }
    }
</script>
@endpush

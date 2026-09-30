@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-envelope-circle-check"></i>
        </div>
        <h1 class="auth-title">Verifikasi Email</h1>
        <p class="auth-subtitle">Terima kasih telah mendaftar! Silakan klik tautan verifikasi yang kami kirimkan ke email Anda.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show small rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i> Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
            <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-auth-primary">
                <span>Kirim Ulang Email Verifikasi</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center mt-2">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none text-danger small">
                <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
            </button>
        </form>
    </div>
</div>
@endsection

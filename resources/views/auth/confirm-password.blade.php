@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="text-center">
        <div class="auth-logo-badge">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1 class="auth-title">Konfirmasi Password</h1>
        <p class="auth-subtitle">Area ini memerlukan konfirmasi password Anda sebelum melanjutkan.</p>
    </div>

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

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="fa-solid fa-lock me-1 text-primary"></i> Password
            </label>
            <div class="input-group-modern">
                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password Anda" 
                    required 
                    autocomplete="current-password"
                >
            </div>
        </div>

        <button type="submit" class="btn-auth-primary">
            <span>Konfirmasi</span>
            <i class="fa-solid fa-check"></i>
        </button>
    </form>
</div>
@endsection

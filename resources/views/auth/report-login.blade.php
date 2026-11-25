@extends('layouts.guest')
@section('content')
<div class="auth-card">
    <div class="auth-logo-badge"><i class="fa-solid fa-chart-column" aria-hidden="true"></i></div>
    <h1 class="auth-title">Login Ulang Laporan</h1>
    <p>Laporan Klinik · Area baca saja. Konfirmasi berlaku 30 menit.</p>
    @auth
        <p>Gunakan akun pemeriksaan yang sama: <strong>{{ auth()->user()->name }}</strong>. Akun tidak akan diganti.</p>
    @else
        <p>Masuk dengan akun petugas untuk mengakses laporan.</p>
    @endauth
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('reports.login.store') }}">
        @csrf
        <label class="form-label" for="login">Email atau Username</label>
        <input class="form-control mb-3" id="login" name="login" value="{{ old('login') }}" required autocomplete="username" autofocus>
        <label class="form-label" for="password">Password</label>
        <input class="form-control mb-3" id="password" name="password" type="password" required autocomplete="current-password">
        <button class="btn-auth-primary" type="submit">Masuk ke Laporan</button>
    </form>
    <p class="mt-3"><a href="{{ route('dashboard') }}">Kembali ke Pemeriksaan</a></p>
</div>
@endsection

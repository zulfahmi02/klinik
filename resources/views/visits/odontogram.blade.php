@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3">Odontogram — {{ $visit->patient->nama }} ({{ $visit->patient->no_rm }})</h1>
    <p>Kunjungan {{ $visit->tanggal_kunjungan->format('d/m/Y') }}</p>
    <a href="{{ route(request()->routeIs('reports.*') ? 'reports.examinations.show' : 'visits.show', $visit) }}">Detail kunjungan</a> @unless(request()->routeIs('reports.*')) · <a href="{{ route('visits.edit', $visit) }}">Pemeriksaan</a> @endunless
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @include('odontogram.partials.visit')
    @if (!$snapshotAvailable)
        <p class="alert alert-warning">Penyimpanan odontogram kunjungan belum tersedia. Hubungi pengelola aplikasi.</p>
    @elseif ($visit->status !== 'Batal' && !request()->routeIs('reports.*'))
        @include('odontogram.partials.editor')
    @endif
</div>
@endsection

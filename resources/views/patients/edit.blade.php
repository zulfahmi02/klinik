@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3">Edit Data Pasien</h1><p class="text-muted mb-0">{{ $patient->no_rm }} · Perbarui identitas dan informasi pasien.</p></div><a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Kembali</a></div>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form class="card p-4" method="POST" action="{{ route('patients.update', $patient) }}">
        @csrf @method('PUT')
        <div class="row g-4">
            @foreach(['nama'=>'Nama lengkap','nik'=>'NIK','tanggal_lahir'=>'Tanggal lahir','tempat_lahir'=>'Tempat lahir','no_hp'=>'Nomor HP','pekerjaan'=>'Pekerjaan','status_pernikahan'=>'Status pernikahan','golongan_darah'=>'Golongan darah','agama'=>'Agama','penjamin'=>'Jenis penjamin','no_penjamin'=>'Nomor penjamin'] as $field=>$label)
                <div class="col-md-6"><label class="form-label" for="{{ $field }}">{{ $label }}{{ $field === 'nama' ? ' *' : '' }}</label><input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'tanggal_lahir' ? 'date' : 'text' }}" class="form-control" value="{{ old($field, $field === 'tanggal_lahir' ? $patient->tanggal_lahir?->format('Y-m-d') : $patient->$field) }}" @required($field === 'nama')></div>
            @endforeach
            <div class="col-md-6"><label for="jenis_kelamin" class="form-label">Jenis kelamin *</label><select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required><option value="L" @selected(old('jenis_kelamin', $patient->jenis_kelamin)==='L')>Laki-laki</option><option value="P" @selected(old('jenis_kelamin', $patient->jenis_kelamin)==='P')>Perempuan</option></select></div>
            @foreach(['alamat'=>'Alamat','alergi'=>'Alergi'] as $field=>$label)<div class="col-12"><label class="form-label" for="{{ $field }}">{{ $label }}</label><textarea id="{{ $field }}" name="{{ $field }}" class="form-control" rows="3">{{ old($field, $patient->$field) }}</textarea></div>@endforeach
        </div>
        <div class="mt-4"><button class="btn btn-primary">Simpan perubahan</button></div>
    </form>
</div>
@endsection

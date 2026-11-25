@extends('layouts.report-pdf')
@section('title', 'Laporan Tindakan Gigi')
@section('content')
<p>Kategori: {{ $kategori ?: 'Semua' }}</p>
<table><thead><tr><th>Kode</th><th>Tindakan</th><th>Kategori</th><th>Tarif saat ini (Rp)</th><th>Jumlah</th><th>Biaya tercatat (Rp)</th></tr></thead><tbody>
@forelse($treatmentSummary as $item)
<tr><td>{{ $item['kode'] }}</td><td>{{ $item['nama'] }}</td><td>{{ $item['kategori'] }}</td><td>{{ number_format($item['tarif'],0,',','.') }}</td><td>{{ $item['jumlah'] }}</td><td>{{ number_format($item['total_biaya'],0,',','.') }}</td></tr>
@empty<tr><td colspan="6">Tidak ada tindakan pada filter ini.</td></tr>@endforelse
<tr><th colspan="4">Total</th><td>{{ $treatmentSummary->sum('jumlah') }}</td><td>{{ number_format($treatmentSummary->sum('total_biaya'),0,',','.') }}</td></tr>
</tbody></table>
<h2>Rincian tindakan per kunjungan</h2>
<table><thead><tr><th>Tanggal</th><th>Pasien / RM</th><th>Dokter</th><th>Tindakan</th><th>Biaya (Rp)</th></tr></thead><tbody>
@forelse($records as $record)<tr><td>{{ $record->visit?->tanggal_kunjungan?->format('d/m/Y') }}</td><td>{{ $record->visit?->patient?->nama }}<br>{{ $record->visit?->patient?->no_rm }}</td><td>{{ $record->visit?->doctor?->nama ?? '-' }}</td><td>{{ $record->treatment?->nama ?? '-' }}</td><td>{{ number_format($record->biaya,0,',','.') }}</td></tr>
@empty<tr><td colspan="5">Tidak ada rincian.</td></tr>@endforelse
</tbody></table>
@endsection

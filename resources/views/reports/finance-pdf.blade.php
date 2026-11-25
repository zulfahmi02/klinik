@extends('layouts.report-pdf')
@section('title', 'Laporan Keuangan')
@section('content')
<p>Status: {{ $statusPembayaran ?: 'Semua' }} · Metode: {{ $metodePembayaran ?: 'Semua' }}</p>
<p>Pendapatan lunas: Rp {{ number_format($totalPendapatan,0,',','.') }} · Tindakan lunas: Rp {{ number_format($totalTindakan,0,',','.') }} · Obat lunas: Rp {{ number_format($totalObat,0,',','.') }} · Belum lunas: Rp {{ number_format($totalPiutang,0,',','.') }}</p>
<table><thead><tr><th>Tanggal</th><th>Pasien / RM</th><th>Dokter</th><th>Tindakan (Rp)</th><th>Obat (Rp)</th><th>Total (Rp)</th><th>Status / Metode</th></tr></thead><tbody>
@forelse($visits as $visit)
<tr><td>{{ $visit->tanggal_kunjungan->format('d/m/Y') }}</td><td>{{ $visit->patient?->nama }}<br>{{ $visit->patient?->no_rm }}</td><td>{{ $visit->doctor?->nama ?? '-' }}</td><td class="number">{{ number_format($visit->biaya_tindakan,0,',','.') }}</td><td class="number">{{ number_format($visit->biaya_obat,0,',','.') }}</td><td class="number">{{ number_format($visit->total_biaya,0,',','.') }}</td><td>{{ $visit->status_pembayaran }}<br>{{ $visit->metode_pembayaran ?? '-' }}</td></tr>
@empty<tr><td colspan="7">Tidak ada data pada filter ini.</td></tr>@endforelse
</tbody></table>
@endsection

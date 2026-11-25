@extends('layouts.report-pdf')
@section('title', 'Laporan Dokter')
@section('content')
<p>Jumlah kunjungan dihitung per kunjungan, termasuk pasien yang kembali berobat.</p>
<table><thead><tr><th>Dokter / SIP</th><th>Kunjungan</th><th>Selesai</th><th>Jumlah tindakan</th><th>Biaya tindakan (Rp)</th><th>Tindakan + obat (Rp)</th></tr></thead><tbody>
@forelse($doctorStats as $doctor)
<tr><td>{{ $doctor['nama'] }}<br>{{ $doctor['no_sip'] ?: '-' }}</td><td>{{ $doctor['total_pasien'] }}</td><td>{{ $doctor['pasien_selesai'] }}</td><td>{{ $doctor['total_tindakan'] }}</td><td>{{ number_format($doctor['total_tindakan_rp'],0,',','.') }}</td><td>{{ number_format($doctor['total_pendapatan'],0,',','.') }}</td></tr>
@empty<tr><td colspan="6">Tidak ada data dokter.</td></tr>@endforelse
<tr><th>Total</th><td>{{ $doctorStats->sum('total_pasien') }}</td><td>{{ $doctorStats->sum('pasien_selesai') }}</td><td>{{ $doctorStats->sum('total_tindakan') }}</td><td>{{ number_format($doctorStats->sum('total_tindakan_rp'),0,',','.') }}</td><td>{{ number_format($doctorStats->sum('total_pendapatan'),0,',','.') }}</td></tr>
</tbody></table>
@endsection

@extends('layouts.report-pdf')
@section('title', 'Rekap Tren Kunjungan')
@section('content')
<p>Tahun {{ $year }}. Jumlah kunjungan termasuk kunjungan batal. Ekspor menampilkan angka sumber grafik.</p>
<table><thead><tr><th>Bulan</th><th>Jumlah kunjungan</th></tr></thead><tbody>
@foreach($monthsLabels as $index => $month)<tr><td>{{ $month }}</td><td>{{ $monthlyData[$index] }}</td></tr>@endforeach
<tr><th>Total</th><td>{{ array_sum($monthlyData) }}</td></tr>
</tbody></table>
<h2>Tren tujuh hari terakhir (terlepas dari filter tahun)</h2>
<table><thead><tr><th>Tanggal</th><th>Jumlah kunjungan</th></tr></thead><tbody>
@foreach($dailyLabels as $index => $day)<tr><td>{{ $day }}</td><td>{{ $dailyTrend[$index] }}</td></tr>@endforeach
</tbody></table>
@foreach(['Penjamin' => $penjaminVisits, 'Dokter' => $doctorVisits] as $label => $counts)
<h2>Rekap {{ $label }} — {{ $year }}</h2>
<table><thead><tr><th>{{ $label }}</th><th>Jumlah kunjungan</th></tr></thead><tbody>
@forelse($counts as $name => $count)<tr><td>{{ $name ?: 'Belum ditentukan' }}</td><td>{{ $count }}</td></tr>@empty<tr><td colspan="2">Belum ada data.</td></tr>@endforelse
</tbody></table>
@endforeach
@endsection

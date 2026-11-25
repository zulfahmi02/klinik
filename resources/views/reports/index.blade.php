@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="dashboard-welcome"><div><span class="dashboard-eyebrow">Pelaporan · Akses baca saja</span><h1>Ringkasan Laporan Klinik Gigi</h1><p>Telusuri pelayanan, riwayat pemeriksaan, dan pembiayaan. Pilih laporan untuk mengatur periode dan mencetak hasilnya.</p></div><i class="fa-solid fa-chart-column text-primary fs-1" aria-hidden="true"></i></div>
    <div class="row g-4">
        @foreach ([['reports.visits', 'file-medical', 'Laporan Kunjungan', 'Cari kunjungan, buka riwayat pemeriksaan dan odontogram, lalu unduh PDF.'], ['reports.treatments', 'tooth', 'Laporan Tindakan', 'Rekap tindakan perawatan gigi berdasarkan periode dan kategori.'], ['reports.finance', 'wallet', 'Laporan Keuangan', 'Rincian biaya tindakan, obat, tagihan lunas, dan piutang.'], ['reports.doctors', 'user-doctor', 'Laporan Dokter', 'Jumlah kunjungan, pelayanan selesai, dan nilai tindakan dokter.'], ['reports.charts', 'chart-line', 'Tren Kunjungan', 'Lihat perkembangan jumlah kunjungan harian dan bulanan.']] as [$route, $icon, $title, $description])
        <div class="col-md-6 col-xl-4"><a class="report-tile" href="{{ route($route) }}"><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i><h2>{{ $title }}</h2><p>{{ $description }}</p><strong class="text-primary small">Buka laporan →</strong></a></div>
        @endforeach
    </div>
</div>
@endsection

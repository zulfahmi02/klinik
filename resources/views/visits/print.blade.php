<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Resume kunjungan {{ $visit->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 20px; } h2 { font-size: 16px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #bbb; padding: 5px; text-align: left; vertical-align: top; }
        tr { page-break-inside: avoid; } thead { display: table-header-group; }
        .text { white-space: pre-wrap; overflow-wrap: anywhere; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    @unless ($pdf ?? false)
    <div class="no-print"><button onclick="window.print()">Cetak resume</button> <a href="{{ route(request()->routeIs('reports.*') ? 'reports.examinations.show' : 'visits.show', $visit) }}">Kembali ke kunjungan</a></div>
    @endunless
    <p style="color:#00685f;font-weight:bold">{{ config('app.name') }} · Pelayanan Gigi</p><h1>Resume pemeriksaan kunjungan</h1>
    <p>Tanggal: {{ $visit->tanggal_kunjungan->format('d/m/Y') }} {{ $visit->jam_kunjungan }} · {{ $visit->poli }}</p>
    <h2>Identitas pasien</h2>
    <table>
        <tr><th>Nama / No. RM</th><td>{{ $visit->patient->nama }} / {{ $visit->patient->no_rm }}</td></tr>
        <tr><th>Tanggal lahir / Jenis kelamin</th><td>{{ $visit->patient->tanggal_lahir?->format('d/m/Y') ?? '-' }} / {{ $visit->patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $visit->patient->alamat ?? '-' }}</td></tr>
        <tr><th>Penjamin / Nomor penjamin</th><td>{{ $visit->penjamin }} / {{ $visit->patient->no_penjamin ?? '-' }}</td></tr>
        <tr><th>Alergi</th><td>{{ $visit->patient->alergi ?? '-' }}</td></tr>
        <tr><th>Dokter</th><td>{{ $visit->doctor?->nama ?? '-' }}</td></tr>
    </table>
    <h2>Hasil pemeriksaan</h2>
    <table>
        <tr><th>Keluhan</th><td class="text">{{ $visit->keluhan ?? '-' }}</td></tr>
        <tr><th>Diagnosa</th><td class="text">{{ $visit->diagnosa ?? '-' }}</td></tr>
        <tr><th>Catatan dokter / Resep</th><td class="text">{{ $visit->catatan_dokter ?? '-' }}</td></tr>
    </table>
    <h2>Odontogram kunjungan</h2>
    @if ($snapshot)
        <p>Dicatat {{ $snapshot->created_at }}; diperbarui {{ $snapshot->updated_at }}. Waktu pencatatan dapat berbeda dari tanggal kunjungan.</p>
    @else
        <p>Belum ada snapshot odontogram kunjungan ini. Hanya data lama yang terhubung langsung ke kunjungan ditampilkan bila tersedia.</p>
    @endif
    @include('odontogram.partials.print-diagram', ['teeth' => $teeth])
    @include('odontogram.partials.table', ['teeth' => $teeth])
    <h2>Rincian tindakan dan obat</h2>
    <table>
        <thead><tr><th>Tindakan</th><th>Catatan</th><th>Biaya</th></tr></thead>
        <tbody>
        @forelse ($visit->visitTreatments as $item)
            <tr><td>{{ $item->treatment?->nama ?? $item->catatan ?? '-' }}</td><td>{{ $item->catatan ?? '-' }}</td><td>Rp {{ number_format($item->biaya, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="3">Belum ada rincian tindakan.</td></tr>
        @endforelse
        <tr><th colspan="2">Subtotal tindakan tercatat</th><td>Rp {{ number_format($visit->biaya_tindakan, 0, ',', '.') }}</td></tr>
        <tr><th colspan="2">Obat / Resep</th><td>Rp {{ number_format($visit->biaya_obat, 0, ',', '.') }}</td></tr>
        <tr><th colspan="2">Total tindakan + obat</th><td>Rp {{ number_format($visit->biaya_tindakan + $visit->biaya_obat, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>
    <p>Rincian ini hanya mencakup tindakan dan obat. {{ $visit->status_pembayaran }} · {{ $visit->metode_pembayaran ?? '-' }}</p>
</body>
</html>
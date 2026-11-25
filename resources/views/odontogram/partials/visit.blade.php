<section id="odontogram" class="card p-3 mb-4">
    <h2 class="h5">Odontogram kunjungan</h2>
    @if ($snapshot)
        <p class="text-muted">Dicatat: {{ $snapshot->created_at }}. Diperbarui: {{ $snapshot->updated_at }}. Waktu pencatatan dapat berbeda dari tanggal kunjungan.</p>
    @else
        <p>Belum ada hasil odontogram yang disimpan untuk kunjungan ini. Gigi tanpa catatan belum dinyatakan sehat.</p>
    @endif
    <h3 class="h6">Gigi permanen</h3>
    @include('odontogram.partials.diagram', ['teeth' => $teeth])
    <details class="mb-3" @if(array_intersect(\App\Services\VisitOdontogram::PRIMARY_TEETH, array_keys($teeth))) open @endif>
        <summary>Gigi susu / pemeriksaan anak</summary>
        <p class="small text-muted mt-2">Catat hanya gigi yang diperiksa. Gigi susu dan permanen dapat dicatat pada kunjungan yang sama.</p>
        @include('odontogram.partials.diagram', ['teeth' => $teeth, 'toothSet' => \App\Services\VisitOdontogram::PRIMARY_TEETH, 'columns' => 10])
    </details>
    <details><summary class="mb-3">Rincian seluruh gigi</summary>
    @include('odontogram.partials.table', ['teeth' => $teeth])
    </details>
    <a class="btn btn-outline-primary no-print" href="{{ route(request()->routeIs('reports.*') ? 'reports.odontogram.show' : 'visits.odontogram.show', $visit) }}">{{ request()->routeIs('reports.*') ? 'Lihat odontogram kunjungan' : 'Lihat / perbarui odontogram kunjungan' }}</a>
    <details class="mt-3 no-print">
        <summary>Kondisi terbaru pasien (referensi)</summary>
        <p>Data ini bukan rekaman kondisi pada tanggal kunjungan. Gigi yang belum dicatat tidak dianggap sehat.</p>
        @include('odontogram.partials.table', ['teeth' => $currentTeeth])
    </details>
</section>

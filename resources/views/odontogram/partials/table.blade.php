<table class="table table-bordered table-sm odontogram-table" style="width:100%; border-collapse:collapse; font-size:12px;">
    <caption>Odontogram FDI — gigi permanen{{ count(\App\Services\VisitOdontogram::displayTeeth($teeth)) > 32 ? ' dan gigi susu' : '' }}</caption>
    <thead><tr><th>Gigi</th><th>Kondisi</th><th>Tindakan</th><th>Catatan</th></tr></thead>
    <tbody>
    @foreach (\App\Services\VisitOdontogram::displayTeeth($teeth) as $number)
        <tr>
            <th scope="row">{{ $number }}</th>
            <td>{{ $teeth[$number]['kondisi'] ?? 'Belum dicatat' }}</td>
            <td>{{ $teeth[$number]['tindakan'] ?? '-' }}</td>
            <td>{{ $teeth[$number]['catatan'] ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

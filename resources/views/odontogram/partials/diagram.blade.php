<div class="dental-chart-scroll" role="region" aria-label="Diagram odontogram, geser untuk melihat seluruh gigi" tabindex="0">
    <div class="dental-chart">
        @foreach (array_chunk($toothSet ?? \App\Services\VisitOdontogram::TEETH, $columns ?? 16) as $row)
            <div class="dental-side-label"><span>Kanan pasien</span><strong>{{ $loop->first ? 'Rahang atas' : 'Rahang bawah' }}</strong><span>Kiri pasien</span></div>
            <div class="tooth-row" style="grid-template-columns:repeat({{ $columns ?? 16 }},minmax(0,1fr))">
                @foreach ($row as $number)
                    @php $record = $teeth[$number] ?? []; $condition = $record['kondisi'] ?? 'Belum dicatat'; @endphp
                    <button type="button" class="tooth" data-tooth="{{ $number }}" data-condition="{{ $condition }}" data-treatment="{{ $record['tindakan'] ?? '' }}" data-note="{{ $record['catatan'] ?? '' }}" aria-label="Gigi {{ $number }}: {{ $condition }}" aria-pressed="false" title="{{ $condition }}">
                        <strong>{{ $number }}</strong>
                        <svg viewBox="0 0 32 36" aria-hidden="true"><path d="M16 5C9 1 3 4 4 12c1 6 3 8 4 17 1 5 4 5 5 0l3-9 3 9c1 5 4 5 5 0 1-9 3-11 4-17 1-8-5-11-12-7Z"/></svg>
                        <small>{{ $condition === 'Belum dicatat' ? '—' : ($condition === 'Sehat' ? 'Sehat' : '●') }}</small>
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
<div class="dental-legend mb-3"><span>— Belum dicatat</span><span>Sehat</span><span>● Ada temuan, pilih gigi untuk rincian</span></div>
<p class="small text-muted" data-tooth-summary aria-live="polite">Pilih gigi untuk melihat kondisi yang tercatat pada kunjungan ini.</p>
@once
@push('scripts')
<script>
document.querySelectorAll('[data-tooth]').forEach(button => button.addEventListener('click', () => {
    const region = button.closest('section') || button.closest('.card') || document;
    region.querySelectorAll('[data-tooth]').forEach(tooth => tooth.setAttribute('aria-pressed', String(tooth === button)));
    const summary = region.querySelector('[data-tooth-summary]');
    if (summary) summary.textContent = `Gigi ${button.dataset.tooth}: ${button.dataset.condition}. ${button.dataset.treatment} ${button.dataset.note}`;
    const number = document.getElementById('nomor_gigi');
    if (number) {
        number.value = button.dataset.tooth;
        document.getElementById('kondisi').value = button.dataset.condition === 'Belum dicatat' ? '' : button.dataset.condition;
        document.getElementById('tindakan').value = button.dataset.treatment;
        document.getElementById('catatan').value = button.dataset.note;
        number.dispatchEvent(new Event('change', {bubbles: true}));
    }
}));
</script>
@endpush
@endonce

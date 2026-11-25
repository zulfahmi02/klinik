        <form method="POST" action="{{ route('visits.odontogram.update', $visit) }}" class="card p-4" id="tooth-editor">
            @csrf
            <input type="hidden" name="return_to_examination" value="{{ request()->routeIs('visits.edit') ? '1' : '0' }}">
            <h2 class="h5 mb-3">Catat kondisi gigi</h2><p class="text-muted">Pilih gigi pada diagram, lengkapi temuan, lalu simpan. Catatan tersimpan untuk kunjungan ini.</p>
            <label for="nomor_gigi">Nomor gigi</label>
            <select id="nomor_gigi" name="nomor_gigi" class="form-select" required>
                @foreach (\App\Services\VisitOdontogram::allTeeth() as $number)<option value="{{ $number }}" @selected(old('nomor_gigi') == $number)>{{ $number }} — {{ $number >= 51 ? 'Gigi susu' : 'Gigi permanen' }}</option>@endforeach
            </select>
            <label for="kondisi">Kondisi</label>
            <select id="kondisi" name="kondisi" class="form-select" required>
                <option value="">Pilih kondisi hasil pemeriksaan</option>
                @foreach (\App\Services\VisitOdontogram::CONDITIONS as $condition)<option @selected(old('kondisi') === $condition)>{{ $condition }}</option>@endforeach
            </select>
            <label for="tindakan">Tindakan</label><input id="tindakan" name="tindakan" class="form-control" maxlength="255" value="{{ old('tindakan') }}">
            <label for="catatan">Catatan</label><textarea id="catatan" name="catatan" class="form-control" maxlength="2000">{{ old('catatan') }}</textarea>
            <button class="btn btn-primary mt-3">Simpan kondisi gigi</button>
        </form>

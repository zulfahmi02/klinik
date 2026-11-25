<table style="width:100%;table-layout:fixed;border-collapse:collapse;margin:16px 0;font-size:9px;text-align:center">
@foreach(array_chunk(\App\Services\VisitOdontogram::TEETH, 16) as $row)
<tr><th colspan="16" style="text-align:center;background:#edf5f3;padding:7px">{{ $loop->first ? 'RAHANG ATAS — Kanan pasien ke kiri pasien' : 'RAHANG BAWAH — Kanan pasien ke kiri pasien' }}</th></tr>
<tr>@foreach($row as $number)<td style="text-align:center;padding:8px 1px;border:1px solid #c9d9d5"><strong>{{ $number }}</strong><br>{{ isset($teeth[$number]) ? ($teeth[$number]['kondisi'] === 'Sehat' ? 'S' : '*') : '-' }}</td>@endforeach</tr>
@endforeach
</table>
<p style="font-size:10px">S: Sehat · *: Ada temuan (lihat rincian) · -: Belum dicatat</p>

@if(array_intersect(\App\Services\VisitOdontogram::PRIMARY_TEETH, array_keys($teeth)))
<h3>Gigi susu</h3>
<table style="width:100%;table-layout:fixed;border-collapse:collapse;font-size:9px">
@foreach(array_chunk(\App\Services\VisitOdontogram::PRIMARY_TEETH, 10) as $row)
<tr><th colspan="10">{{ $loop->first ? 'Rahang atas' : 'Rahang bawah' }} — kanan pasien ke kiri pasien</th></tr>
<tr>@foreach($row as $number)<td style="text-align:center;border:1px solid #c9d9d5;padding:8px"><strong>{{ $number }}</strong><br>{{ isset($teeth[$number]) ? ($teeth[$number]['kondisi'] === 'Sehat' ? 'S' : '*') : '-' }}</td>@endforeach</tr>
@endforeach
</table>
@endif

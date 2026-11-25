<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>@yield('title')</title>
<style>
@page{margin:28px}body{font-family:DejaVu Sans,sans-serif;font-size:10px;color:#203c3a}h1{font-size:20px;margin-bottom:8px}h2{font-size:13px;margin-top:20px}table{width:100%;border-collapse:collapse;margin:14px 0;table-layout:fixed}th,td{border:1px solid #ccdcd8;padding:7px;overflow-wrap:break-word}th{background:#edf5f2;text-align:left}thead{display:table-header-group}tr{page-break-inside:avoid}.muted{color:#566d67}.number{text-align:right}
</style></head><body>
<strong>{{ config('app.name') }} · Pelayanan Gigi</strong>
<h1>@yield('title')</h1>
@if(isset($startDate))<p>Periode: {{ $startDate }} sampai {{ $endDate }}</p>@endif
<p class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }} {{ config('app.timezone') }}</p>
@yield('content')
</body></html>

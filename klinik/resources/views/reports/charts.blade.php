@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-chart-line text-primary me-2"></i>Grafik & Analitik Kunjungan Pasien</h3>
            <p class="text-muted mb-0">Visualisasi data statistik kunjungan poliklinik, tren pasien, dan sebaran layanan</p>
        </div>
        <form method="GET" action="{{ route('reports.charts') }}" class="d-flex align-items-center gap-2">
            <label class="small text-muted fw-semibold mb-0">Tahun:</label>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @for ($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Top Trend Charts Row -->
    <div class="row g-4 mb-4">
        <!-- 7-Days Daily Trend -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-chart-area text-primary me-2"></i>Tren Kunjungan 7 Hari Terakhir
                    </h6>
                    <span class="badge bg-primary-subtle text-primary">Harian</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 260px;">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-chart-column text-success me-2"></i>Tren Kunjungan Bulanan Tahun {{ $year }}
                    </h6>
                    <span class="badge bg-success-subtle text-success">Bulanan</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 260px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="row g-4">
        <!-- Polyclinic Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-chart-pie text-info me-2"></i>Sebaran Kunjungan per Poliklinik
                    </h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-height: 240px;">
                        <canvas id="poliChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penjamin (BPJS vs Umum) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-shield-halved text-warning me-2"></i>Proporsi Penjamin (BPJS vs Mandiri)
                    </h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-height: 240px;">
                        <canvas id="penjaminChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Visits Workload -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-user-doctor text-primary me-2"></i>Volume Pelayanan Dokter
                    </h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-height: 240px;">
                        <canvas id="doctorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart Defaults
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Daily Trend
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 260);
        dailyGradient.addColorStop(0, 'rgba(37, 99, 235, 0.3)');
        dailyGradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Jumlah Pasien',
                    data: {!! json_encode($dailyTrend) !!},
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    backgroundColor: dailyGradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 2. Monthly Trend
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthsLabels) !!},
                datasets: [{
                    label: 'Kunjungan Pasien',
                    data: {!! json_encode($monthlyData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 3. Polyclinic Doughnut Chart
        const poliData = {!! json_encode($poliVisits) !!};
        new Chart(document.getElementById('poliChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(poliData),
                datasets: [{
                    data: Object.values(poliData),
                    backgroundColor: ['#2563eb', '#0d9488', '#f59e0b', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 4. Penjamin Pie Chart
        const penjaminData = {!! json_encode($penjaminVisits) !!};
        new Chart(document.getElementById('penjaminChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(penjaminData),
                datasets: [{
                    data: Object.values(penjaminData),
                    backgroundColor: ['#10b981', '#6366f1', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 5. Doctor Workload Horizontal Bar Chart
        const doctorData = {!! json_encode($doctorVisits) !!};
        new Chart(document.getElementById('doctorChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(doctorData),
                datasets: [{
                    label: 'Pasien Ditangani',
                    data: Object.values(doctorData),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endsection

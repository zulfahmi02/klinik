<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Pasien · {{ config('app.name', 'Klinik') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ink: #153d3c; --muted: #627776; --teal: #087f75; --line: #dce8e4; }
        body { background: #f5f8f5; color: var(--ink); font-family: 'Segoe UI', sans-serif; }
        .registration-shell { max-width: 1180px; }
        .public-header { background: #ffffffeb; border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 12px; color: var(--ink); text-decoration: none; font-size: 19px; font-weight: 750; }
        .brand-mark { width: 40px; height: 40px; border-radius: 13px; background: var(--teal); display: grid; place-items: center; color: white; font-size: 30px; font-weight: 500; line-height: 1; }
        .header-note { font-size: 12px; color: var(--muted); }
        .registration-hero { margin-bottom: 32px; max-width: 760px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 9px; font-size: 11px; font-weight: 750; letter-spacing: 1.7px; text-transform: uppercase; color: var(--teal); margin-bottom: 16px; }
        .eyebrow::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #0e9c88; }
        .registration-hero h1 { font-size: clamp(32px, 4.5vw, 49px); line-height: 1.12; font-weight: 750; letter-spacing: -1.8px; margin-bottom: 16px; }
        .registration-hero h1 span { color: var(--teal); }
        .registration-hero p { color: var(--muted); line-height: 1.75; font-size: 15px; max-width: 610px; }
        .registration-grid { display: grid; grid-template-columns: 310px minmax(0, 1fr); gap: 28px; align-items: start; }
        .registration-guide { position: sticky; top: 24px; }
        .guide-panel { background: #173f3d; color: #fff; border-radius: 24px; padding: 28px; overflow: hidden; position: relative; }
        .guide-panel::after { content: '+'; position: absolute; right: -6px; top: -60px; font-size: 210px; line-height: 1; color: #ffffff06; pointer-events: none; }
        .guide-caption { color: #a9d3c9; text-transform: uppercase; letter-spacing: 1.8px; font-size: 10px; font-weight: 700; }
        .guide-panel h2 { font-size: 25px; font-weight: 650; line-height: 1.35; margin: 12px 0 28px; }
        .guide-steps { list-style: none; padding: 0; margin: 0; }
        .guide-steps li { display: flex; gap: 13px; padding-bottom: 25px; }
        .guide-steps li:last-child { padding-bottom: 0; }
        .step-number { flex: 0 0 30px; height: 30px; border: 1px solid #ffffff35; border-radius: 50%; display: grid; place-items: center; color: #bde5d9; font-size: 12px; }
        .guide-steps strong { display: block; font-size: 14px; font-weight: 600; margin: 3px 0 5px; }
        .guide-steps p { font-size: 12px; line-height: 1.65; color: #b9cfca; margin: 0; }
        .guide-tip { border-top: 1px solid #ffffff20; margin-top: 26px; padding-top: 20px; font-size: 12px; line-height: 1.7; color: #b9cfca; }
        .returning-patient { margin-top: 18px; padding: 21px; border: 1px solid #dce5dc; border-radius: 18px; background: #edf2e9; }
        .returning-patient h3 { font-size: 14px; font-weight: 700; }
        .returning-patient p { font-size: 12px; color: var(--muted); line-height: 1.75; margin: 0; }
        .registration-card { border: 1px solid var(--line); border-radius: 24px; box-shadow: 0 10px 40px #163f3c05; overflow: hidden; background: white; }
        .form-intro { padding: 25px 32px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 15px; background: #fcfdfb; }
        .form-intro strong { font-size: 16px; }
        .form-intro p { margin: 5px 0 0; font-size: 12px; color: var(--muted); }
        .today-pill { white-space: nowrap; color: #276c61; background: #eaf4ee; border-radius: 8px; padding: 8px 11px; font-size: 11px; font-weight: 650; }
        .form-body { padding: 30px 32px; }
        .section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .section-icon { width: 36px; height: 36px; flex-shrink: 0; display: grid; place-items: center; border-radius: 11px; background: #eaf5f0; color: var(--teal); font-size: 12px; font-weight: 750; }
        .section-heading h2 { font-size: 17px; font-weight: 700; margin: 0 0 3px; }
        .section-heading p { font-size: 12px; margin: 0; color: var(--muted); }
        .form-label { font-weight: 600; font-size: 12px; margin-bottom: 8px; }
        .form-control, .form-select { min-height: 46px; border: 1px solid #dce5e0; border-radius: 10px; font-size: 14px; color: var(--ink); background-color: #fcfdfc; padding: 11px 13px; }
        .form-select { padding-right: 36px; }
        .form-control::placeholder { color: #82928e; font-size: 12px; }
        .form-control:focus, .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px #087f7518; background-color: white; }
        .form-text { font-size: 11px; color: var(--muted); }
        .section-divider { border: 0; border-top: 1px solid var(--line); opacity: 1; margin: 30px 0; }
        .registration-notice { background: #f2f6f2; border-radius: 12px; padding: 15px 17px; color: var(--muted); font-size: 12px; line-height: 1.7; margin: 26px 0 20px; }
        .btn-primary { --bs-btn-bg: #087f75; --bs-btn-border-color: #087f75; --bs-btn-hover-bg: #06675e; --bs-btn-hover-border-color: #06675e; --bs-btn-active-bg: #06594f; --bs-btn-active-border-color: #06594f; border-radius: 11px; padding: 13px 20px; font-size: 14px; font-weight: 650; }
        .submit-note { text-align: center; margin: 12px 0 0; color: var(--muted); font-size: 11px; }
        .queue-number { font-size: clamp(3rem, 10vw, 5rem); font-weight: 800; color: var(--teal); }
        .receipt-card { max-width: 700px; margin: 20px auto; }
        .success-mark { width: 64px; height: 64px; display: grid; place-items: center; border-radius: 50%; background: #e7f5ed; color: var(--teal); font-size: 28px; margin: 0 auto 20px; }
        .public-footer { border-top: 1px solid var(--line); margin-top: 20px; padding: 22px 0; font-size: 11px; color: var(--muted); }
        @media (max-width: 991px) { .registration-grid { grid-template-columns: 250px minmax(0, 1fr); gap: 20px; } .guide-panel { padding: 22px; } .form-body, .form-intro { padding: 24px; } }
        @media (max-width: 767px) { .registration-grid { grid-template-columns: 1fr; } .registration-guide { position: static; } .guide-panel { display: none; } .returning-patient { margin: 0; padding: 16px 20px; } .registration-hero { margin-bottom: 22px; } .registration-hero h1 { letter-spacing: -1px; } .form-intro { flex-wrap: wrap; gap: 10px; } .form-body, .form-intro { padding: 22px; } .header-note { font-size: 10px; max-width: 110px; text-align: right; } .brand { font-size: 16px; } }
        @media print { .no-print { display: none !important; } body { background: white; } .registration-card { box-shadow: none; } }
    </style>
</head>
<body>
    <header class="public-header no-print">
        <nav class="container registration-shell py-3 d-flex justify-content-between align-items-center gap-3" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('registration.create') }}"><span class="brand-mark" aria-hidden="true">+</span>{{ config('app.name', 'Klinik') }}</a>
            <span class="header-note">Pendaftaran pasien<br><strong>Mudah, dari mana saja.</strong></span>
        </nav>
    </header>
    <main class="container registration-shell py-4 py-md-5">@yield('content')</main>
    <footer class="public-footer no-print"><div class="container registration-shell d-flex flex-wrap justify-content-between gap-2"><span>&copy; {{ now()->year }} {{ config('app.name', 'Klinik') }}</span><span>Pendaftaran mandiri · Pelayanan dimulai dari sini</span></div></footer>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Klinik App') }} - Masuk ke Sistem</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #0f172a;
            --accent-teal: #0d9488;
            --bg-page: #f8fafc;
            --card-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.09), 0 0 1px 1px rgba(15, 23, 42, 0.04);
        }

        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.07) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(13, 148, 136, 0.07) 0%, transparent 40%),
                        var(--bg-page);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            padding: 20px 15px;
            position: relative;
        }

        /* Subtle Medical Cross Watermark Background */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(rgba(37, 99, 235, 0.05) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: var(--card-shadow);
            padding: 36px 32px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .auth-logo-badge {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            color: #ffffff;
            font-size: 28px;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }

        .auth-title {
            color: #0f172a;
            font-weight: 800;
            font-size: 24px;
            letter-spacing: -0.5px;
            text-align: center;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 13.5px;
            text-align: center;
            margin-bottom: 26px;
        }

        .form-label {
            font-weight: 600;
            font-size: 13.5px;
            color: #334155;
            margin-bottom: 7px;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }

        .input-group-modern .input-group-text {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #64748b;
            padding: 11px 15px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .input-group-modern .form-control {
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 11px 15px 11px 0;
            font-size: 14.5px;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .input-group-modern.has-toggle .form-control {
            border-right: none;
            border-radius: 0;
        }

        .input-group-modern .btn-toggle-password {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            color: #94a3b8;
            padding: 11px 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .input-group-modern .btn-toggle-password:hover {
            color: #2563eb;
            background-color: #f1f5f9;
        }

        .input-group-modern:focus-within .input-group-text,
        .input-group-modern:focus-within .form-control,
        .input-group-modern:focus-within .btn-toggle-password {
            border-color: #2563eb;
            background-color: #ffffff;
        }

        .input-group-modern:focus-within .input-group-text {
            color: #2563eb;
        }

        .input-group-modern:focus-within {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            border-radius: 12px;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
        }

        .btn-auth-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 20px;
            border-radius: 12px;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 8px 18px -4px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-auth-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            box-shadow: 0 12px 22px -4px rgba(37, 99, 235, 0.5);
            color: #ffffff;
        }

        .btn-auth-primary:active {
            transform: translateY(0);
        }

        .demo-credential-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 14px 16px;
            margin-top: 24px;
        }

        .demo-badge-btn {
            font-size: 11.5px;
            padding: 4px 10px;
            border-radius: 8px;
            background: #e2e8f0;
            color: #334155;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            font-weight: 600;
        }

        .demo-badge-btn:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .auth-footer-text {
            color: #64748b;
            font-size: 12.5px;
            text-align: center;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        {{ $slot ?? '' }}
        @yield('content')

        <div class="auth-footer-text">
            &copy; {{ date('Y') }} <strong>{{ config('app.name', 'Klinik App') }}</strong> &bull; Sistem Informasi Layanan Medis
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
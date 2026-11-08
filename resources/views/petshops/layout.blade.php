<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PawKu</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <style>
        :root {
            --ps-primary: #2E9E6B;
            --ps-text: #1f2937;
            --ps-muted: #6b7280;
            --ps-line: #e5e7eb;
            --ps-warn: #f59e0b;
            --ps-danger: #ef4444;
        }

        .ps-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 96px 20px 64px;
            font-family: inherit;
            color: var(--ps-text);
        }

        .ps-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 6px;
        }

        .ps-sub {
            color: var(--ps-muted);
            margin: 0 0 20px;
            font-size: 14px;
        }

        .ps-back {
            display: inline-block;
            font-size: 13px;
            color: var(--ps-muted);
            text-decoration: none;
            margin-bottom: 12px;
        }

        .ps-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 18px;
            border-radius: 14px;
            border: 0;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .ps-btn-primary {
            background: var(--ps-primary);
            color: #fff;
        }

        .ps-btn-soft {
            background: #eef7f2;
            color: var(--ps-primary);
        }

        /* ---------- Daftar ---------- */
        .ps-search {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .ps-search input {
            flex: 1;
            min-width: 220px;
            padding: 12px 16px;
            border: 1px solid var(--ps-line);
            border-radius: 14px;
            font: inherit;
            font-size: 14px;
            background: #fff;
        }

        .ps-search input:focus {
            outline: none;
            border-color: var(--ps-primary);
            box-shadow: 0 0 0 3px rgba(46, 158, 107, .15);
        }

        .ps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }

        .ps-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(46, 158, 107, .10);
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s;
        }

        .ps-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(46, 158, 107, .16);
        }

        .ps-thumb {
            flex: 0 0 auto;
            width: 68px;
            height: 68px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .ps-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ps-card-body {
            min-width: 0;
            flex: 1;
        }

        .ps-card-name {
            font-weight: 700;
            font-size: 15px;
        }

        .ps-addr {
            font-size: 12.5px;
            color: var(--ps-muted);
            margin-top: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ps-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 6px;
        }

        .ps-empty {
            text-align: center;
            padding: 56px 20px;
        }

        .ps-empty .ico {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            background: #eef7f2;
        }

        .ps-pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 26px;
            font-size: 13px;
            color: var(--ps-muted);
        }

        /* ---------- Detail ---------- */
        .ps-hero {
            height: 300px;
            border-radius: 24px;
            overflow: hidden;
            background: #eef7f2;
        }

        .ps-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ps-hero-fallback {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }

        .ps-name {
            font-size: 26px;
            font-weight: 800;
            margin: 18px 0 4px;
        }

        .ps-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ps-pills .ps-pill {
            margin-top: 6px;
        }

        .ps-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
            margin-top: 16px;
            max-width: 520px;
        }

        .ps-action {
            text-align: center;
            padding: 12px 8px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .ps-gallery {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            margin-top: 16px;
            padding-bottom: 4px;
        }

        .ps-gallery img {
            flex: 0 0 auto;
            width: 84px;
            height: 84px;
            border-radius: 14px;
            object-fit: cover;
            cursor: zoom-in;
        }

        .ps-section {
            margin-top: 28px;
        }

        .ps-h2 {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 12px;
        }

        .ps-box {
            background: #fff;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 4px 14px rgba(46, 158, 107, .07);
            font-size: 14px;
            line-height: 1.55;
        }

        .ps-box-warn {
            background: rgba(245, 158, 11, .10);
            border: 1px solid rgba(245, 158, 11, .35);
            box-shadow: none;
        }

        .ps-hours-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid var(--ps-line);
            font-size: 13.5px;
        }

        .ps-hours-row:last-child {
            border-bottom: 0;
        }

        .ps-hours-row.today {
            font-weight: 800;
            color: var(--ps-primary);
        }

        .ps-service {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .ps-service-ico {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex: 0 0 auto;
        }

        .ps-review {
            background: #fff;
            border-radius: 18px;
            padding: 14px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .ps-stars {
            color: var(--ps-warn);
            letter-spacing: 1px;
        }

        .ps-bar {
            height: 6px;
            border-radius: 6px;
            background: #eceff1;
            overflow: hidden;
            flex: 1;
        }

        .ps-bar>span {
            display: block;
            height: 100%;
            background: var(--ps-warn);
        }

        .ps-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 14px 0;
        }

        .ps-chip {
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid var(--ps-line);
            font-size: 12px;
            font-weight: 700;
            color: var(--ps-muted);
            text-decoration: none;
            background: #fff;
        }

        .ps-chip.on {
            background: var(--ps-primary);
            border-color: var(--ps-primary);
            color: #fff;
        }

        .ps-lock {
            text-align: center;
            font-size: 13.5px;
            color: var(--ps-muted);
        }

        dialog.ps-lb {
            border: 0;
            padding: 0;
            background: transparent;
            max-width: 96vw;
            max-height: 96vh;
        }

        dialog.ps-lb::backdrop {
            background: rgba(0, 0, 0, .85);
        }

        dialog.ps-lb img {
            max-width: 96vw;
            max-height: 90vh;
            display: block;
            margin: auto;
            border-radius: 12px;
        }

        .ps-lb-bar {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .ps-lb-bar button {
            background: rgba(255, 255, 255, .92);
            border: 0;
            border-radius: 20px;
            padding: 8px 16px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .ps-hero {
                height: 220px;
                border-radius: 18px;
            }

            .ps-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php
        $psLoggedIn = \App\Support\MitraDisplay::isLoggedIn();
        $psDashboard = match (session('supabase.profile.role')) {
            'super_admin' => route('superadmin.dashboard'),
            'admin' => route('admin.dashboard'),
            'mitra' => route('owner.mitra'),
            default => route('owner.dashboard'),
        };
    @endphp

    <header class="navbar">
        <div class="nav-container">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="logo-pawku" style="width: 70px;">
            </a>

            <nav class="nav-menu">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/') }}#features">Features</a>
                <a href="{{ url('/') }}#tips">Pet Care</a>
                <a href="{{ route('petshops.index') }}" class="active">Petshop</a>
                <a href="{{ url('/') }}#about">About</a>
            </nav>

            <div class="nav-actions">
                @if ($psLoggedIn)
                    <a href="{{ $psDashboard }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="login-link">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                @endif
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <div class="footer-logo-icon">🐾</div>
                <span>PawKu</span>
            </div>
            <p class="footer-copy">© {{ date('Y') }} PawKu. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
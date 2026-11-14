@php
    $m = request()->attributes->get('mitra');
    $mi = \App\Support\MitraType::info($m->mitra_type ?? null);
    $isAdminKlinik = request()->routeIs('admin_mitra.*');
    $clinics = $clinics ?? collect();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu Mitra</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --primary-light: #f5f3ff;
            --green: #2E9E6B;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --heading: #1e293b;
            --body: #475569;
            --muted: #8c8c8c;
            --danger: #ef4444;
            --warning: #faad14;
            --sidebar: 260px;
            --header: 68px;
            --r: 10px;
            --rl: 14px;
            --shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 1px 6px -1px rgba(0, 0, 0, .02)
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--body);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        .sidebar {
            width: var(--sidebar);
            background: #fff;
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            display: flex;
            flex-direction: column;
            z-index: 100
        }

        .brand {
            height: var(--header);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid #f0f2f5;
            gap: 12px;
            text-decoration: none
        }

        .brand img {
            width: 36px;
            height: 36px;
            object-fit: contain
        }

        .brand-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--heading);
            display: block
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-light);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 2px
        }

        .menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .menu-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .8px;
            padding: 8px 12px 6px
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--body);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: var(--r);
            transition: .2s
        }

        .menu a:hover {
            background: #f1f5f9;
            color: var(--primary)
        }

        .menu a.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600
        }

        .menu svg {
            width: 18px;
            height: 18px
        }

        .side-foot {
            padding: 16px;
            border-top: 1px solid #f0f2f5
        }

        .me {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--r);
            background: var(--bg);
            border: 1px solid #f0f2f5
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            flex-shrink: 0;
            overflow: hidden
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .me-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--heading);
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px
        }

        .me-role {
            font-size: 11px;
            color: var(--muted)
        }

        .wrap {
            margin-left: var(--sidebar);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0
        }

        .topbar {
            height: var(--header);
            background: #fff;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 99;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 32px;
            gap: 16px
        }

        .btn-logout {
            background: #fff;
            border: 1px solid #fee2e2;
            color: var(--danger);
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--r);
            cursor: pointer
        }

        .btn-logout:hover {
            background: #fef2f2
        }

        .content {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--heading)
        }

        .page-sub {
            font-size: 13.5px;
            margin-top: 4px
        }

        .group-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 12px
        }

        .grid {
            display: grid;
            gap: 20px
        }

        .g4 {
            grid-template-columns: repeat(4, 1fr)
        }

        .g3 {
            grid-template-columns: repeat(3, 1fr)
        }

        .g2 {
            grid-template-columns: repeat(2, 1fr)
        }

        @media(max-width:1000px) {

            .g4,
            .g3,
            .g2 {
                grid-template-columns: 1fr
            }
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--rl);
            padding: 20px 22px;
            box-shadow: var(--shadow)
        }

        .stat {
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted)
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px
        }

        .stat-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--heading)
        }

        .i-blue {
            background: #e6f4ff;
            color: #1677ff
        }

        .i-green {
            background: #f6ffed;
            color: #52c41a
        }

        .i-gold {
            background: #fffbe6;
            color: #faad14
        }

        .i-red {
            background: #fff2f0;
            color: #ff4d4f
        }

        .i-purple {
            background: #f9f0ff;
            color: #722ed1
        }

        .pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600
        }

        .p-active,
        .p-approved {
            background: #ecfdf5;
            color: #065f46
        }

        .p-pending {
            background: #fffbe6;
            color: #ad6800
        }

        .p-rejected {
            background: #fff2f0;
            color: #cf1322
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--r);
            font-size: 13px;
            font-weight: 500
        }

        .alert-ok {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #237804
        }

        .alert-err {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #a8071a
        }

        @media(max-width:768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .wrap {
                margin-left: 0;
            }

            .topbar {
                padding: 0 16px;
            }

            .content {
                padding: 20px 16px 48px;
            }
        }
    </style>
    <style>
        :root { --primary: {{ $mi['color'] }}; --primary-light: {{ $mi['soft'] }}; }
    </style>
    <style>
        /* ===== Komponen dashboard admin klinik ===== */
        .stat-sub { font-size: 12px; color: var(--muted) }
        .card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 14px }
        .card-title { font-size: 15px; font-weight: 700; color: var(--heading) }
        .link { color: var(--primary); font-size: 13px; font-weight: 600; text-decoration: none }
        .link:hover { text-decoration: underline }
        .muted { color: var(--muted) }
        .empty { text-align: center; padding: 28px 12px; color: var(--muted); font-size: 13px }

        .tbl-wrap { overflow-x: auto }
        .tbl { width: 100%; border-collapse: collapse; font-size: 13px }
        .tbl th { text-align: left; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; padding: 10px 12px; border-bottom: 1px solid var(--border); white-space: nowrap }
        .tbl td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top }
        .tbl tr:last-child td { border-bottom: 0 }
        .tbl .strong { font-weight: 600; color: var(--heading) }
        .tbl .small { font-size: 12px; color: var(--muted); margin-top: 2px }
        .queue { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 8px; border-radius: 10px; background: var(--primary-light); color: var(--primary); font-weight: 700 }

        .st { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap }
        .st-menunggu { background: #fffbe6; color: #ad6800 }
        .st-dikonfirmasi { background: #e6f4ff; color: #0958d9 }
        .st-dilayani { background: #f9f0ff; color: #531dab }
        .st-selesai { background: #ecfdf5; color: #065f46 }
        .st-ditolak, .st-dibatalkan { background: #fff2f0; color: #cf1322 }
        .st-kedaluwarsa { background: #f1f5f9; color: #64748b }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid transparent; border-radius: var(--r); font-size: 13px; font-weight: 600; padding: 9px 16px; cursor: pointer; text-decoration: none; transition: .2s }
        .btn-sm { padding: 6px 12px; font-size: 12px }
        .btn-primary { background: var(--primary); color: #fff }
        .btn-primary:hover { filter: brightness(.92) }
        .btn-danger { background: #fff; border-color: #fecaca; color: var(--danger) }
        .btn-danger:hover { background: #fef2f2 }
        .btn-ghost { background: #fff; border-color: var(--border); color: var(--body) }
        .btn-ghost:hover { background: #f1f5f9 }
        .btn[disabled] { opacity: .5; cursor: not-allowed }
        .actions { display: flex; gap: 6px; flex-wrap: wrap }
        .actions form { margin: 0 }
        .select { font-size: 12.5px; padding: 6px 10px; border: 1px solid var(--border); border-radius: var(--r); background: #fff; color: var(--body); max-width: 190px }

        .tabs { display: flex; gap: 8px; flex-wrap: wrap }
        .tab { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 999px; border: 1px solid var(--border); background: #fff; color: var(--body); font-size: 13px; font-weight: 600; text-decoration: none }
        .tab:hover { background: #f1f5f9 }
        .tab.active { background: var(--primary-light); border-color: var(--primary); color: var(--primary) }
        .tab .count { font-size: 11px; background: #f1f5f9; color: var(--body); border-radius: 999px; padding: 1px 8px }
        .tab.active .count { background: #fff; color: var(--primary) }

        .bar-row + .bar-row { margin-top: 14px }
        .bar-top { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; margin-bottom: 6px }
        .bar-top b { color: var(--heading); font-weight: 600 }
        .bar-track { height: 8px; background: #f1f5f9; border-radius: 99px; overflow: hidden }
        .bar-fill { height: 100%; background: var(--primary); border-radius: 99px }

        .pager { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; font-size: 13px }
        .g21 { grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); align-items: start }
        @media (max-width: 1000px) { .g21 { grid-template-columns: 1fr } }
        .g5 { grid-template-columns: repeat(5, minmax(0, 1fr)) }
        @media (max-width: 1200px) { .g5 { grid-template-columns: repeat(3, minmax(0, 1fr)) } }
        @media (max-width: 700px) { .g5 { grid-template-columns: repeat(2, minmax(0, 1fr)) } }

        /* ===== Kartu booking (halaman Booking admin klinik) ===== */
        .bk-list { display: flex; flex-direction: column; gap: 16px }
        .bk { background: #fff; border: 1px solid var(--border); border-radius: 16px; overflow: hidden }
        .bk-head { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-bottom: 1px solid #f1f5f9 }
        .bk-head .queue { width: 48px; height: 48px; font-size: 18px; border-radius: 12px; flex-shrink: 0 }
        .bk-title { flex: 1; min-width: 0 }
        .bk-title b { display: block; font-size: 16px; color: var(--heading) }
        .bk-title span { font-size: 13px; color: var(--muted) }
        .bk-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; padding: 16px 20px }
        .bk-info .lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted); margin-bottom: 4px }
        .bk-info .val { font-size: 14px; color: var(--heading); font-weight: 600 }
        .bk-info .sub { font-size: 12px; color: var(--muted); margin-top: 2px; font-weight: 400 }

        .steps { display: flex; align-items: center; gap: 6px; padding: 0 20px 16px; flex-wrap: wrap }
        .step { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #94a3b8 }
        .step i { width: 20px; height: 20px; border-radius: 50%; background: #e2e8f0; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-style: normal }
        .step.done { color: #16a34a }
        .step.done i { background: #dcfce7; color: #16a34a }
        .step.now { color: var(--primary) }
        .step.now i { background: var(--primary); color: #fff }
        .step-sep { width: 18px; height: 2px; background: #e2e8f0; border-radius: 2px }

        .bk-panels { display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid #f1f5f9 }
        @media (max-width: 900px) { .bk-panels { grid-template-columns: 1fr } }
        .bk-panel { padding: 16px 20px; display: flex; flex-direction: column; gap: 10px }
        .bk-panel + .bk-panel { border-left: 1px solid #f1f5f9; background: #fafbfc }
        @media (max-width: 900px) { .bk-panel + .bk-panel { border-left: 0; border-top: 1px solid #f1f5f9 } }
        .panel-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--muted) }
        .doc-form { display: flex; gap: 8px; flex-wrap: wrap }
        .doc-form .select { flex: 1; min-width: 160px; max-width: none; padding: 10px 12px; font-size: 13.5px }
        .doc-now { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: #ecfdf5; border-radius: 10px; color: #065f46; font-size: 13px; font-weight: 600; align-self: flex-start }
        .doc-none { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: #fff7ed; border-radius: 10px; color: #c2410c; font-size: 13px; font-weight: 600; align-self: flex-start }
        .act-row { display: flex; gap: 10px; flex-wrap: wrap }
        .act-row form { margin: 0; flex: 1; min-width: 150px }
        .act-row .btn { width: 100%; padding: 12px 16px; font-size: 14px }
        .act-row form.act-small { flex: 0 0 auto; min-width: 0 }
        .act-row form.act-small .btn { width: auto }
        .btn-outline-danger { background: #fff; border-color: #dc2626; color: #dc2626 }
        .btn-outline-danger:hover { background: #fef2f2 }
        .btn-success { background: #16a34a; color: #fff }
        .btn-success:hover { filter: brightness(.92) }
        .btn-outline { background: #fff; border-color: var(--primary); color: var(--primary) }
        .btn-outline:hover { background: var(--primary-light) }
        .act-note { font-size: 12.5px; color: #c2410c }
        .act-done { font-size: 13px; color: var(--muted) }
        .hint { padding: 12px 16px; border-radius: var(--r); background: var(--primary-light); color: var(--heading); font-size: 13px }
    </style>
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? session('supabase.user.email', 'Mitra');
    @endphp
    <aside class="sidebar">
        <a href="{{ $isAdminKlinik ? route('admin_mitra.dashboard', $m->id) : route('mitra.dashboard', $m->id) }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div><span class="brand-name">PawKu</span><span class="brand-badge">{{ $mi['brand'] }}</span></div>
        </a>
        <nav class="menu">
            @if ($isAdminKlinik)
                <div class="menu-label" title="{{ $m->name }}">{{ \Illuminate\Support\Str::limit($m->name, 24) }}</div>
                <a href="{{ route('admin_mitra.dashboard', $m->id) }}"
                    class="{{ request()->routeIs('admin_mitra.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('admin_mitra.booking', $m->id) }}"
                    class="{{ request()->routeIs('admin_mitra.booking*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                        <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                    </svg>
                    <span>Booking</span>
                </a>
                @if ($clinics->count() > 1)
                    <div class="menu-label" style="margin-top:12px">Ganti Klinik</div>
                    @foreach ($clinics as $c)
                        @if ((string) $c->id !== (string) $m->id)
                            <a href="{{ route('admin_mitra.dashboard', $c->id) }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m17 1 4 4-4 4" />
                                    <path d="M3 11V9a4 4 0 0 1 4-4h14" />
                                    <path d="m7 23-4-4 4-4" />
                                    <path d="M21 13v2a4 4 0 0 1-4 4H3" />
                                </svg>
                                <span>{{ \Illuminate\Support\Str::limit($c->name, 22) }}</span>
                            </a>
                        @endif
                    @endforeach
                @endif
            @else
            <div class="menu-label" title="{{ $m->name }}">{{ \Illuminate\Support\Str::limit($m->name, 24) }}</div>
            <a href="{{ route('mitra.dashboard', $m->id) }}"
                class="{{ request()->routeIs('mitra.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('mitra.bookings', $m->id) }}"
                class="{{ request()->routeIs('mitra.bookings') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                    <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                </svg>
                <span>Booking</span>
            </a>
            <a href="{{ route('mitra.services', $m->id) }}"
                class="{{ request()->routeIs('mitra.services') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="6" r="3" />
                    <circle cx="6" cy="18" r="3" />
                    <line x1="20" y1="4" x2="8.12" y2="15.88" />
                    <line x1="14.47" y1="14.48" x2="20" y2="20" />
                    <line x1="8.12" y1="8.12" x2="12" y2="12" />
                </svg>
                <span>Layanan</span>
            </a>
            <a href="{{ route('owner.mitra.profile', $m->id) }}"
                class="{{ request()->routeIs('owner.mitra.profile*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span>Profil</span>
            </a>
            <div class="menu-label" style="margin-top:12px">Lainnya</div>
            <a href="{{ route('owner.mitra') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m17 1 4 4-4 4" />
                    <path d="M3 11V9a4 4 0 0 1 4-4h14" />
                    <path d="m7 23-4-4 4-4" />
                    <path d="M21 13v2a4 4 0 0 1-4 4H3" />
                </svg>
                <span>Ganti Mitra</span>
            </a>
            @endif
        </nav>
        <div class="side-foot">
            <div class="me">
                <div class="avatar">
                    @if (!empty($sp['avatar_url']))
                        <img src="{{ $sp['avatar_url'] }}" alt="">
                    @else
                        {{ strtoupper(substr($name, 0, 1)) }}
                    @endif
                </div>
                <div><span class="me-name">{{ $name }}</span><span class="me-role">{{ $isAdminKlinik ? 'Admin Klinik' : $mi['label'] }}</span></div>
            </div>
        </div>
    </aside>

    <div class="wrap">
        <header class="topbar">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            <button class="btn-logout" type="button" onclick="confirmLogout()">Keluar</button>
        </header>
        <main class="content">
            @yield('content')
        </main>
    </div>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Keluar dari akun?',
                text: 'Sesi Anda akan dihapus dan Anda harus login kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @js(session('success')),
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @js(session('error')),
                    timer: 4000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
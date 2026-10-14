<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu Superadmin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #1677ff;
            --primary-hover: #0958d9;
            --primary-light: #e6f4ff;
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

        button,
        input,
        select,
        textarea {
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

        .menu .badge {
            margin-left: auto;
            background: var(--warning);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px
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

        .g3 {
            grid-template-columns: repeat(3, 1fr)
        }

        .g2 {
            grid-template-columns: repeat(2, 1fr)
        }

        @media(max-width:1000px) {

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

        .tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .tab {
            padding: 9px 16px;
            border-radius: var(--r);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--body);
            background: #fff;
            border: 1px solid var(--border)
        }

        .tab.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary)
        }

        .tab .n {
            margin-left: 6px;
            opacity: .8
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px
        }

        th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--muted);
            padding: 10px 12px;
            border-bottom: 1px solid var(--border)
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
            color: var(--heading)
        }

        tr:last-child td {
            border-bottom: none
        }

        .sub {
            font-size: 12px;
            color: var(--muted)
        }

        .table-wrap {
            overflow-x: auto
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

        .p-inactive,
        .p-pending {
            background: #fffbe6;
            color: #ad6800
        }

        .p-blocked,
        .p-rejected {
            background: #fff2f0;
            color: #cf1322
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #f1f5f9;
            color: var(--heading);
            text-decoration: none;
            font-family: inherit
        }

        .btn:hover {
            background: #e2e8f0
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary)
        }

        .btn-primary:hover {
            background: var(--primary-hover)
        }

        .btn-green {
            background: var(--green);
            color: #fff;
            border-color: var(--green)
        }

        .btn-green:hover {
            opacity: .9;
            background: var(--green)
        }

        .btn-danger {
            background: #fff;
            color: var(--danger);
            border-color: #fecaca
        }

        .btn-danger:hover {
            background: #fef2f2
        }

        .btn-warn {
            background: #fff;
            color: #ad6800;
            border-color: #ffe58f
        }

        .btn-warn:hover {
            background: #fffbe6
        }

        .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .actions form {
            display: inline
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

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center
        }

        .input {
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 9px 14px;
            font-size: 13px;
            font-family: inherit;
            color: var(--heading);
            width: 100%;
            outline: none;
            background: #fff
        }

        .input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 119, 255, .12)
        }

        label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--heading);
            display: block;
            margin-bottom: 6px
        }

        .field {
            margin-bottom: 14px
        }

        .err {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px
        }

        dialog {
            border: none;
            border-radius: var(--rl);
            padding: 26px;
            width: min(440px, 92vw);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            margin: auto
        }

        dialog::backdrop {
            background: rgba(15, 23, 42, .45)
        }

        .empty {
            text-align: center;
            padding: 36px 0;
            color: var(--muted)
        }

        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 16px;
            font-size: 13px
        }

        .pagination a,
        .pagination span {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            color: var(--body)
        }

        .pagination .active span {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary)
        }

        .pagination svg {
            width: 14px;
            height: 14px
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? session('supabase.user.email', 'Superadmin');
        $pendingCount = 0;
        try {
            $pendingCount = \Illuminate\Support\Facades\DB::table('mitras')->where('status', 'pending')->count();
        } catch (\Throwable $e) {
        }
    @endphp
    <aside class="sidebar">
        <a href="{{ route('superadmin.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div><span class="brand-name">PawKu</span><span class="brand-badge">SUPERADMIN</span></div>
        </a>
        <nav class="menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard Monitoring</span>
            </a>
            <a href="{{ route('superadmin.petshops') }}"
                class="{{ request()->routeIs('superadmin.petshops*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>Persetujuan Petshop</span>
                @if($pendingCount > 0)<span class="badge">{{ $pendingCount }}</span>@endif
            </a>
            <a href="{{ route('superadmin.admins') }}"
                class="{{ request()->routeIs('superadmin.admins*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span>Kelola Admin</span>
            </a>
        </nav>
        <div class="side-foot">
            <div class="me">
                <div class="avatar">
                    @if(!empty($sp['avatar_url']))<img src="{{ $sp['avatar_url'] }}"
                    alt="">@else{{ strtoupper(substr($name, 0, 1)) }}@endif
                </div>
                <div><span class="me-name">{{ $name }}</span><span class="me-role">Super Admin</span></div>
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

        function confirmAction(formEl, message, isDanger) {
            Swal.fire({
                title: 'Konfirmasi Aksi',
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#ef4444' : '#1677ff',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
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
            @if(session('error'))
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
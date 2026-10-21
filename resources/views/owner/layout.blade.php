<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu</title>
    <link rel="icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <style>
        /* =====================================================
           Palet PawKu (selaras dengan AppColors di app Flutter)
           Nama variabel lama dipertahankan agar halaman owner
           lain tidak rusak.
           ===================================================== */
        :root {
            /* Identitas utama */
            --primary: #E87D2F;
            --primary-hover: #C9611A;
            --primary-dark: #C9611A;
            --primary-light: #FCE3CC;
            --brown: #5E2F20;

            /* Latar & garis */
            --bg: #FDF3E7;
            --surface: #ffffff;
            --border: #F0E0D0;
            --border-strong: #E9D5C2;

            /* Teks */
            --heading: #5E2F20;
            --body: #8C6B5D;
            --muted: #A88B7D;

            /* Status */
            --success: #2E9E6B;
            --success-soft: #DDF3E9;
            --warning: #E8A33D;
            --warning-soft: #FCEFD6;
            --danger: #D9534F;
            --danger-soft: #FBE3E2;
            --info: #3FA9D6;
            --info-soft: #DCF3FC;
            --blue: #3FA9D6;

            /* Warna per fitur */
            --feeding: #FF9F45;
            --feeding-soft: #FFEBDA;
            --bathing: #4EC5F1;
            --bathing-soft: #DCF3FC;
            --vaccine: #FF6B8B;
            --vaccine-soft: #FFE1E7;
            --weight: #57C785;
            --weight-soft: #DFF6E7;
            --booking: #B185DB;
            --booking-soft: #F0E4FA;
            --petshop: #FFB100;
            --petshop-soft: #FFF3D6;

            /* Ukuran */
            --sidebar: 260px;
            --header: 68px;
            --r: 12px;
            --rl: 16px;
            --shadow: 0 1px 2px rgba(94, 47, 32, .04), 0 4px 14px -6px rgba(94, 47, 32, .08)
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

        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: var(--sidebar);
            background: var(--surface);
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
            padding: 0 22px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            text-decoration: none
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain
        }

        .brand-name {
            font-size: 19px;
            font-weight: 800;
            color: var(--brown);
            display: block;
            line-height: 1.1
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary-dark);
            background: var(--primary-light);
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 3px
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
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
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
            transition: background .2s, color .2s
        }

        .menu a:hover {
            background: var(--bg);
            color: var(--primary-dark)
        }

        .menu a.active {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 10px -4px rgba(232, 125, 47, .6)
        }

        .menu svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0
        }

        .side-foot {
            padding: 16px;
            border-top: 1px solid var(--border)
        }

        .me {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--r);
            background: var(--bg);
            border: 1px solid var(--border)
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary-dark);
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

        /* ---------- Area utama ---------- */
        .wrap {
            margin-left: var(--sidebar);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0
        }

        .topbar {
            height: var(--header);
            background: var(--bg);
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
            background: var(--surface);
            border: 1px solid var(--danger-soft);
            color: var(--danger);
            font-size: 13px;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: background .2s
        }

        .btn-logout:hover {
            background: var(--danger-soft)
        }

        .content {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 28px
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--heading)
        }

        .page-sub {
            font-size: 13.5px;
            margin-top: 4px
        }

        .group-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--heading);
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

        /* ---------- Kartu ---------- */
        .card {
            background: var(--surface);
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
            color: var(--body)
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            color: var(--heading)
        }

        /* Warna ikon (nama lama dipetakan ke palet Pawku) */
        .i-primary { background: var(--primary-light); color: var(--primary-dark) }
        .i-blue    { background: var(--bathing-soft);  color: var(--info) }
        .i-green   { background: var(--weight-soft);   color: var(--success) }
        .i-gold    { background: var(--petshop-soft);  color: #B87A00 }
        .i-red     { background: var(--vaccine-soft);  color: var(--vaccine) }
        .i-purple  { background: var(--booking-soft);  color: var(--booking) }

        .pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600
        }

        .p-active,
        .p-approved { background: var(--success-soft); color: #1d6b49 }
        .p-pending  { background: var(--warning-soft); color: #9a6410 }
        .p-rejected { background: var(--danger-soft);  color: #a63a37 }

        .alert {
            padding: 12px 16px;
            border-radius: var(--r);
            font-size: 13px;
            font-weight: 500
        }

        .alert-ok {
            background: var(--success-soft);
            border: 1px solid #b6e3cf;
            color: #1d6b49
        }

        .alert-err {
            background: var(--danger-soft);
            border: 1px solid #f3c4c2;
            color: #a63a37
        }

        /* ---------- Tombol umum (bentuk pil seperti di app) ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 30px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s
        }

        .btn-primary { background: var(--primary); color: #fff }
        .btn-primary:hover { background: var(--primary-hover) }
        .btn-outline { background: transparent; color: var(--primary); border-color: var(--primary) }
        .btn-outline:hover { background: var(--primary-light) }

        input[type=text], input[type=email], input[type=password], input[type=number],
        input[type=date], select, textarea {
            border: 1px solid var(--border-strong);
            border-radius: 12px;
            background: #fff;
            color: var(--heading);
            padding: 10px 14px
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(232, 125, 47, .18)
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

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important }
        }
    </style>
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? session('supabase.user.email', 'Pengguna');
    @endphp
    <aside class="sidebar">
        <a href="{{ route('owner.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div><span class="brand-name">PawKu</span><span class="brand-badge">Pemilik Kucing</span></div>
        </a>
        <nav class="menu">
            <div class="menu-label">Menu utama</div>
            <a href="{{ route('owner.dashboard') }}"
                class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('owner.vaccine.calculator') }}"
                class="{{ request()->routeIs('owner.vaccine.calculator*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 2 4 4" />
                    <path d="m17 7 3-3" />
                    <path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5" />
                    <path d="m9 11 4 4" />
                    <path d="m5 19-3 3" />
                    <path d="m14 4 6 6" />
                </svg>
                <span>Kalkulator Vaksin</span>
            </a>
            <a href="{{ route('owner.mitra') }}"
                class="{{ request()->routeIs('owner.mitra*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m2 7 4.4-4.4A2 2 0 0 1 7.8 2h8.4a2 2 0 0 1 1.4.6L22 7" />
                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                    <path d="M2 7h20v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0Z" />
                </svg>
                <span>Mitra</span>
            </a>
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
                <div><span class="me-name">{{ $name }}</span><span class="me-role">Pemilik Kucing</span></div>
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
        const pawkuSwal = {
            confirmButtonColor: '#E87D2F',
            cancelButtonColor: '#8C6B5D',
            color: '#5E2F20',
            background: '#ffffff',
            customClass: {
                popup: 'swal-pawku',
                confirmButton: 'swal-pawku-btn',
                cancelButton: 'swal-pawku-btn',
            },
        };

        function confirmLogout() {
            Swal.fire({
                ...pawkuSwal,
                title: 'Keluar dari akun?',
                text: 'Sesi Anda akan dihapus dan Anda harus login kembali.',
                icon: 'warning',
                iconColor: '#E8A33D',
                showCancelButton: true,
                confirmButtonColor: '#D9534F',
                confirmButtonText: 'Ya, keluar',
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
                    ...pawkuSwal,
                    icon: 'success',
                    iconColor: '#2E9E6B',
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
                    ...pawkuSwal,
                    icon: 'error',
                    iconColor: '#D9534F',
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
    <style>
        .swal-pawku { border-radius: 16px; font-family: 'Montserrat', sans-serif }
        .swal-pawku-btn { border-radius: 30px !important; font-weight: 600 !important }
    </style>
    @stack('scripts')
</body>

</html>
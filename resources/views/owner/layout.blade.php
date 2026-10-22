<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/dashboard-layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $su = session('supabase.user', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? ($su['email'] ?? 'Pengguna');
        $role = $sp['role'] ?? 'pemilik_kucing';
        $roleLabel = match ($role) {
            'mitra' => 'Mitra Usaha',
            'admin' => 'Admin PawKu',
            'super_admin' => 'Super Admin',
            default => 'Pemilik Kucing',
        };
    @endphp

    <div class="scrim" id="scrim" aria-hidden="true"></div>

    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <a href="{{ route('owner.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div>
                <span class="brand-name">PawKu</span>
                <span class="brand-badge">PEMILIK KUCING</span>
            </div>
        </a>

        <nav class="menu">
            <div class="menu-label">Menu Utama</div>
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
            <a href="{{ route('owner.cats.index') }}"
                class="{{ request()->routeIs('owner.cats*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="11" cy="4" r="2" />
                    <circle cx="18" cy="8" r="2" />
                    <circle cx="20" cy="16" r="2" />
                    <path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z" />
                </svg>
                <span>Kucing Saya</span>
            </a>
            <a href="{{ route('owner.vaccine.calculator') }}"
                class="{{ request()->routeIs('owner.vaccine.calculator*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m18 2 4 4" />
                    <path d="m17 7 3-3" />
                    <path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5" />
                    <path d="m9 11 4 4" />
                    <path d="m5 19-3 3" />
                    <path d="m14 4 6 6" />
                </svg>
                <span>Kalkulator Vaksin</span>
            </a>

            <div class="menu-label">Bisnis &amp; Kemitraan</div>
            <a href="{{ route('owner.mitra') }}" class="{{ request()->routeIs('owner.mitra*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m2 7 4.4-4.4A2 2 0 0 1 7.8 2h8.4a2 2 0 0 1 1.4.6L22 7" />
                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                    <path d="M2 7h20v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0Z" />
                </svg>
                <span>Mitra Usaha</span>
            </a>
        </nav>

        <div class="side-foot">
            <div class="me">
                <div class="avatar">
                    @if (!empty($sp['avatar_url']))
                        <img src="{{ $sp['avatar_url'] }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr($name, 0, 1)) }}
                    @endif
                </div>
                <div style="min-width:0;flex:1;">
                    <span class="me-name">{{ $name }}</span>
                    @if (!empty($su['email']))
                        <span class="me-mail">{{ $su['email'] }}</span>
                    @endif
                    <span class="me-role">{{ $roleLabel }}</span>
                </div>
            </div>
        </div>
    </aside>

    <div class="wrap">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" id="menuToggle" type="button" aria-label="Buka menu" aria-controls="sidebar"
                    aria-expanded="false">
                    <span class="bars"></span>
                </button>
                <div class="topbar-title">
                    PawKu <span>/ @yield('title')</span>
                </div>
            </div>
            <div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                <button class="btn-logout" type="button" onclick="confirmLogout()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            const body = document.body;
            const toggle = document.getElementById('menuToggle');
            const scrim = document.getElementById('scrim');

            if (!toggle || !scrim) return;

            function setNav(open) {
                body.classList.toggle('nav-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
            }

            toggle.addEventListener('click', () => setNav(!body.classList.contains('nav-open')));
            scrim.addEventListener('click', () => setNav(false));
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setNav(false); });
            document.querySelectorAll('.menu a').forEach((a) => a.addEventListener('click', () => setNav(false)));
            window.matchMedia('(min-width: 1025px)').addEventListener('change', (e) => { if (e.matches) setNav(false); });
        })();

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

        document.addEventListener('DOMContentLoaded', function () {
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
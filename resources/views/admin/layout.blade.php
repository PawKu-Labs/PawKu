<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    {{-- Font Awesome (pengganti emoji) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/dashboard-layout.css') }}">

    {{-- CSS khusus per halaman (@push('styles')) dimuat setelah CSS dasar --}}
    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $su = session('supabase.user', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? ($su['email'] ?? 'Admin');
        $email = $su['email'] ?? '';
        $pendingCount = 0;
        try {
            $pendingCount = \Illuminate\Support\Facades\DB::table('mitras')->where('status', 'pending')->count();
        } catch (\Throwable $e) {
        }

        // Ganti nama route di bawah jika berbeda di routes/web.php.
        // Selama route belum ada, link diarahkan ke '#' agar halaman tidak error.
        $articlesUrl = \Illuminate\Support\Facades\Route::has('admin.articles') ? route('admin.articles') : '#';
        $backupUrl = \Illuminate\Support\Facades\Route::has('admin.backup') ? route('admin.backup') : '#';
    @endphp

    <div class="scrim" id="scrim" aria-hidden="true"></div>

    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div>
                <span class="brand-name">PawKu</span>
                <span class="brand-badge">ADMIN</span>
            </div>
        </a>

        <nav class="menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard Monitoring</span>
            </a>
            <a href="{{ route('admin.petshops') }}" class="{{ request()->routeIs('admin.petshops*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>Persetujuan Petshop</span>
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="menu-label">Konten &amp; Data</div>
            <a href="{{ $articlesUrl }}" class="{{ request()->routeIs('admin.articles*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                <span>Kelola Artikel</span>
            </a>
            <a href="{{ \Illuminate\Support\Facades\Route::has('admin.pricing') ? route('admin.pricing') : '#' }}"
                class="{{ request()->routeIs('admin.pricing*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                    <line x1="7" y1="7" x2="7.01" y2="7" />
                </svg>
                <span>Harga &amp; Promo</span>
            </a>
            <a href="{{ \Illuminate\Support\Facades\Route::has('admin.banner') ? route('admin.banner') : '#' }}"
                class="{{ request()->routeIs('admin.banner*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <circle cx="8.5" cy="10" r="1.5" />
                    <polyline points="21 15 16 10 5 19" />
                </svg>
                <span>Banner Promo</span>
            </a>
            <a href="{{ route('admin.activity') }}" class="{{ request()->routeIs('admin.activity*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                <span>Log Aktivitas</span>
            </a>
            <a href="{{ $backupUrl }}" class="{{ request()->routeIs('admin.backup*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3" />
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" />
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" />
                </svg>
                <span>Backup Data</span>
            </a>
        </nav>

        <div class="side-foot">
            <div class="me">
                <div class="avatar">
                    @if(!empty($sp['avatar_url']))
                        <img src="{{ $sp['avatar_url'] }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr($name, 0, 1)) }}
                    @endif
                </div>
                <div style="min-width:0;flex:1;">
                    <span class="me-name">{{ $name }}</span>
                    @if(!empty($email))
                        <span class="me-mail">{{ $email }}</span>
                    @endif
                    <span class="me-role">Admin PawKu</span>
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
                    Portal Admin <span>/ @yield('title')</span>
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
                text: 'Sesi admin Anda akan dihapus dan Anda harus login kembali.',
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
                confirmButtonColor: isDanger ? '#ef4444' : '#F59E3D',
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

        function promptReject(formEl) {
            Swal.fire({
                title: 'Tolak Pendaftaran Petshop?',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan (opsional)',
                inputPlaceholder: 'Tuliskan alasan penolakan untuk pemilik petshop...',
                inputAttributes: {
                    maxlength: 500,
                    rows: 3
                },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak Petshop',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    let inputReason = formEl.querySelector('input[name="rejection_reason"]');
                    if (!inputReason) {
                        inputReason = document.createElement('input');
                        inputReason.type = 'hidden';
                        inputReason.name = 'rejection_reason';
                        formEl.appendChild(inputReason);
                    }
                    inputReason.value = result.value || '';
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
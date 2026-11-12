<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu Dokter</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dokter.css') }}">

    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $su = session('supabase.user', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? ($su['email'] ?? 'Dokter');
        $email = $su['email'] ?? '';

        // Konteks klinik aktif (hasil dari tabel mitra_members). Ganti dengan data asli.
        $clinics = session('dokter.clinics', [
            ['id' => 1, 'name' => 'Klinik PAW_HEALTH Batam Center'],
            ['id' => 2, 'name' => 'Klinik PAW_HEALTH Nagoya'],
        ]);
        $activeClinic = session('dokter.active_clinic', $clinics[0]['id'] ?? null);

        // pending | verified | rejected  (kolom verification_status di mitra_members)
        $verifStatus = session('dokter.verification', 'verified');
    @endphp

    <div class="scrim" id="scrim" aria-hidden="true"></div>

    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <a href="{{ route('dokter.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div>
                <span class="brand-name">PawKu</span>
                <span class="brand-badge">DOKTER</span>
            </div>
        </a>

        {{-- Ganti klinik tanpa login ulang --}}
        @if(count($clinics) > 1)
            <form class="clinic-switch" action="{{ route('dokter.klinik.switch') }}" method="POST">
                @csrf
                <label for="clinicSelect">Klinik aktif</label>
                <select id="clinicSelect" name="clinic_id" onchange="this.form.submit()">
                    @foreach($clinics as $c)
                        <option value="{{ $c['id'] }}" @selected($c['id'] == $activeClinic)>{{ $c['name'] }}</option>
                    @endforeach
                </select>
            </form>
        @endif

        <nav class="menu">
            <div class="menu-label">Praktik</div>
            <a href="{{ route('dokter.dashboard') }}"
                class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('dokter.pasien') }}" class="{{ request()->routeIs('dokter.pasien*') ? 'active' : '' }}">
                <i class="fa-solid fa-paw"></i>
                <span>Pasien &amp; Antrian</span>
            </a>
            <a href="{{ route('dokter.pemeriksaan.create') }}"
                class="{{ request()->routeIs('dokter.pemeriksaan*') ? 'active' : '' }}">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Pemeriksaan Baru</span>
            </a>
            <a href="{{ route('dokter.rekam-medis') }}"
                class="{{ request()->routeIs('dokter.rekam-medis*') ? 'active' : '' }}">
                <i class="fa-solid fa-notes-medical"></i>
                <span>Rekam Medis Saya</span>
            </a>

            <div class="menu-label">Akun</div>
            <a href="{{ route('dokter.profil') }}" class="{{ request()->routeIs('dokter.profil*') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card"></i>
                <span>Profil Praktik</span>
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
                    <span class="me-name">drh. {{ $name }}</span>
                    @if(!empty($email))
                        <span class="me-mail">{{ $email }}</span>
                    @endif
                    <span class="me-role">Dokter Hewan</span>
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
                    Portal Dokter <span>/ @yield('title')</span>
                </div>
            </div>
            <div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                <button class="btn-logout" type="button" onclick="confirmLogout()">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </header>

        <main class="content">
            @if($verifStatus === 'pending')
                <div class="verif-banner" role="status">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <div>
                        <strong>Akun menunggu verifikasi.</strong>
                        Anda baru bisa mengisi dan memfinalisasi rekam medis setelah STR dan SIP diverifikasi.
                        Pastikan data di <a href="{{ route('dokter.profil') }}">Profil Praktik</a> sudah lengkap.
                    </div>
                </div>
            @elseif($verifStatus === 'rejected')
                <div class="verif-banner" role="alert"
                    style="background:var(--dk-red-soft);border-color:#F5B5B5;color:#8A1C1C;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <strong>Verifikasi ditolak.</strong>
                        Perbarui nomor STR atau SIP di <a href="{{ route('dokter.profil') }}">Profil Praktik</a>, lalu
                        ajukan ulang.
                    </div>
                </div>
            @endif

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
            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: @js(session('success')),
                    timer: 3000, showConfirmButton: false, toast: true, position: 'top-end',
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error', title: 'Gagal!', text: @js(session('error')),
                    timer: 4000, showConfirmButton: false, toast: true, position: 'top-end',
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
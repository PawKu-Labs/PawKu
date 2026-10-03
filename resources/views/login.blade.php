<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - PawKu</title>

    <!-- Google Font: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Auth Styling -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

</head>

<body>

    <!-- Toast Notification / SnackBar -->
    <div id="auth-toast" class="auth-toast" role="alert" aria-live="polite">
        <span id="toast-message"></span>
    </div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Back to Home Button -->
            <a href="{{ url('/') }}" class="auth-back" title="Kembali ke Beranda">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Beranda</span>
            </a>

            <!-- Header & Ilustrasi Kucing -->
            <div class="auth-header">
                <img src="{{ asset('assets/illustrations/cat-login.png') }}" alt="PawKu Mascot Cat"
                    class="auth-illustration"
                    onerror="this.onerror=null; this.src='{{ asset('assets/illustrations/cat1.png') }}';">

                <h1 class="auth-title">
                    Masuk ke akun <span class="brand-highlight">PawKu</span> anda
                </h1>
            </div>

            <!-- Form Login -->
            <form id="login-form" class="auth-form" novalidate onsubmit="handleEmailLogin(event)">
                @csrf

                <!-- Input Email -->
                <div class="form-group" id="group-email">
                    <div class="input-container" id="container-email">
                        <input type="email" id="email" name="email" class="form-input" placeholder="Email"
                            autocomplete="email" required oninput="validateEmailField(false)"
                            onblur="validateEmailField(true)">
                    </div>
                    <div id="email-error" class="field-error-msg">Email wajib diisi</div>
                </div>

                <!-- Input Password -->
                <div class="form-group" id="group-password">
                    <div class="input-container" id="container-password">
                        <input type="password" id="password" name="password" class="form-input has-toggle"
                            placeholder="Password" autocomplete="current-password" required
                            oninput="validatePasswordField(false)" onblur="validatePasswordField(true)">

                        <!-- Eye Toggle Password Button -->
                        <button type="button" id="btn-toggle-password" class="password-toggle-btn"
                            aria-label="Tampilkan atau sembunyikan password" onclick="togglePasswordVisibility()">
                            <svg id="icon-eye-off" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                            <svg id="icon-eye-on" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                                style="display: none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div id="password-error" class="field-error-msg">Password wajib diisi</div>
                </div>

                <!-- Tombol Masuk -->
                <button type="submit" id="btn-masuk" class="btn btn-primary">
                    <span id="btn-masuk-text">Masuk</span>
                    <span id="btn-masuk-spinner" class="spinner" style="display: none;"></span>
                </button>

                <!-- Tombol Daftar -->
                <a href="{{ url('/register') }}" class="btn btn-outline" id="btn-daftar">
                    Daftar
                </a>

                <!-- Divider atau -->
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <span class="auth-divider-text">atau</span>
                    <div class="auth-divider-line"></div>
                </div>

                <!-- Tombol Lanjutkan dengan Google -->
                <button type="button" id="btn-google" class="btn btn-google" onclick="handleGoogleLogin()">
                    <img src="{{ asset('assets/icons/google_logo.png') }}" alt="Google Logo"
                        onerror="this.onerror=null; this.src='https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg';">
                    <span id="btn-google-text">Lanjutkan dengan Google</span>
                    <span id="btn-google-spinner" class="spinner spinner-primary" style="display: none;"></span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Konfigurasi
        const SUPABASE_URL = "{{ rtrim(config('services.supabase.url'), '/') }}";
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const LOGIN_URL = "{{ route('login.submit') }}";

        // Regex validasi email
        const emailRegex = /^[\w\.\-\+]+@([\w\-]+\.)+[\w\-]{2,4}$/;

        // DOM Elements
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        const containerEmail = document.getElementById('container-email');
        const containerPassword = document.getElementById('container-password');
        const btnMasuk = document.getElementById('btn-masuk');
        const btnMasukText = document.getElementById('btn-masuk-text');
        const btnMasukSpinner = document.getElementById('btn-masuk-spinner');
        const btnGoogle = document.getElementById('btn-google');
        const btnGoogleText = document.getElementById('btn-google-text');
        const btnGoogleSpinner = document.getElementById('btn-google-spinner');
        const toast = document.getElementById('auth-toast');
        const toastMessage = document.getElementById('toast-message');

        let toastTimeout = null;

        // Tampilkan pesan toast
        function showToast(message, type = 'error') {
            clearTimeout(toastTimeout);
            toastMessage.textContent = message;
            toast.className = 'auth-toast show ' + (type === 'success' ? 'toast-success' : 'toast-error');
            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            document.getElementById('icon-eye-off').style.display = isPassword ? 'none' : 'block';
            document.getElementById('icon-eye-on').style.display = isPassword ? 'block' : 'none';
        }

        // Validasi Field Email
        function validateEmailField(showIfEmpty = true) {
            const val = emailInput.value.trim();
            if (val === '') {
                if (showIfEmpty) {
                    showFieldError(containerEmail, emailError, 'Email wajib diisi');
                    return false;
                }
                clearFieldError(containerEmail, emailError);
                return null;
            }
            if (!emailRegex.test(val)) {
                showFieldError(containerEmail, emailError, 'Format email tidak valid');
                return false;
            }
            clearFieldError(containerEmail, emailError);
            return true;
        }

        // Validasi Field Password
        function validatePasswordField(showIfEmpty = true) {
            const val = passwordInput.value;
            if (val === '') {
                if (showIfEmpty) {
                    showFieldError(containerPassword, passwordError, 'Password wajib diisi');
                    return false;
                }
                clearFieldError(containerPassword, passwordError);
                return null;
            }
            if (val.length < 8) {
                showFieldError(containerPassword, passwordError, 'Password minimal 8 karakter');
                return false;
            }
            clearFieldError(containerPassword, passwordError);
            return true;
        }

        function showFieldError(container, errorEl, message) {
            container.classList.add('has-error');
            errorEl.textContent = message;
            errorEl.classList.add('visible');
        }

        function clearFieldError(container, errorEl) {
            container.classList.remove('has-error');
            errorEl.classList.remove('visible');
        }

        // Login Email & Password (lewat AuthController Laravel)
        async function handleEmailLogin(event) {
            event.preventDefault();

            const isEmailValid = validateEmailField(true);
            const isPasswordValid = validatePasswordField(true);

            if (!isEmailValid || !isPasswordValid) {
                showToast('Harap periksa dan lengkapi email serta kata sandi Anda.', 'error');
                return;
            }

            btnMasuk.classList.add('loading');
            btnMasukText.style.display = 'none';
            btnMasukSpinner.style.display = 'inline-block';

            try {
                const res = await fetch(LOGIN_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        email: emailInput.value.trim(),
                        password: passwordInput.value,
                    }),
                });

                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (json.credential) {
                        showFieldError(containerEmail, emailError, 'Email atau password tidak cocok');
                        showFieldError(containerPassword, passwordError, 'Email atau password tidak cocok');
                    }
                    showToast(json.message || 'Gagal masuk. Coba lagi.', 'error');
                    return;
                }

                showToast(json.message, 'success');
                setTimeout(() => {
                    window.location.href = json.redirect;
                }, 800);

            } catch (err) {
                console.error('Login error:', err);
                showToast('Tidak dapat terhubung ke server.', 'error');
            } finally {
                btnMasuk.classList.remove('loading');
                btnMasukText.style.display = 'inline-block';
                btnMasukSpinner.style.display = 'none';
            }
        }

        // Login Google (redirect ke Supabase OAuth, lalu kembali ke /auth/callback)
        function handleGoogleLogin() {
            btnGoogle.classList.add('loading');
            btnGoogleText.style.display = 'none';
            btnGoogleSpinner.style.display = 'inline-block';

            const redirectTo = encodeURIComponent(window.location.origin + '/auth/callback');
            window.location.href = `${SUPABASE_URL}/auth/v1/authorize?provider=google&redirect_to=${redirectTo}`;
        }

        // Reset tombol Google jika user menekan tombol "back" dari halaman Google
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                btnGoogle.classList.remove('loading');
                btnGoogleText.style.display = 'inline-block';
                btnGoogleSpinner.style.display = 'none';
            }
        });

        // Tampilkan pesan error dari redirect URL (?error=...) atau Flash session
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const errorParam = urlParams.get('error');
            if (errorParam) {
                showToast(errorParam, 'error');
            }

            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
        });
    </script>
</body>

</html>
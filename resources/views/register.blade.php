<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - PawKu</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <!-- Google Font: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Auth Styling -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <!-- Supabase JS Client v2 -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <style>
        .password-rules {
            background-color: #F7F7FA;
            border-radius: 16px;
            padding: 14px 18px;
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            border: 1px solid #E0E0E0;
        }
        .rule-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6B6B6B;
            transition: color 0.2s ease;
        }
        .rule-item.met {
            color: #2E9E6B;
            font-weight: 500;
        }
        .rule-icon {
            width: 14px;
            height: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div id="auth-toast" class="auth-toast" role="alert" aria-live="polite">
        <span id="toast-message"></span>
    </div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Back to Login Button -->
            <a href="{{ route('login') }}" class="auth-back" title="Kembali ke Halaman Masuk">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Masuk</span>
            </a>

            <!-- Header -->
            <div class="auth-header">
                <img src="{{ asset('assets/illustrations/cat-login.png') }}" 
                     alt="PawKu Mascot Cat" 
                     class="auth-illustration"
                     onerror="this.onerror=null; this.src='{{ asset('assets/illustrations/cat1.png') }}';">
                
                <h1 class="auth-title">
                    Daftar akun <span class="brand-highlight">PawKu</span> baru
                </h1>
            </div>

            <!-- Form Register -->
            <form id="register-form" class="auth-form" novalidate onsubmit="handleRegister(event)">
                @csrf

                <!-- Nama Lengkap / Username -->
                <div class="form-group">
                    <div class="input-container" id="container-name">
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               placeholder="Nama Lengkap" 
                               required
                               oninput="validateNameField()">
                    </div>
                    <div id="name-error" class="field-error-msg">Nama lengkap wajib diisi</div>
                </div>

                <!-- Input Email -->
                <div class="form-group">
                    <div class="input-container" id="container-email">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input" 
                               placeholder="Email" 
                               required
                               oninput="validateEmailField()">
                    </div>
                    <div id="email-error" class="field-error-msg">Email wajib diisi</div>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <div class="input-container" id="container-password">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input has-toggle" 
                               placeholder="Password" 
                               required
                               oninput="validatePasswordField()">

                        <button type="button" 
                                class="password-toggle-btn" 
                                onclick="togglePasswordVisibility('password', this)">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <div id="password-error" class="field-error-msg">Password wajib diisi</div>

                    <!-- Checklist Aturan Password (sesuai Flutter) -->
                    <div class="password-rules" id="password-rules">
                        <div class="rule-item" id="rule-len">
                            <span class="rule-icon">•</span> Minimal 8 karakter
                        </div>
                        <div class="rule-item" id="rule-lower">
                            <span class="rule-icon">•</span> Minimal 1 huruf kecil (a–z)
                        </div>
                        <div class="rule-item" id="rule-upper">
                            <span class="rule-icon">•</span> Minimal 1 huruf besar (A–Z)
                        </div>
                        <div class="rule-item" id="rule-special">
                            <span class="rule-icon">•</span> Minimal 1 simbol khusus (!@#$)
                        </div>
                    </div>
                </div>

                <!-- Input Konfirmasi Password -->
                <div class="form-group">
                    <div class="input-container" id="container-confirm">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-input has-toggle" 
                               placeholder="Konfirmasi Password" 
                               required
                               oninput="validateConfirmField()">

                        <button type="button" 
                                class="password-toggle-btn" 
                                onclick="togglePasswordVisibility('confirm_password', this)">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <div id="confirm-error" class="field-error-msg">Konfirmasi password wajib diisi</div>
                </div>

                <!-- Tombol Buat Akun -->
                <button type="submit" id="btn-submit" class="btn btn-primary">
                    <span id="btn-submit-text">Daftar Sekarang</span>
                    <span id="btn-submit-spinner" class="spinner" style="display: none;"></span>
                </button>

                <!-- Tombol Kembali ke Login -->
                <a href="{{ route('login') }}" class="btn btn-outline">
                    Sudah Punya Akun? Masuk
                </a>
            </form>
        </div>
    </div>

    <script>
        const SUPABASE_URL = "{{ config('services.supabase.url', env('SUPABASE_URL', 'https://qvaflmgyezrjotbkcild.supabase.co')) }}";
        const SUPABASE_ANON_KEY = "{{ config('services.supabase.anon_key', env('SUPABASE_ANON_KEY', 'sb_publishable_eRBjvQXnmDt3rhxeiQYi5g_ajVrKEDc')) }}";
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const emailRegex = /^[\w\.\-\+]+@([\w\-]+\.)+[\w\-]{2,4}$/;

        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');

        const toast = document.getElementById('auth-toast');
        const toastMessage = document.getElementById('toast-message');
        let toastTimeout = null;

        function showToast(message, type = 'error') {
            clearTimeout(toastTimeout);
            toastMessage.textContent = message;
            toast.className = 'auth-toast show ' + (type === 'success' ? 'toast-success' : 'toast-error');
            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
        }

        function validateNameField() {
            const val = nameInput.value.trim();
            const container = document.getElementById('container-name');
            const error = document.getElementById('name-error');
            if (!val) {
                container.classList.add('has-error');
                error.textContent = 'Nama lengkap wajib diisi';
                error.classList.add('visible');
                return false;
            }
            container.classList.remove('has-error');
            error.classList.remove('visible');
            return true;
        }

        function validateEmailField() {
            const val = emailInput.value.trim();
            const container = document.getElementById('container-email');
            const error = document.getElementById('email-error');
            if (!val) {
                container.classList.add('has-error');
                error.textContent = 'Email wajib diisi';
                error.classList.add('visible');
                return false;
            }
            if (!emailRegex.test(val)) {
                container.classList.add('has-error');
                error.textContent = 'Format email tidak valid';
                error.classList.add('visible');
                return false;
            }
            container.classList.remove('has-error');
            error.classList.remove('visible');
            return true;
        }

        function validatePasswordField() {
            const val = passwordInput.value;
            const container = document.getElementById('container-password');
            const error = document.getElementById('password-error');

            const hasLen = val.length >= 8;
            const hasLower = /[a-z]/.test(val);
            const hasUpper = /[A-Z]/.test(val);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>\-_+=\[\]\\\/~`]/.test(val);

            document.getElementById('rule-len').classList.toggle('met', hasLen);
            document.getElementById('rule-lower').classList.toggle('met', hasLower);
            document.getElementById('rule-upper').classList.toggle('met', hasUpper);
            document.getElementById('rule-special').classList.toggle('met', hasSpecial);

            if (!hasLen || !hasLower || !hasUpper || !hasSpecial) {
                container.classList.add('has-error');
                error.textContent = 'Kata sandi belum memenuhi semua kriteria keamanan';
                error.classList.add('visible');
                return false;
            }

            container.classList.remove('has-error');
            error.classList.remove('visible');
            return true;
        }

        function validateConfirmField() {
            const val = confirmInput.value;
            const pwd = passwordInput.value;
            const container = document.getElementById('container-confirm');
            const error = document.getElementById('confirm-error');

            if (!val) {
                container.classList.add('has-error');
                error.textContent = 'Konfirmasi password wajib diisi';
                error.classList.add('visible');
                return false;
            }
            if (val !== pwd) {
                container.classList.add('has-error');
                error.textContent = 'Konfirmasi password tidak cocok';
                error.classList.add('visible');
                return false;
            }

            container.classList.remove('has-error');
            error.classList.remove('visible');
            return true;
        }

        async function handleRegister(event) {
            event.preventDefault();

            const isNameValid = validateNameField();
            const isEmailValid = validateEmailField();
            const isPasswordValid = validatePasswordField();
            const isConfirmValid = validateConfirmField();

            if (!isNameValid || !isEmailValid || !isPasswordValid || !isConfirmValid) {
                showToast('Harap lengkapi semua kolom dengan data yang valid.', 'error');
                return;
            }

            const btn = document.getElementById('btn-submit');
            const text = document.getElementById('btn-submit-text');
            const spinner = document.getElementById('btn-submit-spinner');

            btn.classList.add('loading');
            text.style.display = 'none';
            spinner.style.display = 'inline-block';

            try {
                const { data, error } = await supabase.auth.signUp({
                    email: emailInput.value.trim(),
                    password: passwordInput.value,
                    options: {
                        data: {
                            full_name: nameInput.value.trim(),
                            name: nameInput.value.trim()
                        }
                    }
                });

                if (error) throw error;

                showToast('Pendaftaran berhasil! Silakan cek email untuk verifikasi atau masuk.', 'success');
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 2000);
            } catch (err) {
                console.error("Sign up error:", err);
                showToast(err.message || 'Gagal mendaftar. Silakan coba lagi.', 'error');
            } finally {
                btn.classList.remove('loading');
                text.style.display = 'inline-block';
                spinner.style.display = 'none';
            }
        }
    </script>
</body>
</html>

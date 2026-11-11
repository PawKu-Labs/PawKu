<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menyiapkan Akun... - PawKu</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <!-- Google Font: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F7F7FA;
            color: #1A1A1A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .loading-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 40px 32px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .brand-icon-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 50%;
            z-index: 2;
        }

        .spinner-ring {
            position: absolute;
            inset: 0;
            border: 3px solid rgba(245, 158, 61, 0.15);
            border-top-color: #F59E3D;
            border-radius: 50%;
            animation: spin 0.9s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 8px;
        }

        .loading-subtitle {
            font-size: 0.875rem;
            color: #6B6B6B;
            line-height: 1.5;
        }

        .dots::after {
            content: '';
            display: inline-block;
            animation: ellipsis 1.5s infinite;
            text-align: left;
            width: 1em;
        }

        @keyframes ellipsis {
            0% {
                content: '';
            }

            25% {
                content: '.';
            }

            50% {
                content: '..';
            }

            75% {
                content: '...';
            }
        }
    </style>
</head>

<body>
    <div class="loading-card">
        <div class="brand-icon-wrapper">
            <div class="spinner-ring"></div>
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu Logo" class="brand-icon">
        </div>
        <h2 class="loading-title">Menghubungkan Akun<span class="dots"></span></h2>
        <p class="loading-subtitle">Mohon tunggu sebentar, kami sedang menyiapkan sesi Anda.</p>
    </div>

    <script>
        (async () => {
            const params = new URLSearchParams(window.location.hash.substring(1));
            const accessToken = params.get('access_token');
            const refreshToken = params.get('refresh_token');
            const err = params.get('error_description')
                || new URLSearchParams(window.location.search).get('error_description');

            const fail = (msg) => {
                window.location.replace("{{ route('login') }}?error=" + encodeURIComponent(msg));
            };

            if (err || !accessToken) return fail(err || 'Login Google gagal.');

            try {
                const res = await fetch("{{ route('auth.session') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ access_token: accessToken, refresh_token: refreshToken }),
                });

                const json = await res.json();
                if (!res.ok) return fail(json.message || 'Login Google gagal.');

                // Gunakan replace() agar halaman callback tidak bisa di-back
                window.location.replace(json.redirect);
            } catch (e) {
                fail('Tidak dapat terhubung ke server.');
            }
        })();
    </script>
</body>

</html>
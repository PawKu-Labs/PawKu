<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - PawKu</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- PawKu CSS -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            background: #FFF8EA;
        }

        .error-paw {
            font-size: 80px;
            margin-bottom: 8px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        .error-code {
            font-size: 100px;
            font-weight: 900;
            color: #F59E3D;
            line-height: 1;
            margin: 0;
            letter-spacing: -4px;
        }

        .error-title {
            font-size: 26px;
            font-weight: 800;
            color: #5A3022;
            margin: 16px 0 10px;
        }

        .error-desc {
            font-size: 15px;
            color: #80665C;
            max-width: 420px;
            line-height: 1.6;
            margin: 0 auto 32px;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 24px;
            border-radius: 50px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s;
        }

        .error-btn-primary {
            background: #F59E3D;
            color: #5A3022;
        }

        .error-btn-primary:hover {
            background: #E9822B;
            color: #fff;
        }

        .error-btn-outline {
            background: transparent;
            color: #5A3022;
            border: 2px solid #F59E3D;
        }

        .error-btn-outline:hover {
            background: #FFF0D6;
        }

        .error-pawprints {
            margin-top: 48px;
            font-size: 22px;
            opacity: .35;
            letter-spacing: 6px;
        }
    </style>
</head>

<body>

    <div class="error-page">

        <div class="error-paw">🐾</div>

        <p class="error-code">404</p>

        <h1 class="error-title">Aduh, halaman ini nyasar!</h1>

        <p class="error-desc">
            Sepertinya halaman yang kamu cari sudah pindah, dihapus,
            atau mungkin sedang ikut jalan-jalan sama kucingnya. 🐱
        </p>

        <div class="error-actions">
            <a href="{{ url('/') }}" class="error-btn error-btn-primary">
                🏠 Kembali ke Beranda
            </a>
            <a href="{{ route('petshops.index') }}" class="error-btn error-btn-outline">
                🏪 Lihat Petshop
            </a>
        </div>

        <div class="error-pawprints">🐾 🐾 🐾 🐾 🐾</div>

    </div>

</body>

</html>


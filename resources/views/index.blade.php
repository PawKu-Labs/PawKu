<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawKu — Smart Cat Care & Feline Health Platform</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <!-- Google Font: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- PawKu CSS -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <!-- UI Refinement Styles (Kompatibel dengan index.css) -->
    <style>
        .icon-svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            flex-shrink: 0;
        }

        .icon-svg-lg {
            width: 24px;
            height: 24px;
        }

        /* Hero Badge & Buttons */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .btn-primary,
        .btn-outline,
        .btn-white {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Dashboard UI Mockup Polish */
        .pet-dashboard {
            box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .notification {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 7px;
            height: 7px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 1.5px solid #ffffff;
        }

        .pet-image {
            overflow: hidden;
            padding: 0 !important;
        }

        .pet-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .health-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            display: inline-block;
        }

        .quick-icon,
        .schedule-icon,
        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Editorial Cat Care Tips Cards */
        .tip-card {
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .tip-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.1);
        }

        .tip-image {
            padding: 0 !important;
            overflow: hidden;
            height: 200px;
            position: relative;
        }

        .tip-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .tip-card:hover .tip-image img {
            transform: scale(1.04);
        }

        .tip-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .tip-read-time {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }

        /* CTA & Footer Logo */
        .cta-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .footer-logo img {
            height: 36px;
            width: auto;
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================== -->
    <header class="navbar">
        <div class="nav-container">

            <!-- LOGO -->
            <a href="/" class="logo">
                <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu Logo" style="width: 68px;">
            </a>

            <!-- MENU -->
            <nav class="nav-menu">
                <a href="#" class="active">Home</a>
                <a href="#features">Features</a>
                <a href="#tips">Pet Care</a>
                <a href="/kalkulator">Cek Status Berat Badan</a>
                <a href="{{ route('petshops.index') }}">Petshop</a>
                <a href="#about">About</a>
            </nav>

            <!-- ACTION -->
            <div class="nav-actions">
                <a href="/login" class="btn-primary">
                    <span>Sign In</span>
                    <svg class="icon-svg" style="width: 16px; height: 16px;" viewBox="0 0 24 24">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </a>
            </div>

        </div>
    </header>


    <!-- =========================
         HERO
    ========================== -->
    <main>
        <section class="hero">
            <div class="hero-container">

                <!-- HERO CONTENT -->
                <div class="hero-content">

                    <div class="hero-badge">
                        <svg class="icon-svg" style="width: 16px; height: 16px;" viewBox="0 0 24 24">
                            <circle cx="11" cy="4" r="2"></circle>
                            <circle cx="18" cy="8" r="2"></circle>
                            <circle cx="20" cy="16" r="2"></circle>
                            <path
                                d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z">
                            </path>
                        </svg>
                        <span>Trusted Feline Health & Cat Care Platform</span>
                    </div>

                    <h1 class="hero-title">
                        Better Care.
                        <br>
                        <span>Happier Cats.</span>
                    </h1>

                    <p class="hero-description">
                        PawKu membantu pemilik kucing memantau riwayat kesehatan, jadwal vaksinasi F3/F4, porsi nutrisi
                        harian, hingga menemukan mitra petshop dan klinik kucing terpercaya dalam satu platform.
                    </p>

                    <div class="hero-buttons">
                        <a href="/login" class="btn-primary">
                            <span>Start Caring</span>
                            <svg class="icon-svg" style="width: 18px; height: 18px;" viewBox="0 0 24 24">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </a>

                        <a href="#features" class="btn-outline">
                            Explore Features
                        </a>
                    </div>

                    <!-- STATS -->
                    <div class="hero-stats">
                        <div class="stat-item">
                            <h3>1,200+</h3>
                            <p>Cat Parents</p>
                        </div>

                        <div class="stat-divider"></div>

                        <div class="stat-item">
                            <h3>4.9/5</h3>
                            <p>Verified Rating</p>
                        </div>

                        <div class="stat-divider"></div>

                        <div class="stat-item">
                            <h3>24/7</h3>
                            <p>Feline Monitoring</p>
                        </div>
                    </div>

                </div>


                <!-- HERO VISUAL (APP PREVIEW CARD) -->
                <div class="hero-visual">

                    <div class="hero-decoration-one"></div>
                    <div class="hero-decoration-two"></div>

                    <div class="pet-dashboard">

                        <!-- DASHBOARD HEADER -->
                        <div class="dashboard-header">
                            <div class="greeting">
                                <small>Selamat Pagi,</small>
                                <strong>Nabil</strong>
                            </div>

                            <div class="notification" aria-label="Notifications">
                                <svg class="icon-svg" viewBox="0 0 24 24">
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                                </svg>
                                <span class="notification-dot"></span>
                            </div>
                        </div>

                        <!-- CAT PROFILE -->
                        <div class="pet-profile">
                            <div class="pet-image">
                                <img src="https://images.unsplash.com/photo-1573865526739-10659fec78a5?auto=format&fit=crop&w=240&q=80"
                                    alt="Mochi - British Shorthair Cat">
                            </div>

                            <div class="pet-info">
                                <h3>Mochi</h3>
                                <p>British Shorthair · 2 Tahun</p>
                                <span class="health-status">
                                    <span class="status-dot"></span>
                                    Optimal Health
                                </span>
                            </div>
                        </div>

                        <!-- QUICK ACCESS -->
                        <div class="quick-section">
                            <p class="section-label">Quick Access</p>

                            <div class="quick-grid">

                                <div class="quick-item">
                                    <div class="quick-icon">
                                        <svg class="icon-svg" viewBox="0 0 24 24">
                                            <path
                                                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                                            </path>
                                            <path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"></path>
                                        </svg>
                                    </div>
                                    <p>Health</p>
                                </div>

                                <div class="quick-item">
                                    <div class="quick-icon">
                                        <svg class="icon-svg" viewBox="0 0 24 24">
                                            <path d="m18 2 4 4"></path>
                                            <path d="m17 7 3-3"></path>
                                            <path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5">
                                            </path>
                                            <path d="m9 11 4 4"></path>
                                            <path d="m5 19-3 3"></path>
                                            <path d="m14 4 6 6"></path>
                                        </svg>
                                    </div>
                                    <p>Vaccine</p>
                                </div>

                                <div class="quick-item">
                                    <div class="quick-icon">
                                        <svg class="icon-svg" viewBox="0 0 24 24">
                                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                                            <path d="M7 2v20"></path>
                                            <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path>
                                        </svg>
                                    </div>
                                    <p>Nutrition</p>
                                </div>

                                <div class="quick-item">
                                    <div class="quick-icon">
                                        <svg class="icon-svg" viewBox="0 0 24 24">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                            <line x1="16" x2="16" y1="2" y2="6"></line>
                                            <line x1="8" x2="8" y1="2" y2="6"></line>
                                            <line x1="3" x2="21" y1="10" y2="10"></line>
                                        </svg>
                                    </div>
                                    <p>Schedule</p>
                                </div>

                            </div>
                        </div>

                        <!-- UPCOMING -->
                        <div class="upcoming">
                            <div class="upcoming-header">
                                <p class="section-label">Upcoming Reminder</p>
                                <a href="/login">See all</a>
                            </div>

                            <div class="schedule-card">
                                <div class="schedule-icon">
                                    <svg class="icon-svg" viewBox="0 0 24 24">
                                        <path d="m18 2 4 4"></path>
                                        <path d="m17 7 3-3"></path>
                                        <path d="M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5"></path>
                                        <path d="m9 11 4 4"></path>
                                        <path d="m5 19-3 3"></path>
                                    </svg>
                                </div>

                                <div class="schedule-info">
                                    <strong>Vaksin F3 (Tricat) Booster</strong>
                                    <span>Besok · 10:00 WIB</span>
                                </div>

                                <span class="schedule-view">Detail</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>


        <!-- =========================
             FEATURES
        ========================== -->
        <section class="features" id="features">
            <div class="container">

                <div class="section-heading">
                    <span class="section-label-top">EVERYTHING IN ONE PLACE</span>
                    <h2>Comprehensive care tailored for cats</h2>
                    <p>
                        Dirancang khusus untuk kebutuhan kucing indoor maupun outdoor—mulai dari catatan medis, jadwal
                        vaksinasi, hingga koneksi dengan mitra petshop.
                    </p>
                </div>

                <div class="feature-grid">

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="icon-svg icon-svg-lg" viewBox="0 0 24 24">
                                <path
                                    d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3">
                                </path>
                                <path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"></path>
                                <circle cx="20" cy="10" r="2"></circle>
                            </svg>
                        </div>
                        <h3>Feline Medical Records</h3>
                        <p>
                            Catat riwayat berat badan, status steril, alergi pakan, serta hasil pemeriksaan dokter hewan
                            secara terstruktur dan mudah diakses.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="icon-svg icon-svg-lg" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3>F3, F4 & Rabies Tracking</h3>
                        <p>
                            Pantau riwayat imunisasi inti kucing dan terima pengingat otomatis sebelum jadwal vaksinasi
                            tahunan maupun obat cacing tiba.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="icon-svg icon-svg-lg" viewBox="0 0 24 24">
                                <path
                                    d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z">
                                </path>
                                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                            </svg>
                        </div>
                        <h3>Wet & Dry Nutrition Guide</h3>
                        <p>
                            Rekomendasi takaran kalori dan keseimbangan pakan basah maupun kering sesuai usia kitten,
                            adult, hingga kucing senior.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="icon-svg icon-svg-lg" viewBox="0 0 24 24">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                                <path d="m9 16 2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3>Grooming & Parasite Care</h3>
                        <p>
                            Atur rutinitas mandi anti-jamur, potong kuku, pembersihan telinga, serta jadwal tetes
                            anti-kutu dalam satu kalender rapi.
                        </p>
                    </div>

                </div>

            </div>
        </section>


        <!-- =========================
             CAT CARE TIPS
        ========================== -->
        <section class="tips" id="tips">
            <div class="container">

                <div class="tips-header">
                    <div>
                        <span class="section-label-top">FELINE CARE JOURNAL</span>
                        <h2>Insights for healthier, happier cats</h2>
                    </div>

                    <a href="{{ route('petshops.index') }}" class="view-all">
                        Explore Petshops &rarr;
                    </a>
                </div>

                <div class="tips-grid">

                    <article class="tip-card">
                        <div class="tip-image">
                            <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=700&q=80"
                                alt="Perilaku dan kesehatan kucing">
                        </div>

                        <div class="tip-content">
                            <div class="tip-meta">
                                <span class="tip-category">HEALTH</span>
                                <span class="tip-read-time">4 min read</span>
                            </div>

                            <h3>5 Subtle Signs Your Cat Is Hiding Discomfort</h3>

                            <p>
                                Kucing cenderung menyembunyikan rasa sakit. Kenali perubahan kebiasaan litter box, nafsu
                                makan, dan pola bersembunyi sejak dini.
                            </p>
                        </div>
                    </article>

                    <article class="tip-card">
                        <div class="tip-image">
                            <img src="https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=700&q=80"
                                alt="Nutrisi dan hidrasi kucing">
                        </div>

                        <div class="tip-content">
                            <div class="tip-meta">
                                <span class="tip-category">NUTRITION</span>
                                <span class="tip-read-time">5 min read</span>
                            </div>

                            <h3>Balancing Wet & Dry Food for Urinary Health</h3>

                            <p>
                                Mengatur kombinasi wet food dan asupan air harian untuk menjaga fungsi ginjal serta
                                mencegah risiko gangguan saluran kemih kucing.
                            </p>
                        </div>
                    </article>

                    <article class="tip-card">
                        <div class="tip-image">
                            <img src="https://images.unsplash.com/photo-1513360371669-4adf3dd7dff8?auto=format&fit=crop&w=700&q=80"
                                alt="Perawatan bulu dan grooming kucing">
                        </div>

                        <div class="tip-content">
                            <div class="tip-meta">
                                <span class="tip-category">GROOMING</span>
                                <span class="tip-read-time">3 min read</span>
                            </div>

                            <h3>Coat Brushing Routine to Minimize Hairballs</h3>

                            <p>
                                Teknik dan frekuensi menyisir bulu untuk kucing berbulu pendek maupun panjang agar
                                terhindar dari bulu kusut dan hairball.
                            </p>
                        </div>
                    </article>

                </div>

            </div>
        </section>


        <!-- =========================
             CTA
        ========================== -->
        <section class="cta-section" id="about">
            <div class="container">
                <div class="cta">

                    <div class="cta-circle-one"></div>
                    <div class="cta-circle-two"></div>

                    <div class="cta-content">
                        <div class="cta-icon">
                            <svg class="icon-svg icon-svg-lg" viewBox="0 0 24 24">
                                <circle cx="11" cy="4" r="2"></circle>
                                <circle cx="18" cy="8" r="2"></circle>
                                <circle cx="20" cy="16" r="2"></circle>
                                <path
                                    d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z">
                                </path>
                            </svg>
                        </div>

                        <h2>Give your cat the care they deserve.</h2>

                        <p>
                            Mulai kelola rekam kesehatan, jadwal vaksinasi F3/F4, dan kebutuhan harian kucing kesayangan
                            Anda secara lebih teratur bersama PawKu.
                        </p>

                        <a href="/login" class="btn-white">
                            <span>Create Account</span>
                            <svg class="icon-svg" style="width: 18px; height: 18px;" viewBox="0 0 24 24">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </section>

    </main>


    <!-- =========================
         FOOTER
    ========================== -->
    <footer class="footer">
        <div class="footer-container">

            <div class="footer-logo">
                <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu Logo">
                <span>PawKu</span>
            </div>

            <p class="footer-copy">
                &copy; {{ date('Y') }} PawKu Feline Care Platform. All rights reserved.
            </p>

            <div class="footer-links">
                <a href="#features">Features</a>
                <a href="{{ route('petshops.index') }}">Petshop</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>

        </div>
    </footer>

</body>

</html>
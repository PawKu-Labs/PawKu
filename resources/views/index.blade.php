<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawKu - Pet Care</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- PawKu CSS -->
    <link rel="stylesheet" href="{{ asset('./css/index.css') }}">

</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <header class="navbar">

        <div class="nav-container">


            <!-- LOGO -->

            <a href="/" class="logo">

                <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="logo-pawku" style="width: 70px;">

            </a>


            <!-- MENU -->

            <nav class="nav-menu">

                <a href="/" class="active">
                    Home
                </a>

                <a href="#features">
                    Features
                </a>

                <a href="#tips">
                    Pet Care
                </a>

                <a href="{{ route('petshops.index') }}">
                    Petshop
                </a>

                <a href="#about">
                    About
                </a>

            </nav>


            <!-- ACTION -->

            <div class="nav-actions">

                <a href="/login" class="btn-primary">
                    Login
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
                        <span>🐾</span>

                        <span>
                            Your pet's best companion
                        </span>
                    </div>


                    <h1 class="hero-title">

                        Better Care.
                        <br>

                        <span>
                            Happier Pets.
                        </span>

                    </h1>


                    <p class="hero-description">

                        PawKu membantu kamu mengelola kesehatan,
                        jadwal, nutrisi, dan kebutuhan hewan
                        peliharaan dalam satu tempat.

                    </p>


                    <div class="hero-buttons">

                        <a href="#" class="btn-primary">
                            Start Caring 🐾
                        </a>

                        <a href="#features" class="btn-outline">
                            Explore Features
                        </a>

                    </div>


                    <!-- STATS -->

                    <div class="hero-stats">

                        <div class="stat-item">

                            <h3>
                                1K+
                            </h3>

                            <p>
                                Pet Parents
                            </p>

                        </div>


                        <div class="stat-divider"></div>


                        <div class="stat-item">

                            <h3>
                                4.9/5
                            </h3>

                            <p>
                                User Rating
                            </p>

                        </div>


                        <div class="stat-divider"></div>


                        <div class="stat-item">

                            <h3>
                                24/7
                            </h3>

                            <p>
                                Pet Care
                            </p>

                        </div>

                    </div>

                </div>



                <!-- HERO VISUAL -->

                <div class="hero-visual">


                    <div class="hero-decoration-one"></div>

                    <div class="hero-decoration-two"></div>


                    <div class="pet-dashboard">


                        <!-- DASHBOARD HEADER -->

                        <div class="dashboard-header">

                            <div class="greeting">

                                <small>
                                    Good Morning 👋
                                </small>

                                <strong>
                                    Nabil
                                </strong>

                            </div>


                            <div class="notification">
                                🔔
                            </div>

                        </div>



                        <!-- PET PROFILE -->

                        <div class="pet-profile">

                            <div class="pet-image">
                                🐶
                            </div>


                            <div class="pet-info">

                                <h3>
                                    Milo
                                </h3>

                                <p>
                                    Golden Retriever
                                </p>

                                <span class="health-status">
                                    💚 Healthy
                                </span>

                            </div>

                        </div>



                        <!-- QUICK ACCESS -->

                        <div class="quick-section">

                            <p class="section-label">
                                Quick Access
                            </p>


                            <div class="quick-grid">


                                <div class="quick-item">

                                    <div class="quick-icon">
                                        🩺
                                    </div>

                                    <p>
                                        Health
                                    </p>

                                </div>


                                <div class="quick-item">

                                    <div class="quick-icon">
                                        💉
                                    </div>

                                    <p>
                                        Vaccine
                                    </p>

                                </div>


                                <div class="quick-item">

                                    <div class="quick-icon">
                                        🍖
                                    </div>

                                    <p>
                                        Nutrition
                                    </p>

                                </div>


                                <div class="quick-item">

                                    <div class="quick-icon">
                                        📅
                                    </div>

                                    <p>
                                        Schedule
                                    </p>

                                </div>


                            </div>

                        </div>



                        <!-- UPCOMING -->

                        <div class="upcoming">


                            <div class="upcoming-header">

                                <p class="section-label">
                                    Upcoming
                                </p>

                                <a href="#">
                                    See all
                                </a>

                            </div>


                            <div class="schedule-card">


                                <div class="schedule-icon">
                                    💉
                                </div>


                                <div class="schedule-info">

                                    <strong>
                                        Rabies Vaccine
                                    </strong>

                                    <span>
                                        Tomorrow · 10:00 AM
                                    </span>

                                </div>


                                <span class="schedule-view">
                                    View
                                </span>

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

                    <span class="section-label-top">
                        EVERYTHING IN ONE PLACE
                    </span>

                    <h2>
                        Everything your pet needs
                    </h2>

                    <p>
                        Kelola berbagai kebutuhan hewan
                        peliharaan dengan lebih mudah
                        bersama PawKu.
                    </p>

                </div>



                <div class="feature-grid">


                    <div class="feature-card">

                        <div class="feature-icon">
                            🩺
                        </div>

                        <h3>
                            Pet Health
                        </h3>

                        <p>
                            Catat dan pantau kondisi kesehatan
                            hewan peliharaan.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-icon">
                            💉
                        </div>

                        <h3>
                            Vaccination
                        </h3>

                        <p>
                            Jangan lewatkan jadwal vaksinasi
                            penting untuk hewan kamu.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-icon">
                            🍖
                        </div>

                        <h3>
                            Nutrition
                        </h3>

                        <p>
                            Temukan informasi makanan dan
                            nutrisi yang sesuai.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-icon">
                            📅
                        </div>

                        <h3>
                            Pet Schedule
                        </h3>

                        <p>
                            Atur jadwal grooming, vaksin,
                            makan, dan perawatan.
                        </p>

                    </div>


                </div>

            </div>

        </section>



        <!-- =========================
             PET CARE TIPS
        ========================== -->

        <section class="tips" id="tips">

            <div class="container">


                <div class="tips-header">

                    <div>

                        <span class="section-label-top">
                            PET CARE
                        </span>

                        <h2>
                            Tips for happier pets 🐾
                        </h2>

                    </div>


                    <a href="#" class="view-all">
                        View all →
                    </a>

                </div>



                <div class="tips-grid">


                    <article class="tip-card">

                        <div class="tip-image">
                            🐕
                        </div>

                        <div class="tip-content">

                            <span class="tip-category">
                                HEALTH
                            </span>

                            <h3>
                                5 Signs Your Pet Needs More Attention
                            </h3>

                            <p>
                                Kenali beberapa perubahan perilaku
                                yang perlu diperhatikan.
                            </p>

                        </div>

                    </article>



                    <article class="tip-card">

                        <div class="tip-image">
                            🍖
                        </div>

                        <div class="tip-content">

                            <span class="tip-category">
                                NUTRITION
                            </span>

                            <h3>
                                Choosing the Right Food
                            </h3>

                            <p>
                                Panduan sederhana memilih makanan
                                yang sesuai untuk hewan.
                            </p>

                        </div>

                    </article>



                    <article class="tip-card">

                        <div class="tip-image">
                            🛁
                        </div>

                        <div class="tip-content">

                            <span class="tip-category">
                                CARE
                            </span>

                            <h3>
                                How Often Should You Groom?
                            </h3>

                            <p>
                                Tips menjaga kebersihan dan
                                kenyamanan hewan peliharaan.
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
                            🐾
                        </div>

                        <h2>
                            Give your pet the care they deserve.
                        </h2>

                        <p>
                            Mulai perjalanan perawatan hewan
                            yang lebih teratur, mudah, dan
                            menyenangkan bersama PawKu.
                        </p>

                        <a href="#" class="btn-white">
                            Get Started →
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

                <div class="footer-logo-icon">
                    🐾
                </div>

                <span>
                    PawKu
                </span>

            </div>


            <p class="footer-copy">
                © {{ date('Y') }} PawKu. All rights reserved.
            </p>


            <div class="footer-links">

                <a href="#">
                    Privacy
                </a>

                <a href="#">
                    Terms
                </a>

            </div>


        </div>

    </footer>


</body>

</html>
```
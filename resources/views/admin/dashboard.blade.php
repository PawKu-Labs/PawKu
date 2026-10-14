<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - PawKu</title>

    <!-- Google Fonts: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #1677ff;
            --primary-hover: #0958d9;
            --primary-light: #e6f4ff;
            --primary-dark: #10239e;
            --brand-green: #2E9E6B;
            --brand-green-light: #e6f7ef;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #f0f2f5;
            --border-subtle: #e2e8f0;
            --text-heading: #1e293b;
            --text-body: #475569;
            --text-muted: #8c8c8c;
            --text-placeholder: #bfbfbf;
            --sidebar-width: 260px;
            --header-height: 68px;
            --success: #52c41a;
            --success-bg: #f6ffed;
            --success-border: #b7eb8f;
            --warning: #faad14;
            --warning-bg: #fffbe6;
            --danger: #ff4d4f;
            --danger-bg: #fff2f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px 0 rgba(0, 0, 0, 0.02);
            --shadow-card: 0 2px 8px rgba(0, 0, 0, 0.04);
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        /* Sidebar Styling (Mantis Style) */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-subtle);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid var(--border-color);
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.3px;
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-light);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 2px;
            align-self: flex-start;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .menu-group-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0 12px 6px;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--text-body);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .menu-item a:hover {
            background-color: #f1f5f9;
            color: var(--primary);
        }

        .menu-item.active a {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .menu-icon {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            color: currentColor;
        }

        .menu-badge {
            margin-left: auto;
            background: #f1f5f9;
            color: var(--text-body);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
        }

        .user-mini-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            background: #f8fafc;
            border: 1px solid var(--border-color);
        }

        .user-avatar-mini {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            font-size: 14px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .user-avatar-mini img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-mini-meta {
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .user-mini-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-heading);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-mini-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Main Content Layout */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header Navbar */
        .topbar {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            position: sticky;
            top: 0;
            z-index: 99;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 8px 14px;
            gap: 10px;
            width: 320px;
            transition: all 0.2s ease;
        }

        .search-box:focus-within {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 119, 255, 0.12);
        }

        .search-input {
            border: none;
            background: transparent;
            font-size: 13px;
            color: var(--text-heading);
            outline: none;
            width: 100%;
        }

        .search-shortcut {
            font-size: 11px;
            color: var(--text-muted);
            background: #ffffff;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid var(--border-subtle);
            font-family: inherit;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .env-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--brand-green);
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--brand-green);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(46, 158, 107, 0.6);
            animation: pulse 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
        }

        @keyframes pulse {
            to {
                box-shadow: 0 0 0 8px rgba(46, 158, 107, 0);
            }
        }

        .btn-action-logout {
            background: #ffffff;
            border: 1px solid #fee2e2;
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-action-logout:hover {
            background: #fef2f2;
            border-color: #fca5a5;
        }

        /* Page Content Area */
        .content-body {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Breadcrumb & Title */
        .page-header {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: var(--text-body);
        }

        /* Session Verification Banner (Utama untuk Cek Sesi) */
        .session-banner {
            background: linear-gradient(135deg, #ffffff 0%, #f6faff 100%);
            border: 1px solid #d0e2ff;
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: relative;
            overflow: hidden;
        }

        .session-banner::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, var(--brand-green) 0%, var(--primary) 100%);
        }

        .banner-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .banner-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .banner-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #e6f7ef;
            color: var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .banner-subtitle {
            font-size: 12.5px;
            color: var(--text-body);
            margin-top: 2px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .session-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            padding-top: 4px;
        }

        .session-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .session-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .session-val {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-heading);
            word-break: break-all;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-role {
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        /* Mantis Metric Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .stat-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-blue {
            background: #e6f4ff;
            color: #1677ff;
        }

        .icon-green {
            background: #f6ffed;
            color: #52c41a;
        }

        .icon-gold {
            background: #fffbe6;
            color: #faad14;
        }

        .icon-purple {
            background: #f9f0ff;
            color: #722ed1;
        }

        .stat-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .stat-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .stat-trend {
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .stat-period {
            color: var(--text-muted);
        }

        /* Two Columns Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .panel-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .panel-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-mantis {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary-mantis {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary-mantis:hover {
            background: var(--primary-hover);
        }

        .btn-secondary-mantis {
            background: #f1f5f9;
            color: var(--text-heading);
            border-color: var(--border-subtle);
        }

        .btn-secondary-mantis:hover {
            background: #e2e8f0;
        }

        .info-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-val {
            font-weight: 600;
            color: var(--text-heading);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Sidebar Ala Mantis -->
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu" class="brand-logo-img"
                onerror="this.onerror=null; this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div class="brand-info">
                <span class="brand-name">PawKu</span>
                <span class="brand-badge">ADMIN v1.0</span>
            </div>
        </a>

        <div class="sidebar-menu">
            <div>
                <div class="menu-group-label">Navigasi Utama</div>
                <ul class="menu-list">
                    <li class="menu-item active">
                        <a href="{{ route('admin.dashboard') }}">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="menu-group-label">Modul Pet Care</div>
                <ul class="menu-list">
                    <li class="menu-item">
                        <a href="#bookings">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M19 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z">
                                </path>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>Booking Layanan</span>
                            <span class="menu-badge">{{ $stats['bookings'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#cats">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                <line x1="15" y1="9" x2="15.01" y2="9"></line>
                            </svg>
                            <span>Data Kucing</span>
                            <span class="menu-badge">{{ $stats['cats'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#petshops">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Petshops & Mitra</span>
                            <span class="menu-badge">{{ $stats['petshops'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.articles*') ? 'active' : '' }}">
                        <a href="{{ route('admin.articles') }}">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <line x1="10" y1="9" x2="8" y2="9"></line>
                            </svg>
                            <span>Kelola Artikel</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.backup*') ? 'active' : '' }}">
                        <a href="{{ route('admin.backup') }}">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round">
                                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                            </svg>
                            <span>Backup Data</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="menu-group-label">Sistem & Pengguna</div>
                <ul class="menu-list">
                    <li class="menu-item">
                        <a href="#users">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Daftar Pengguna</span>
                            <span class="menu-badge">{{ $stats['users'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('home') }}">
                            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="12 8 8 12 12 16 12 8"></polygon>
                            </svg>
                            <span>Lihat Web Beranda</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-mini-card">
                <div class="user-avatar-mini">
                    @if(!empty($profile['avatar_url']))
                        <img src="{{ $profile['avatar_url'] }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr($profile['full_name'] ?? $user['email'] ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div class="user-mini-meta">
                    <span
                        class="user-mini-name">{{ $profile['full_name'] ?? $user['user_metadata']['full_name'] ?? 'Admin PawKu' }}</span>
                    <span class="user-mini-role">{{ ucfirst($profile['role'] ?? 'Super Admin') }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar Ala Mantis -->
        <header class="topbar">
            <div class="topbar-left">
                <div class="search-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="search-input" placeholder="Cari data di dashboard...">
                    <kbd class="search-shortcut">Ctrl+K</kbd>
                </div>
            </div>

            <div class="topbar-right">
                <div class="env-badge" title="Status Sesi Supabase Aktif">
                    <span class="pulse-dot"></span>
                    <span>Sesi Aktif</span>
                </div>

                <!-- Form Logout Terproteksi CSRF -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
                <button type="button" class="btn-action-logout" title="Keluar dan hapus sesi" onclick="confirmLogout()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Breadcrumb & Header Title -->
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">PawKu</a>
                    <span>/</span>
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                    <span>/</span>
                    <span>Dashboard Mantis</span>
                </div>
                <h1 class="page-title">Dashboard Administrasi</h1>
                <p class="page-subtitle">Selamat datang kembali,
                    <strong>{{ $profile['full_name'] ?? $user['user_metadata']['full_name'] ?? ($user['email'] ?? 'Administrator') }}</strong>.
                    Sesi Anda aktif dan berjalan normal.
                </p>
            </div>

            <!-- Panel Khusus Verifikasi Session (Untuk Memeriksa Session) -->
            <div class="session-banner">
                <div class="banner-header">
                    <div class="banner-title-wrap">
                        <div class="banner-icon-badge">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <div>
                            <div class="banner-title">Status Session Laravel & Supabase</div>
                            <div class="banner-subtitle">Informasi sesi pengguna yang saat ini tersimpan di storage sesi
                                server.</div>
                        </div>
                    </div>
                    <div class="status-pill">
                        <span class="pulse-dot"></span>
                        <span>SESSION TERVERIFIKASI & AKTIF</span>
                    </div>
                </div>

                <div class="session-grid">
                    <div class="session-item">
                        <span class="session-label">Email Terautentikasi</span>
                        <span class="session-val">{{ $user['email'] ?? 'Tidak tersedia' }}</span>
                    </div>
                    <div class="session-item">
                        <span class="session-label">Role Akses</span>
                        <span class="session-val">
                            <span class="badge-role">{{ strtoupper($profile['role'] ?? 'ADMIN') }}</span>
                        </span>
                    </div>
                    <div class="session-item">
                        <span class="session-label">User ID (Supabase UUID)</span>
                        <span class="session-val"
                            style="font-size: 11.5px; font-family: monospace;">{{ $user['id'] ?? '-' }}</span>
                    </div>
                    <div class="session-item">
                        <span class="session-label">Status Kadaluarsa Sesi</span>
                        <span class="session-val" style="color: var(--brand-green);">
                            @if($expiresAt)
                                {{ \Carbon\Carbon::createFromTimestamp($expiresAt)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                WIB
                            @else
                                Sesi Aktif
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Mantis Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Booking Layanan</span>
                        <div class="stat-icon-wrapper icon-blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['bookings'] }}</div>
                    <div class="stat-footer">
                        <span class="stat-trend trend-up">↑ 8.2%</span>
                        <span class="stat-period">vs bulan lalu</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Kucing Terdaftar</span>
                        <div class="stat-icon-wrapper icon-green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                <line x1="15" y1="9" x2="15.01" y2="9"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['cats'] }}</div>
                    <div class="stat-footer">
                        <span class="stat-trend trend-up">↑ 14.5%</span>
                        <span class="stat-period">pasien kucing</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Petshop & Mitra</span>
                        <div class="stat-icon-wrapper icon-gold">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['petshops'] }}</div>
                    <div class="stat-footer">
                        <span class="stat-trend trend-up">Aktif</span>
                        <span class="stat-period">mitra terverifikasi</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Profil Pengguna</span>
                        <div class="stat-icon-wrapper icon-purple">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['users'] }}</div>
                    <div class="stat-footer">
                        <span class="stat-trend trend-up">↑ 22.1%</span>
                        <span class="stat-period">akun terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Two Columns Testing & Diagnostics Panel -->
            <div class="content-grid">
                <div class="panel-card">
                    <div class="panel-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <span>Uji Coba Middleware & Proteksi Sesi</span>
                    </div>
                    <p style="font-size: 13.5px; line-height: 1.6; color: var(--text-body);">
                        Middleware <code>guest</code> dan <code>auth</code> telah diaktifkan untuk memeriksa sesi:
                    </p>
                    <ul style="padding-left: 20px; font-size: 13px; line-height: 1.8; color: var(--text-body);">
                        <li><strong>Uji Proteksi Login:</strong> Jika Anda mengklik tautan ke halaman login di bawah,
                            middleware <code>guest</code> akan secara otomatis mendeteksi sesi aktif Anda dan
                            mengalihkan Anda kembali ke dashboard ini (tidak bisa ke halaman login lagi sebelum logout).
                        </li>
                        <li><strong>Uji Proteksi Logout:</strong> Jika Anda menekan tombol "Logout Sesi", session di
                            server dan Supabase akan dihapus, lalu Anda akan dialihkan kembali ke halaman login.</li>
                    </ul>

                    <div class="action-button-group">
                        <a href="{{ route('login') }}" class="btn-mantis btn-primary-mantis"
                            title="Coba kunjungi /login untuk tes middleware guest">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <span>Tes Akses Halaman Login</span>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" style="display: inline;"
                            onsubmit="event.preventDefault(); confirmLogout()">
                            @csrf
                            <button type="submit" class="btn-mantis btn-secondary-mantis">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Logout & Bersihkan Sesi</span>
                            </button>
                        </form>

                        <a href="{{ route('home') }}" class="btn-mantis btn-secondary-mantis">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Beranda Web</span>
                        </a>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="panel-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Informasi Lingkungan</span>
                    </div>

                    <ul class="info-list">
                        <li class="info-row">
                            <span class="info-label">Session Storage</span>
                            <span class="info-val">file (Active)</span>
                        </li>
                        <li class="info-row">
                            <span class="info-label">Auth Provider</span>
                            <span class="info-val">Supabase GoTrue</span>
                        </li>
                        <li class="info-row">
                            <span class="info-label">Database</span>
                            <span class="info-val">PostgreSQL Pooler</span>
                        </li>
                        <li class="info-row">
                            <span class="info-label">Token Status</span>
                            <span class="info-val" style="color: var(--brand-green);">Valid & Terenkripsi</span>
                        </li>
                        <li class="info-row">
                            <span class="info-label">Waktu Server</span>
                            <span class="info-val">{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
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
    </script>

</body>

</html>
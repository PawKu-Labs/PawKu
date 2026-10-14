@php
    $profile = $profile ?? [];
    $user = $user ?? [];
    $stats = $stats ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backup Data - PawKu</title>

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
            --brand-green: #2E9E6B;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #f0f2f5;
            --border-subtle: #e2e8f0;
            --text-heading: #1e293b;
            --text-body: #475569;
            --text-muted: #8c8c8c;
            --sidebar-width: 260px;
            --header-height: 68px;
            --success: #52c41a;
            --success-bg: #f6ffed;
            --success-border: #b7eb8f;
            --danger: #ff4d4f;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px 0 rgba(0, 0, 0, 0.02);
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
        }

        button, input, select, textarea { font-family: inherit; }

        /* Sidebar */
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

        .brand-logo-img { width: 36px; height: 36px; object-fit: contain; }
        .brand-info { display: flex; flex-direction: column; }

        .brand-name {
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

        .menu-list { list-style: none; display: flex; flex-direction: column; gap: 4px; }

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

        .menu-item a:hover { background-color: #f1f5f9; color: var(--primary); }
        .menu-item.active a { background-color: var(--primary-light); color: var(--primary); font-weight: 600; }

        .menu-icon { width: 18px; height: 18px; stroke-width: 2; color: currentColor; }

        .menu-badge {
            margin-left: auto;
            background: #f1f5f9;
            color: var(--text-body);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border-color); background: #ffffff; }

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

        .user-avatar-mini img { width: 100%; height: 100%; object-fit: cover; }
        .user-mini-meta { overflow: hidden; display: flex; flex-direction: column; }

        .user-mini-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-heading);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-mini-role { font-size: 11px; color: var(--text-muted); }

        /* Main */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

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

        .topbar-left { display: flex; align-items: center; gap: 20px; }

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

        .topbar-right { display: flex; align-items: center; gap: 16px; }

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

        @keyframes pulse { to { box-shadow: 0 0 0 8px rgba(46, 158, 107, 0); } }

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

        .btn-action-logout:hover { background: #fef2f2; border-color: #fca5a5; }

        .content-body {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .page-header { display: flex; flex-direction: column; gap: 6px; }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.3px;
        }

        .page-subtitle { font-size: 13.5px; color: var(--text-body); }

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

        .alert-error { border-color: #fca5a5; color: #b91c1c; background: #fef2f2; font-size: 13.5px; }
        .alert-success { border-color: var(--success-border); color: #065f46; background: var(--success-bg); font-size: 13.5px; }

        .session-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .link-toggle {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
        }

        .link-toggle:hover { text-decoration: underline; }

        .check-list {
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 4px 16px;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            font-size: 13.5px;
            color: var(--text-heading);
            font-weight: 500;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
        }

        .check-item:last-child { border-bottom: none; }
        .check-item input { accent-color: var(--primary); width: 16px; height: 16px; }

        .format-group {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .format-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 13.5px;
            color: var(--text-heading);
            font-weight: 600;
        }

        .format-option small {
            display: block;
            font-weight: 400;
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 2px;
        }

        .format-option input { margin-top: 3px; accent-color: var(--primary); }
        .format-option:has(input:checked) { border-color: var(--primary); background: var(--primary-light); }

        .action-button-group { display: flex; flex-wrap: wrap; gap: 12px; }

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

        .btn-primary-mantis { background: var(--primary); color: #ffffff; }
        .btn-primary-mantis:hover { background: var(--primary-hover); }
        .btn-primary-mantis:disabled { opacity: 0.6; cursor: not-allowed; }

        .btn-secondary-mantis { background: #f1f5f9; color: var(--text-heading); border-color: var(--border-subtle); }
        .btn-secondary-mantis:hover { background: #e2e8f0; }

        .note { font-size: 12.5px; color: var(--text-muted); line-height: 1.6; }

        /* ===== Monitoring Backup ===== */
        .backup-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .backup-stat {
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .backup-stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .backup-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .backup-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 18px;
        }

        .backup-stat-value {
            font-size: 21px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 6px;
        }

        .backup-stat-desc {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .backup-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: var(--success-bg);
            color: #237804;
        }

        /* Form + Kalender: 2 kolom sama lebar & sama tinggi */
        .backup-layout {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: stretch;
        }

        .backup-layout > .monitor-card,
        .backup-layout > .panel-card {
            height: 100%;
        }

        .monitor-card {
            background: #fff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            min-width: 0;
        }

        .monitor-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
        }

        .monitor-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 5px;
        }

        .monitor-card-subtitle {
            font-size: 11.5px;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .monitor-tag {
            font-size: 10px;
            color: var(--primary);
            background: var(--primary-light);
            padding: 5px 8px;
            border-radius: 6px;
            white-space: nowrap;
            font-weight: 700;
        }

        /* Form mengisi tinggi kartu, tombol menempel di bawah */
        #backup-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        #backup-form .action-button-group { margin-top: auto; }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .calendar-nav button {
            width: 30px;
            height: 30px;
            border: 1px solid var(--border-subtle);
            background: #fff;
            border-radius: 7px;
            cursor: pointer;
            color: var(--text-heading);
            font-size: 16px;
        }

        .calendar-nav button:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .calendar-month {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-heading);
            min-width: 95px;
            text-align: center;
        }

        .backup-calendar {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px;
        }

        .calendar-weekday {
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 700;
            padding: 7px 0;
        }

        .calendar-day {
            min-width: 0;
            aspect-ratio: 1 / 0.85;
            border: 1px solid transparent;
            background: #f8fafc;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 11px;
            color: var(--text-heading);
            cursor: pointer;
            position: relative;
        }

        .calendar-day:hover { border-color: var(--primary); }

        .calendar-day.empty {
            background: transparent;
            cursor: default;
        }

        .calendar-day.has-backup {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
            font-weight: 700;
        }

        .calendar-day.selected {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .calendar-day.today { box-shadow: inset 0 0 0 1px var(--primary); }

        .calendar-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #16a34a;
        }

        .calendar-day.selected .calendar-dot { background: var(--primary); }

        .calendar-legend {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 14px;
        }

        .calendar-detail {
            margin-top: 18px;
            padding: 14px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
        }

        .calendar-detail-date {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 9px;
        }

        .calendar-detail-empty {
            font-size: 11.5px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .backup-history-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .backup-history-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--success-bg);
            color: #237804;
            border-radius: 8px;
            font-size: 15px;
        }

        .backup-history-meta { flex: 1; min-width: 0; }

        .backup-history-name {
            font-size: 11.5px;
            color: var(--text-heading);
            font-weight: 600;
        }

        .backup-history-info {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Responsif */
        @media (max-width: 1050px) {
            .backup-layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .content-body { padding: 20px 14px 32px; }
            .backup-summary { grid-template-columns: 1fr; }
            .backup-stat { padding: 16px; }
            .monitor-card,
            .backup-layout > .panel-card { padding: 16px; }
            .format-group { grid-template-columns: 1fr; }
            .chart-container { height: 220px; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Sidebar -->
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
                    <li class="menu-item">
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
                                <path d="M19 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path>
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
                    <li class="menu-item active">
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

    <!-- Main -->
    <div class="main-wrapper">
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

        <main class="content-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">PawKu</a>
                    <span>/</span>
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                    <span>/</span>
                    <span>Backup Data</span>
                </div>
                <h1 class="page-title">Backup Data</h1>
                <p class="page-subtitle">Unduh salinan data PawKu dalam format SQL atau CSV.</p>
            </div>

            @if(session('error'))
                <div class="panel-card alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="panel-card alert-success">{{ session('success') }}</div>
            @endif

            <!-- 1. Ringkasan (3 kartu sama lebar) -->
            <div class="backup-summary">
                <div class="backup-stat">
                    <div class="backup-stat-top">
                        <span class="backup-stat-label">Backup Terakhir</span>
                        <span class="backup-stat-icon">◷</span>
                    </div>
                    <div class="backup-stat-value">14 Okt 2026</div>
                    <div class="backup-stat-desc">09:30 WIB · SQL</div>
                </div>

                <div class="backup-stat">
                    <div class="backup-stat-top">
                        <span class="backup-stat-label">Total Backup Bulan Ini</span>
                        <span class="backup-stat-icon">▤</span>
                    </div>
                    <div class="backup-stat-value">8 kali</div>
                    <div class="backup-stat-desc">Data simulasi Oktober 2026</div>
                </div>

                <div class="backup-stat">
                    <div class="backup-stat-top">
                        <span class="backup-stat-label">Status Backup</span>
                        <span class="backup-stat-icon">✓</span>
                    </div>
                    <div class="backup-stat-value">
                        <span class="backup-status">● Berhasil</span>
                    </div>
                    <div class="backup-stat-desc">Status simulasi, bukan status server</div>
                </div>
            </div>

            <!-- 2. Form + Kalender (2 kolom sama lebar & sama tinggi) -->
            <div class="backup-layout">

                <div class="panel-card">
                    <div class="monitor-card-header" style="margin-bottom:0;">
                        <div>
                            <h3 class="monitor-card-title">Unduh Backup</h3>
                            <p class="monitor-card-subtitle">Pilih data dan format file yang diinginkan</p>
                        </div>
                    </div>

                    <form id="backup-form" action="{{ route('admin.backup.download') }}" method="POST">
                        @csrf

                        <div>
                            <div class="label-row">
                                <span class="session-label">Pilih data</span>
                                <button type="button" class="link-toggle" id="toggle-all">Hapus semua pilihan</button>
                            </div>
                            <div class="check-list">
                                @foreach($tables as $key => $label)
                                    <label class="check-item">
                                        <input type="checkbox" name="tables[]" value="{{ $key }}" checked>
                                        <span>{{ $label }}</span>
                                        @if(isset($stats[$key]))<span class="menu-badge">{{ $stats[$key] }}</span>@endif
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="session-label" style="margin-bottom:10px;">Format</div>
                            <div class="format-group">
                                <label class="format-option">
                                    <input type="radio" name="format" value="SQL" checked>
                                    <span>SQL<small>1 file berisi semua data terpilih</small></span>
                                </label>
                                <label class="format-option">
                                    <input type="radio" name="format" value="csv">
                                    <span>CSV<small>ZIP, 1 file per tabel</small></span>
                                </label>
                            </div>
                        </div>

                        <p class="note">Backup berisi data yang tersimpan saat ini. Simpan file di tempat yang aman karena
                            dapat memuat data pribadi pengguna.</p>

                        <div class="action-button-group">
                            <button type="submit" class="btn-mantis btn-primary-mantis" id="btn-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>Unduh Backup</span>
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn-mantis btn-secondary-mantis">Kembali</a>
                        </div>
                    </form>
                </div>

                <div class="monitor-card">
                    <div class="monitor-card-header">
                        <div>
                            <h3 class="monitor-card-title">Kalender Backup</h3>
                            <p class="monitor-card-subtitle">Pilih tanggal untuk melihat riwayat</p>
                        </div>
                        <span class="monitor-tag">Kalender</span>
                    </div>

                    <div class="calendar-nav" style="justify-content:center; margin-bottom:14px;">
                        <button type="button" id="prevMonth" aria-label="Bulan sebelumnya">‹</button>
                        <span class="calendar-month" id="calendarMonth"></span>
                        <button type="button" id="nextMonth" aria-label="Bulan berikutnya">›</button>
                    </div>

                    <div class="backup-calendar" id="backupCalendar"></div>

                    <div class="calendar-legend">
                        <span class="calendar-dot"></span>
                        <span>Ada riwayat backup</span>
                    </div>

                    <div class="calendar-detail" id="calendarDetail">
                        <div class="calendar-detail-date">Pilih tanggal</div>
                        <div class="calendar-detail-empty">
                            Klik tanggal pada kalender untuk melihat detail backup.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Grafik (lebar penuh, di bawah) -->
            <div class="monitor-card">
                <div class="monitor-card-header">
                    <div>
                        <h3 class="monitor-card-title">Aktivitas Backup</h3>
                        <p class="monitor-card-subtitle">Frekuensi backup selama 7 hari terakhir</p>
                    </div>
                    <span class="monitor-tag">GRAFIK DATA</span>
                </div>
                <div class="chart-container">
                    <canvas id="backupChart"></canvas>
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

        (function () {
            const form = document.getElementById('backup-form');
            const toggleBtn = document.getElementById('toggle-all');
            const btnDownload = document.getElementById('btn-download');
            const boxes = () => form.querySelectorAll('input[name="tables[]"]');

            function syncToggleLabel() {
                const allChecked = [...boxes()].every(b => b.checked);
                toggleBtn.textContent = allChecked ? 'Hapus semua pilihan' : 'Pilih semua';
            }

            toggleBtn.addEventListener('click', () => {
                const allChecked = [...boxes()].every(b => b.checked);
                boxes().forEach(b => b.checked = !allChecked);
                syncToggleLabel();
            });

            boxes().forEach(b => b.addEventListener('change', syncToggleLabel));

            form.addEventListener('submit', (e) => {
                if (![...boxes()].some(b => b.checked)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'Belum ada data dipilih',
                        text: 'Pilih minimal satu data untuk di-backup.',
                        confirmButtonColor: '#1677ff',
                    });
                    return;
                }
                // Cegah klik ganda; tombol aktif lagi setelah download dimulai
                btnDownload.disabled = true;
                setTimeout(() => btnDownload.disabled = false, 4000);
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    (function () {
        // Semua data riwayat di bawah ini adalah DUMMY.
        // Tidak ada request ke Laravel, Supabase, atau API.

        const backupHistory = {
            "2026-10-02": [
                { time: "09:15", format: "SQL", size: "1.24 MB", status: "Berhasil" }
            ],
            "2026-10-05": [
                { time: "10:30", format: "CSV", size: "845 KB", status: "Berhasil" }
            ],
            "2026-10-07": [
                { time: "08:45", format: "SQL", size: "1.31 MB", status: "Berhasil" }
            ],
            "2026-10-08": [
                { time: "13:20", format: "SQL", size: "1.32 MB", status: "Berhasil" }
            ],
            "2026-10-10": [
                { time: "09:00", format: "CSV", size: "862 KB", status: "Berhasil" }
            ],
            "2026-10-12": [
                { time: "11:10", format: "SQL", size: "1.38 MB", status: "Berhasil" }
            ],
            "2026-10-14": [
                { time: "09:30", format: "SQL", size: "1.42 MB", status: "Berhasil" }
            ]
        };

        // Grafik aktivitas 8–14 Oktober 2026
        const chartCanvas = document.getElementById("backupChart");

        if (chartCanvas && typeof Chart !== "undefined") {
            new Chart(chartCanvas, {
                type: "bar",
                data: {
                    labels: [
                        "8 Okt", "9 Okt", "10 Okt", "11 Okt",
                        "12 Okt", "13 Okt", "14 Okt"
                    ],
                    datasets: [{
                        label: "Jumlah backup",
                        data: [1, 0, 1, 0, 1, 0, 1],
                        backgroundColor: "#1677ff",
                        hoverBackgroundColor: "#0958d9",
                        borderRadius: 6,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.raw + " aktivitas backup (dummy)";
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: "#f1f5f9" },
                            title: {
                                display: true,
                                text: "Jumlah backup"
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Kalender
        const calendar = document.getElementById("backupCalendar");
        const monthLabel = document.getElementById("calendarMonth");
        const detail = document.getElementById("calendarDetail");

        if (!calendar || !monthLabel || !detail) return;

        let currentMonth = new Date(2026, 9, 1);
        let selectedDate = "2026-10-14";

        const monthNames = [
            "Januari", "Februari", "Maret", "April",
            "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        const weekdays = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

        function pad(value) {
            return String(value).padStart(2, "0");
        }

        function dateKey(year, month, day) {
            return year + "-" + pad(month + 1) + "-" + pad(day);
        }

        function showDetail(key) {
            selectedDate = key;
            const entries = backupHistory[key] || [];
            const parts = key.split("-");
            const date = new Date(
                Number(parts[0]),
                Number(parts[1]) - 1,
                Number(parts[2])
            );

            detail.replaceChildren();

            const heading = document.createElement("div");
            heading.className = "calendar-detail-date";
            heading.textContent = date.toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric"
            });

            detail.appendChild(heading);

            if (entries.length === 0) {
                const empty = document.createElement("div");
                empty.className = "calendar-detail-empty";
                empty.textContent = "Tidak ada riwayat backup pada tanggal ini (data dummy).";
                detail.appendChild(empty);
                return;
            }

            entries.forEach(function (entry) {
                const item = document.createElement("div");
                item.className = "backup-history-item";

                const icon = document.createElement("div");
                icon.className = "backup-history-icon";
                icon.textContent = "✓";

                const meta = document.createElement("div");
                meta.className = "backup-history-meta";

                const name = document.createElement("div");
                name.className = "backup-history-name";
                name.textContent = "Backup " + entry.format + " · " + entry.status;

                const info = document.createElement("div");
                info.className = "backup-history-info";
                info.textContent = entry.time + " WIB · " + entry.size;

                meta.append(name, info);
                item.append(icon, meta);
                detail.appendChild(item);
            });
        }

        function renderCalendar() {
            const year = currentMonth.getFullYear();
            const month = currentMonth.getMonth();

            monthLabel.textContent = monthNames[month] + " " + year;
            calendar.replaceChildren();

            weekdays.forEach(function (day) {
                const cell = document.createElement("div");
                cell.className = "calendar-weekday";
                cell.textContent = day;
                calendar.appendChild(cell);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const blank = document.createElement("div");
                blank.className = "calendar-day empty";
                blank.setAttribute("aria-hidden", "true");
                calendar.appendChild(blank);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const key = dateKey(year, month, day);
                const hasBackup = Boolean(backupHistory[key]);

                const cell = document.createElement("button");
                cell.type = "button";
                cell.className = "calendar-day";
                cell.textContent = day;

                if (hasBackup) {
                    cell.classList.add("has-backup");
                    cell.setAttribute("aria-label", day + ", ada riwayat backup");
                    const dot = document.createElement("span");
                    dot.className = "calendar-dot";
                    dot.setAttribute("aria-hidden", "true");
                    cell.appendChild(dot);
                }

                if (key === selectedDate) {
                    cell.classList.add("selected");
                    cell.setAttribute("aria-pressed", "true");
                }

                cell.addEventListener("click", function () {
                    showDetail(key);
                    renderCalendar();
                });

                calendar.appendChild(cell);
            }
        }

        document.getElementById("prevMonth").addEventListener("click", function () {
            currentMonth = new Date(
                currentMonth.getFullYear(),
                currentMonth.getMonth() - 1,
                1
            );
            renderCalendar();
        });

        document.getElementById("nextMonth").addEventListener("click", function () {
            currentMonth = new Date(
                currentMonth.getFullYear(),
                currentMonth.getMonth() + 1,
                1
            );
            renderCalendar();
        });

        renderCalendar();
        showDetail(selectedDate);
    })();
    </script>

</body>

</html>
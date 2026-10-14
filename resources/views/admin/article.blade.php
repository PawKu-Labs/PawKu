<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Artikel - PawKu Admin</title>

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

        /* ===== Sidebar ===== */
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

        /* ===== Main layout ===== */
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

        .content-body {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .page-header-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

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
            font-size: 22px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: var(--text-body);
        }

        /* ===== Buttons ===== */
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

        /* ===== Stat cards ===== */
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
            font-size: 26px;
            font-weight: 700;
            color: var(--text-heading);
        }

        /* ===== Toolbar ===== */
        .panel-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .filter-tabs {
            display: flex;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: var(--radius-md);
        }

        .filter-tab {
            border: none;
            background: transparent;
            padding: 7px 16px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-body);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-tab.active {
            background: #ffffff;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .select-input {
            border: 1px solid var(--border-subtle);
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 9px 14px;
            font-size: 13px;
            color: var(--text-heading);
            outline: none;
            cursor: pointer;
        }

        .select-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 119, 255, 0.12);
        }

        .search-box.inline {
            width: 260px;
        }

        /* ===== Catalog ===== */
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 22px;
        }

        .article-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .article-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
        }

        .article-thumb {
            position: relative;
            aspect-ratio: 16 / 9;
            background: var(--primary-light);
            overflow: hidden;
        }

        .article-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .badge-status {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            backdrop-filter: blur(4px);
        }

        .badge-publish {
            background: rgba(236, 253, 245, 0.95);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-draft {
            background: rgba(255, 251, 230, 0.95);
            color: #ad6800;
            border: 1px solid #ffe58f;
        }

        .article-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .badge-role {
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            align-self: flex-start;
        }

        .article-title {
            font-size: 14.5px;
            line-height: 1.4;
            font-weight: 700;
            color: var(--text-heading);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-desc {
            font-size: 12.5px;
            line-height: 1.55;
            color: var(--text-body);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: auto;
            padding-top: 4px;
        }

        .article-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .article-actions {
            display: flex;
            gap: 8px;
            padding: 12px 18px;
            border-top: 1px solid var(--border-color);
            background: #fcfdff;
        }

        .btn-icon {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 10px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
            background: #ffffff;
            color: var(--text-heading);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .btn-icon.danger {
            color: #ef4444;
            border-color: #fee2e2;
        }

        .btn-icon.danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #ef4444;
        }

        .empty-state {
            display: none;
            text-align: center;
            padding: 56px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .empty-state.show {
            display: block;
        }

        /* ===== Modal ===== */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal {
            background: #ffffff;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .modal-close {
            border: none;
            background: transparent;
            font-size: 22px;
            line-height: 1;
            color: var(--text-muted);
            cursor: pointer;
        }

        .modal-body {
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-heading);
        }

        .form-control {
            border: 1px solid var(--border-subtle);
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 13px;
            color: var(--text-heading);
            outline: none;
            width: 100%;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 119, 255, 0.12);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 90px;
            line-height: 1.5;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
        }

        /* ===== Dropzone upload gambar ===== */
        .dropzone {
            position: relative;
            border: 2px dashed var(--border-subtle);
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dropzone:hover,
        .dropzone.dragover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .dropzone-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #ffffff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }

        .dropzone-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-heading);
        }

        .dropzone-text span {
            color: var(--primary);
        }

        .dropzone-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .dropzone.has-image {
            padding: 0;
            border-style: solid;
            overflow: hidden;
            cursor: default;
        }

        .dropzone-preview {
            display: none;
            position: relative;
            aspect-ratio: 16 / 9;
        }

        .dropzone.has-image .dropzone-preview {
            display: block;
        }

        .dropzone.has-image .dropzone-empty {
            display: none;
        }

        .dropzone-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dropzone-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
        }

        .dz-btn {
            border: none;
            background: rgba(255, 255, 255, 0.95);
            color: var(--text-heading);
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .dz-btn.danger {
            color: #ef4444;
        }

        .dropzone-filename {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 8px 12px;
            font-size: 11.5px;
            color: #ffffff;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .search-box {
                width: 100%;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

@php
    // ------------------------------------------------------------------
    // DATA DUMMY ARTIKEL (nanti ganti dengan data dari controller/DB)
    // ------------------------------------------------------------------
    $articles = [
        [
            'id' => 'a1',
            'title' => '5 Tanda Kucing Kamu Sedang Stres dan Cara Mengatasinya',
            'description' => 'Kucing sering menyembunyikan rasa tidak nyaman. Kenali perubahan nafsu makan, kebiasaan grooming, dan perilaku bersembunyi sebelum masalahnya menjadi lebih serius.',
            'category' => 'Kucing',
            'author' => 'Admin PawKu',
            'status' => 'publish',
            'views' => 1284,
            'date' => '2026-10-10',
            'image' => 'https://picsum.photos/seed/pawku1/1000/600',
        ],
        [
            'id' => 'a2',
            'title' => 'Jadwal Vaksin Anjing yang Wajib Diketahui Pemilik Baru',
            'description' => 'Vaksinasi sejak dini melindungi anjing dari penyakit berbahaya. Simak kapan vaksin pertama diberikan dan seberapa sering pengulangannya dilakukan.',
            'category' => 'Anjing',
            'author' => 'drh. Sinta',
            'status' => 'publish',
            'views' => 932,
            'date' => '2026-10-07',
            'image' => 'https://picsum.photos/seed/pawku2/1000/600',
        ],
        [
            'id' => 'a3',
            'title' => 'Memilih Makanan Kering yang Tepat untuk Hewan Kesayangan',
            'description' => 'Tidak semua makanan cocok untuk setiap usia dan ukuran. Pelajari cara membaca label komposisi agar nutrisi harian hewanmu tercukupi dengan baik.',
            'category' => 'Nutrisi',
            'author' => 'Admin PawKu',
            'status' => 'publish',
            'views' => 756,
            'date' => '2026-10-03',
            'image' => 'https://picsum.photos/seed/pawku3/1000/600',
        ],
        [
            'id' => 'a4',
            'title' => 'Cara Memotong Kuku Kucing Tanpa Drama di Rumah',
            'description' => 'Memotong kuku bisa jadi momen menegangkan. Ikuti langkah sederhana dan tips membuat kucing tenang agar proses grooming kuku berjalan aman dan nyaman.',
            'category' => 'Perawatan',
            'author' => 'Rina Kusuma',
            'status' => 'draft',
            'views' => 0,
            'date' => '2026-10-12',
            'image' => 'https://picsum.photos/seed/pawku4/1000/600',
        ],
        [
            'id' => 'a5',
            'title' => 'Mengenal Gejala Awal Kutu dan Jamur pada Kulit Hewan',
            'description' => 'Garukan berlebihan dan bulu rontok bisa jadi tanda masalah kulit. Kenali gejala awal kutu dan jamur serta langkah penanganan pertama sebelum ke dokter hewan.',
            'category' => 'Kesehatan',
            'author' => 'drh. Sinta',
            'status' => 'publish',
            'views' => 511,
            'date' => '2026-09-28',
            'image' => 'https://picsum.photos/seed/pawku5/1000/600',
        ],
        [
            'id' => 'a6',
            'title' => 'Tips Membawa Anjing Bepergian Naik Mobil dengan Aman',
            'description' => 'Perjalanan jauh bisa membuat anjing mabuk atau cemas. Siapkan perlengkapan, atur jeda istirahat, dan biasakan anjing sebelum hari keberangkatan.',
            'category' => 'Anjing',
            'author' => 'Admin PawKu',
            'status' => 'draft',
            'views' => 0,
            'date' => '2026-10-13',
            'image' => 'https://picsum.photos/seed/pawku6/1000/600',
        ],
    ];

    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $fmtDate = function ($d) use ($bulan) {
        $t = strtotime($d);
        return date('j', $t) . ' ' . $bulan[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
    };

    $collection = collect($articles);
    $totalArtikel = $collection->count();
    $totalPublish = $collection->where('status', 'publish')->count();
    $totalDraft = $collection->where('status', 'draft')->count();
    $totalViews = $collection->sum('views');
    $categories = $collection->pluck('category')->unique()->values();
@endphp

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
                    <li class="menu-item active">
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
                            <span class="menu-badge">{{ $totalArtikel }}</span>
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
        <!-- Topbar -->
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

        <!-- Content Body -->
        <main class="content-body">
            <div class="page-header-row">
                <div class="page-header">
                    <div class="breadcrumb">
                        <a href="{{ route('home') }}">PawKu</a>
                        <span>/</span>
                        <a href="{{ route('admin.dashboard') }}">Admin</a>
                        <span>/</span>
                        <span>Kelola Artikel</span>
                    </div>
                    <h1 class="page-title">Kelola Artikel</h1>
                    <p class="page-subtitle">Atur artikel edukasi yang tampil di Beranda aplikasi PawKu.</p>
                </div>
                <button type="button" class="btn-mantis btn-primary-mantis" onclick="openModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah Artikel</span>
                </button>
            </div>

            <!-- Ringkasan -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Artikel</span>
                        <div class="stat-icon-wrapper icon-blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalArtikel }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Terbit</span>
                        <div class="stat-icon-wrapper icon-green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalPublish }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Draf</span>
                        <div class="stat-icon-wrapper icon-gold">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalDraft }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Dibaca</span>
                        <div class="stat-icon-wrapper icon-purple">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($totalViews, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Toolbar filter -->
            <div class="panel-card">
                <div class="toolbar">
                    <div class="filter-tabs" id="statusTabs">
                        <button type="button" class="filter-tab active" data-status="all">Semua</button>
                        <button type="button" class="filter-tab" data-status="publish">Terbit</button>
                        <button type="button" class="filter-tab" data-status="draft">Draf</button>
                    </div>

                    <div class="toolbar-right">
                        <select class="select-input" id="categoryFilter">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <div class="search-box inline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="articleSearch" class="search-input"
                                placeholder="Cari judul artikel...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Katalog -->
            <div class="catalog-grid" id="catalogGrid">
                @foreach($articles as $a)
                    <article class="article-card" data-status="{{ $a['status'] }}"
                        data-category="{{ strtolower($a['category']) }}"
                        data-title="{{ strtolower($a['title']) }}">
                        <div class="article-thumb">
                            <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}" loading="lazy">
                            <span class="badge-status {{ $a['status'] === 'publish' ? 'badge-publish' : 'badge-draft' }}">
                                {{ $a['status'] === 'publish' ? 'Terbit' : 'Draf' }}
                            </span>
                        </div>

                        <div class="article-body">
                            <span class="badge-role">{{ $a['category'] }}</span>
                            <h3 class="article-title">{{ $a['title'] }}</h3>
                            <p class="article-desc">{{ $a['description'] }}</p>

                            <div class="article-meta">
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    {{ $fmtDate($a['date']) }}
                                </span>
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    {{ $a['author'] }}
                                </span>
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    {{ number_format($a['views'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="article-actions">
                            <button type="button" class="btn-icon"
                                onclick="openModal({{ json_encode($a) }})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                Edit
                            </button>
                            <button type="button" class="btn-icon danger"
                                onclick="confirmDelete(this, @js($a['title']))">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="empty-state" id="emptyState">
                Tidak ada artikel yang cocok dengan filter atau pencarian Anda.
            </div>
        </main>
    </div>

    <!-- Modal Tambah / Edit (dummy, belum tersambung ke database) -->
    <div class="modal-backdrop" id="articleModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Tambah Artikel</h2>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="fTitle">Judul</label>
                    <input type="text" id="fTitle" class="form-control" placeholder="Judul artikel">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="fCategory">Kategori</label>
                        <select id="fCategory" class="form-control">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="fStatus">Status</label>
                        <select id="fStatus" class="form-control">
                            <option value="publish">Terbit</option>
                            <option value="draft">Draf</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Gambar Artikel</label>
                    <input type="file" id="fImage" accept="image/png,image/jpeg,image/webp" hidden>
                    <div class="dropzone" id="dropzone" tabindex="0" role="button"
                        aria-label="Unggah gambar artikel">
                        <div class="dropzone-empty">
                            <div class="dropzone-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div class="dropzone-text">Tarik &amp; lepas gambar di sini, atau <span>pilih file</span></div>
                            <div class="dropzone-hint">JPG, PNG, atau WEBP. Maksimal 2 MB. Rasio 16:9 disarankan.</div>
                        </div>
                        <div class="dropzone-preview">
                            <img id="dzImg" src="" alt="Pratinjau gambar">
                            <div class="dropzone-actions">
                                <button type="button" class="dz-btn" id="dzChange">Ganti</button>
                                <button type="button" class="dz-btn danger" id="dzRemove">Hapus</button>
                            </div>
                            <div class="dropzone-filename" id="dzName"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="fDesc">Deskripsi Singkat</label>
                    <textarea id="fDesc" class="form-control" placeholder="Ringkasan untuk kartu di Beranda"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="fContent">Isi Artikel</label>
                    <textarea id="fContent" class="form-control" style="min-height:140px;"
                        placeholder="Pisahkan antar paragraf dengan baris kosong"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-mantis btn-secondary-mantis" onclick="closeModal()">Batal</button>
                <button type="button" class="btn-mantis btn-primary-mantis" onclick="saveArticle()">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        // ----- Logout -----
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

        // ----- Filter & pencarian (client-side) -----
        let activeStatus = 'all';
        const cards = () => document.querySelectorAll('#catalogGrid .article-card');

        function applyFilter() {
            const cat = document.getElementById('categoryFilter').value;
            const q = document.getElementById('articleSearch').value.trim().toLowerCase();
            let visible = 0;

            cards().forEach(card => {
                const okStatus = activeStatus === 'all' || card.dataset.status === activeStatus;
                const okCat = cat === 'all' || card.dataset.category === cat;
                const okSearch = !q || card.dataset.title.includes(q);
                const show = okStatus && okCat && okSearch;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('emptyState').classList.toggle('show', visible === 0);
        }

        document.querySelectorAll('#statusTabs .filter-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#statusTabs .filter-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeStatus = btn.dataset.status;
                applyFilter();
            });
        });
        document.getElementById('categoryFilter').addEventListener('change', applyFilter);
        document.getElementById('articleSearch').addEventListener('input', applyFilter);

        // ----- Hapus (dummy: hanya menghapus kartu dari tampilan) -----
        function confirmDelete(btn, title) {
            Swal.fire({
                title: 'Hapus artikel?',
                text: '"' + title + '" akan dihapus dari daftar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('.article-card').remove();
                    applyFilter();
                    Swal.fire({ title: 'Terhapus', text: 'Artikel berhasil dihapus (dummy).', icon: 'success', timer: 1400, showConfirmButton: false });
                }
            });
        }

        // ----- Dropzone upload gambar (dummy: hanya pratinjau lokal) -----
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fImage');
        const dzImg = document.getElementById('dzImg');
        const dzName = document.getElementById('dzName');
        const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
        const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
        let objectUrl = null;

        function setPreview(src, name) {
            if (objectUrl && src !== objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }
            if (src) {
                dzImg.src = src;
                dzName.textContent = name || '';
                dropzone.classList.add('has-image');
            } else {
                dzImg.removeAttribute('src');
                dzName.textContent = '';
                fileInput.value = '';
                dropzone.classList.remove('has-image');
            }
        }

        function handleFile(file) {
            if (!file) return;
            if (!ALLOWED.includes(file.type)) {
                Swal.fire({ title: 'Format tidak didukung', text: 'Gunakan gambar JPG, PNG, atau WEBP.', icon: 'error', confirmButtonColor: '#1677ff' });
                return;
            }
            if (file.size > MAX_SIZE) {
                Swal.fire({ title: 'Ukuran terlalu besar', text: 'Ukuran gambar maksimal 2 MB.', icon: 'error', confirmButtonColor: '#1677ff' });
                return;
            }
            const url = URL.createObjectURL(file);
            setPreview(url, file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB');
            objectUrl = url;
        }

        // Buka dialog pilih file
        dropzone.addEventListener('click', e => {
            if (dropzone.classList.contains('has-image')) return;
            fileInput.click();
        });
        dropzone.addEventListener('keydown', e => {
            if ((e.key === 'Enter' || e.key === ' ') && !dropzone.classList.contains('has-image')) {
                e.preventDefault();
                fileInput.click();
            }
        });
        document.getElementById('dzChange').addEventListener('click', e => {
            e.stopPropagation();
            fileInput.click();
        });
        document.getElementById('dzRemove').addEventListener('click', e => {
            e.stopPropagation();
            setPreview('');
        });
        fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

        // Drag & drop
        ['dragenter', 'dragover'].forEach(ev => dropzone.addEventListener(ev, e => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        }));
        ['dragleave', 'drop'].forEach(ev => dropzone.addEventListener(ev, e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        }));
        dropzone.addEventListener('drop', e => {
            const file = e.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        });

        // Cegah browser membuka file jika dilepas di luar dropzone
        ['dragover', 'drop'].forEach(ev => window.addEventListener(ev, e => {
            if (!dropzone.contains(e.target)) e.preventDefault();
        }));

        // ----- Modal tambah / edit -----
        const modal = document.getElementById('articleModal');

        function openModal(article) {
            const isEdit = !!article;
            document.getElementById('modalTitle').textContent = isEdit ? 'Edit Artikel' : 'Tambah Artikel';
            document.getElementById('fTitle').value = isEdit ? article.title : '';
            document.getElementById('fCategory').value = isEdit ? article.category : document.getElementById('fCategory').options[0].value;
            document.getElementById('fStatus').value = isEdit ? article.status : 'publish';
            setPreview(isEdit ? article.image : '', isEdit ? 'Gambar saat ini' : '');
            document.getElementById('fDesc').value = isEdit ? article.description : '';
            document.getElementById('fContent').value = '';
            modal.classList.add('show');
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });

        function saveArticle() {
            closeModal();
            Swal.fire({
                title: 'Tersimpan (dummy)',
                text: 'Data belum disimpan ke database. Hubungkan ke controller nanti.',
                icon: 'info',
                confirmButtonColor: '#1677ff',
            });
        }
    </script>

</body>

</html>
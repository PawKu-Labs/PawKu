<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PawKu Superadmin</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icon/pawku-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --primary: #F59E3D;
            --primary-hover: #E08A24;
            --primary-light: #FEF3E4;
            --violet: #EA580C;
            --green: #2E9E6B;
            --green-hover: #238356;
            --green-light: #e6f7ef;
            --bg: #fbf8f4;
            --border: #ece5dc;
            --heading: #2b2118;
            --body: #5b5047;
            --muted: #8c8176;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --warning: #faad14;
            --sidebar: 268px;
            --header: 68px;
            --r: 10px;
            --rl: 16px;

            --grad-brand: linear-gradient(135deg, #F7B15C 0%, #F59E3D 50%, #EA8A1F 100%);
            --grad-green: linear-gradient(135deg, #2E9E6B 0%, #14b8a6 100%);
            --grad-amber: linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%);
            --grad-rose: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%);
            --grad-sky: linear-gradient(135deg, #F7B15C 0%, #F59E3D 100%);
            --grad-purple: linear-gradient(135deg, #FB923C 0%, #EA580C 100%);

            --shadow: 0 1px 2px rgba(43, 33, 24, .04), 0 4px 14px -4px rgba(43, 33, 24, .07);
            --shadow-md: 0 10px 28px -8px rgba(234, 138, 31, .28);
            --ring: 0 0 0 3px rgba(245, 158, 61, .35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background:
                radial-gradient(900px 420px at 100% -10%, rgba(251, 146, 60, .10), transparent 60%),
                radial-gradient(800px 380px at 0% 0%, rgba(245, 158, 61, .08), transparent 60%),
                var(--bg);
            background-attachment: fixed;
            color: var(--body);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
            font-size: inherit
        }

        :focus-visible {
            outline: none;
            box-shadow: var(--ring);
            border-radius: 8px
        }

        /* ---------- Sidebar (putih) ---------- */
        .sidebar {
            width: var(--sidebar);
            background: #fff;
            color: var(--body);
            border-right: 1px solid var(--border);
            height: 100vh;
            height: 100dvh;
            position: fixed;
            inset: 0 auto 0 0;
            display: flex;
            flex-direction: column;
            z-index: 120;
            transition: transform .28s cubic-bezier(.4, 0, .2, 1)
        }

        .brand {
            height: var(--header);
            display: flex;
            align-items: center;
            padding: 0 22px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0
        }

        .brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 3px
        }

        .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--heading);
            display: block;
            line-height: 1.1
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .6px;
            color: #fff;
            background: var(--grad-brand);
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 3px
        }

        .menu {
            flex: 1;
            overflow-y: auto;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .menu-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: .8px;
            padding: 8px 12px 6px
        }

        .menu-label:not(:first-child) {
            margin-top: 10px
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: var(--body);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 12px;
            transition: background .2s, color .2s
        }

        .menu a:hover {
            background: var(--primary-light);
            color: var(--primary-hover)
        }

        .menu a.active {
            background: var(--grad-brand);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 8px 20px -8px rgba(234, 138, 31, .7)
        }

        .menu svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0
        }

        .menu .badge {
            margin-left: auto;
            background: var(--grad-rose);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px
        }

        .side-foot {
            padding: 14px;
            border-top: 1px solid var(--border)
        }

        .me {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #faf6f1;
            border: 1px solid var(--border)
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--grad-brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .me-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--heading);
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .me-mail {
            font-size: 11px;
            color: var(--muted);
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .me-role {
            font-size: 11px;
            color: var(--muted)
        }

        .scrim {
            position: fixed;
            inset: 0;
            background: rgba(43, 33, 24, .5);
            backdrop-filter: blur(2px);
            z-index: 110;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s
        }

        /* ---------- Main ---------- */
        .wrap {
            margin-left: var(--sidebar);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0
        }

        .topbar {
            height: var(--header);
            background: rgba(255, 255, 255, .86);
            backdrop-filter: saturate(180%) blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 99;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            gap: 12px
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0
        }

        .topbar-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--heading);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .topbar-title span {
            color: var(--muted);
            font-weight: 400
        }

        .hamburger {
            display: none;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--heading);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .hamburger .bars,
        .hamburger .bars::before,
        .hamburger .bars::after {
            display: block;
            width: 18px;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            transition: transform .25s, opacity .2s
        }

        .hamburger .bars {
            position: relative
        }

        .hamburger .bars::before,
        .hamburger .bars::after {
            content: '';
            position: absolute;
            left: 0
        }

        .hamburger .bars::before {
            top: -6px
        }

        .hamburger .bars::after {
            top: 6px
        }

        .nav-open .hamburger .bars {
            background: transparent
        }

        .nav-open .hamburger .bars::before {
            transform: translateY(6px) rotate(45deg)
        }

        .nav-open .hamburger .bars::after {
            transform: translateY(-6px) rotate(-45deg)
        }

        .btn-logout {
            background: #fff;
            border: 1px solid #fee2e2;
            color: var(--danger);
            font-size: 13px;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: var(--r);
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0
        }

        .btn-logout:hover {
            background: #fef2f2;
            border-color: #fca5a5
        }

        .content {
            padding: 28px 32px 56px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0
        }

        /* ---------- Hero ---------- */
        .hero {
            position: relative;
            overflow: hidden;
            background: var(--grad-brand);
            color: #fff;
            border-radius: 20px;
            padding: 26px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 18px 40px -18px rgba(234, 138, 31, .75)
        }

        .hero::before,
        .hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .14);
            pointer-events: none
        }

        .hero::before {
            width: 260px;
            height: 260px;
            right: -70px;
            top: -120px
        }

        .hero::after {
            width: 160px;
            height: 160px;
            right: 140px;
            bottom: -110px;
            background: rgba(255, 255, 255, .09)
        }

        .hero>* {
            position: relative;
            z-index: 1
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.2px
        }

        .page-sub {
            font-size: 13.5px;
            color: rgba(255, 255, 255, .92);
            margin-top: 6px;
            max-width: 62ch;
            line-height: 1.55
        }

        .hero .btn {
            background: rgba(255, 255, 255, .2);
            color: #fff;
            border-color: rgba(255, 255, 255, .45);
            backdrop-filter: blur(6px)
        }

        .hero .btn:hover {
            background: rgba(255, 255, 255, .32)
        }

        .alert-bar {
            background: linear-gradient(135deg, #fff8e1, #ffedd5);
            border: 1px solid #fed7aa;
            border-radius: var(--rl);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px
        }

        .alert-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--grad-amber);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0
        }

        .group-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--heading);
            margin-bottom: 12px
        }

        .grid {
            display: grid;
            gap: 18px
        }

        .g2 {
            grid-template-columns: repeat(2, 1fr)
        }

        .g3 {
            grid-template-columns: repeat(3, 1fr)
        }

        .g4 {
            grid-template-columns: repeat(4, 1fr)
        }

        @media (max-width: 1180px) {

            .g3,
            .g4 {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media (max-width: 640px) {

            .g2,
            .g3 {
                grid-template-columns: 1fr
            }

            .g4 {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px
            }
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--rl);
            padding: 20px 22px;
            box-shadow: var(--shadow)
        }

        .card.stat {
            transition: transform .2s ease, box-shadow .2s ease;
            position: relative;
            overflow: hidden
        }

        .card.stat::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent, var(--grad-brand))
        }

        a.card.stat:hover,
        .card.stat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md)
        }

        .stat {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-decoration: none
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px
        }

        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted)
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 16px -8px rgba(0, 0, 0, .35)
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            color: var(--heading);
            line-height: 1.1
        }

        .i-blue {
            background: var(--grad-sky)
        }

        .i-green {
            background: var(--grad-green)
        }

        .i-gold {
            background: var(--grad-amber)
        }

        .i-red {
            background: var(--grad-rose)
        }

        .i-purple {
            background: var(--grad-purple)
        }

        .a-blue {
            --accent: var(--grad-sky)
        }

        .a-green {
            --accent: var(--grad-green)
        }

        .a-gold {
            --accent: var(--grad-amber)
        }

        .a-red {
            --accent: var(--grad-rose)
        }

        .a-purple {
            --accent: var(--grad-purple)
        }

        @media (max-width: 640px) {
            .g4 .card.stat {
                padding: 14px
            }

            .g4 .stat-value {
                font-size: 24px
            }

            .g4 .stat-icon {
                width: 34px;
                height: 34px;
                font-size: 15px
            }
        }

        /* ---------- Tabs & filters ---------- */
        .tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .tab {
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--body);
            background: #fff;
            border: 1px solid var(--border);
            transition: .2s;
            white-space: nowrap
        }

        .tab:hover {
            border-color: var(--primary);
            color: var(--primary-hover)
        }

        .tab.active {
            background: var(--grad-brand);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 18px -8px rgba(234, 138, 31, .8)
        }

        .tab .n {
            margin-left: 6px;
            opacity: .85;
            font-weight: 700
        }

        @media (max-width: 720px) {
            .tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                margin: 0 -16px;
                padding: 2px 16px 8px;
                scrollbar-width: none
            }

            .tabs::-webkit-scrollbar {
                display: none
            }
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0
        }

        .field.grow {
            flex: 1 1 240px
        }

        .field label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted)
        }

        .input {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 10px;
            padding: 9px 12px;
            color: var(--heading);
            width: 100%;
            min-height: 40px
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: var(--ring)
        }

        /* ---------- Table ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px
        }

        th {
            text-align: left;
            font-size: 11.5px;
            letter-spacing: .3px;
            color: var(--muted);
            padding: 13px 14px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(180deg, #fffdfa, #fbf5ed);
            font-weight: 700
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #f3ede6;
            vertical-align: middle;
            color: var(--heading)
        }

        tbody tr {
            transition: background .15s
        }

        tbody tr:hover {
            background: #fffaf4
        }

        tr:last-child td {
            border-bottom: none
        }

        .sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px
        }

        .table-wrap {
            overflow-x: auto
        }

        .cell-first {
            padding-left: 22px
        }

        .cell-last {
            padding-right: 22px
        }

        .pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap
        }

        .p-active,
        .p-approved {
            background: #ecfdf5;
            color: #065f46
        }

        .p-inactive,
        .p-pending {
            background: #fffbe6;
            color: #ad6800
        }

        .p-blocked,
        .p-rejected {
            background: #fff2f0;
            color: #cf1322
        }

        .c-user {
            background: #FEF3E4;
            color: #B45309
        }

        .c-data {
            background: #ffe9ec;
            color: #be123c
        }

        .c-transaction {
            background: #e7f8ef;
            color: #15803d
        }

        .c-admin {
            background: #fef9c3;
            color: #854d0e
        }

        .c-auth {
            background: #f3efea;
            color: #5b5047
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #f6f1ec;
            color: var(--heading);
            text-decoration: none;
            transition: .2s;
            min-height: 36px
        }

        .btn:hover {
            background: #ece5dc
        }

        .btn-primary {
            background: var(--grad-brand);
            color: #fff;
            border-color: transparent
        }

        .btn-primary:hover {
            background: var(--grad-brand);
            filter: brightness(1.06)
        }

        .btn-green {
            background: var(--grad-green);
            color: #fff;
            border-color: transparent
        }

        .btn-green:hover {
            background: var(--grad-green);
            filter: brightness(1.08)
        }

        .btn-danger {
            background: #fff;
            color: var(--danger);
            border-color: #fecaca
        }

        .btn-danger:hover {
            background: #fef2f2;
            border-color: var(--danger)
        }

        .btn-outline {
            background: #fff;
            color: var(--body);
            border-color: var(--border)
        }

        .btn-outline:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-hover)
        }

        .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center
        }

        .actions form {
            display: inline
        }

        ul.pagination {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            list-style: none;
            flex-wrap: wrap
        }

        ul.pagination li>a,
        ul.pagination li>span {
            display: inline-block;
            padding: 7px 14px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--body);
            text-decoration: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600
        }

        ul.pagination li.disabled>span {
            opacity: .5
        }

        ul.pagination li>a:hover {
            border-color: var(--primary);
            color: var(--primary-hover)
        }

        .pager {
            padding: 16px 22px;
            border-top: 1px solid #f3ede6
        }

        .empty {
            text-align: center;
            padding: 42px 16px;
            color: var(--muted);
            font-size: 13.5px
        }

        .empty>div:first-child i {
            color: var(--primary)
        }

        /* ---------- Feed ---------- */
        .feed {
            list-style: none;
            display: flex;
            flex-direction: column
        }

        .feed li {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f3ede6
        }

        .feed li:last-child {
            border-bottom: none
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
            background: var(--grad-brand)
        }

        .dot.user {
            background: var(--grad-sky)
        }

        .dot.data {
            background: var(--grad-rose)
        }

        .dot.transaction {
            background: var(--grad-green)
        }

        .dot.admin {
            background: var(--grad-amber)
        }

        .dot.auth {
            background: #a8a095
        }

        .feed-text {
            font-size: 13px;
            color: var(--heading);
            font-weight: 500;
            overflow-wrap: anywhere
        }

        /* ---------- Modal ---------- */
        dialog {
            border: none;
            border-radius: 20px;
            padding: 24px;
            width: min(560px, 92vw);
            box-shadow: 0 24px 70px rgba(43, 33, 24, .3);
            margin: auto;
            color: var(--body)
        }

        dialog::backdrop {
            background: rgba(43, 33, 24, .5);
            backdrop-filter: blur(3px)
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: -24px -24px 16px;
            padding: 16px 24px;
            color: #fff;
            background: var(--grad-brand);
            border-radius: 20px 20px 0 0
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            overflow-wrap: anywhere
        }

        .modal-close {
            background: rgba(255, 255, 255, .2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            color: #fff;
            line-height: 1;
            flex-shrink: 0
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, .34)
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 12px;
            font-size: 13px
        }

        .detail-row .lbl {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted)
        }

        .detail-row .val {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--heading);
            overflow-wrap: anywhere
        }

        .diff {
            width: 100%;
            font-size: 12.5px
        }

        .diff th,
        .diff td {
            padding: 8px 10px;
            vertical-align: top;
            overflow-wrap: anywhere
        }

        .diff .old {
            color: #cf1322;
            background: #fff6f5
        }

        .diff .new {
            color: #15803d;
            background: #f3fcf7
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none
            }

            .nav-open .sidebar {
                transform: translateX(0);
                box-shadow: 0 0 60px rgba(0, 0, 0, .25)
            }

            .nav-open .scrim {
                opacity: 1;
                pointer-events: auto
            }

            .nav-open {
                overflow: hidden
            }

            .wrap {
                margin-left: 0
            }

            .hamburger {
                display: inline-flex
            }

            .topbar {
                padding: 0 20px
            }

            .content {
                padding: 22px 20px 48px
            }
        }

        @media (max-width: 720px) {
            .content {
                padding: 16px 16px 40px;
                gap: 18px
            }

            .topbar {
                padding: 0 14px
            }

            .hero {
                padding: 20px;
                border-radius: 16px
            }

            .page-title {
                font-size: 20px
            }

            .hero .btn {
                width: 100%
            }

            .hero>div:last-child {
                width: 100%
            }

            dialog {
                width: 94vw;
                padding: 18px
            }

            .modal-header {
                margin: -18px -18px 14px;
                padding: 14px 18px
            }

            table.responsive thead {
                display: none
            }

            table.responsive,
            table.responsive tbody,
            table.responsive tr,
            table.responsive td {
                display: block;
                width: 100%
            }

            table.responsive tr {
                padding: 12px 16px;
                border-bottom: 1px solid #f3ede6
            }

            table.responsive tr:hover {
                background: transparent
            }

            table.responsive td {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
                padding: 7px 0 !important;
                border: none;
                text-align: right
            }

            table.responsive td::before {
                content: attr(data-label);
                font-size: 11.5px;
                font-weight: 700;
                color: var(--muted);
                text-align: left;
                flex-shrink: 0;
                padding-top: 2px
            }

            table.responsive td[data-label=""]::before {
                display: none
            }

            table.responsive td.stack {
                flex-direction: column;
                text-align: left
            }

            table.responsive td .actions {
                justify-content: flex-start !important
            }

            table.responsive td.empty {
                display: block;
                text-align: center
            }

            ul.pagination {
                justify-content: center
            }

            .pager {
                padding: 14px 16px
            }
        }

        @media (max-width: 480px) {
            .btn-logout span {
                display: none
            }

            .btn-logout {
                padding: 9px 11px
            }

            .topbar-title span {
                display: none
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                transition: none !important;
                scroll-behavior: auto !important
            }
        }
    </style>

    {{-- CSS khusus per halaman (@push('styles')) dimuat setelah CSS dasar --}}
    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @php
        $sp = session('supabase.profile', []);
        $su = session('supabase.user', []);
        $name = $sp['full_name'] ?? $sp['username'] ?? ($su['email'] ?? 'Superadmin');
        $email = $su['email'] ?? '';
        $pendingCount = 0;
        try {
            $pendingCount = \Illuminate\Support\Facades\DB::table('mitras')->where('status', 'pending')->count();
        } catch (\Throwable $e) {
        }
    @endphp

    <div class="scrim" id="scrim" aria-hidden="true"></div>

    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <a href="{{ route('superadmin.dashboard') }}" class="brand">
            <img src="{{ asset('assets/icon/pawku-nobg.png') }}" alt="PawKu"
                onerror="this.onerror=null;this.src='https://raw.githubusercontent.com/fashaputri/asset/main/pawku.png';">
            <div>
                <span class="brand-name">PawKu</span>
                <span class="brand-badge">SUPERADMIN</span>
            </div>
        </a>

        <nav class="menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('superadmin.dashboard') }}"
                class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard Monitoring</span>
            </a>
            <a href="{{ route('superadmin.petshops') }}"
                class="{{ request()->routeIs('superadmin.petshops*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>Persetujuan Petshop</span>
                @if($pendingCount > 0)
                    <span class="badge">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('superadmin.admins') }}"
                class="{{ request()->routeIs('superadmin.admins*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span>Kelola Admin</span>
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
                    <span class="me-name">{{ $name }}</span>
                    @if(!empty($email))
                        <span class="me-mail">{{ $email }}</span>
                    @endif
                    <span class="me-role">Super Admin</span>
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
                    Portal Superadmin <span>/ @yield('title')</span>
                </div>
            </div>
            <div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                <button class="btn-logout" type="button" onclick="confirmLogout()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
            </div>
        </header>

        <main class="content">
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
                text: 'Sesi superadmin Anda akan dihapus dan Anda harus login kembali.',
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

        function confirmAction(formEl, message, isDanger) {
            Swal.fire({
                title: 'Konfirmasi Aksi',
                text: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#ef4444' : '#F59E3D',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @js(session('success')),
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @js(session('error')),
                    timer: 4000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
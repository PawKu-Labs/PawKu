@extends('owner.layout')
@section('title', 'Kalkulator Jadwal Vaksin')

@section('content')
<style>
    :root {
        --vx-primary: #0d9488;
        --vx-primary-dark: #0f766e;
        --vx-primary-light: #ccfbf1;
        --vx-primary-soft: #f0fdfa;
        --vx-accent: #f59e0b;
        --vx-accent-soft: #fef3c7;
        --vx-navy: #0f172a;
        --vx-slate: #334155;
        --vx-card-bg: #ffffff;
        --vx-border: #e2e8f0;
        --vx-radius-lg: 16px;
        --vx-radius-md: 12px;
        --vx-radius-sm: 8px;
        --vx-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        --vx-shadow-md: 0 4px 14px -2px rgba(13, 148, 136, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
        --vx-shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
    }

    .vx-wrapper {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    /* Hero Banner */
    .vx-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 55%, #14b8a6 100%);
        border-radius: var(--vx-radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        box-shadow: 0 12px 28px -6px rgba(13, 148, 136, 0.28);
    }

    .vx-hero::after {
        content: "";
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .vx-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .vx-hero-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 26px;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 8px;
    }

    .vx-hero-sub {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.9);
        max-width: 680px;
        line-height: 1.55;
    }

    .vx-hero-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
    }

    .vx-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.15);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    /* Sections */
    .vx-section-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--vx-navy);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .vx-step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--vx-primary-light);
        color: var(--vx-primary-dark);
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Cat Selection Cards Grid */
    .vx-cat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }

    .vx-cat-item {
        background: var(--vx-card-bg);
        border: 2px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.22s ease;
        position: relative;
    }

    .vx-cat-item:hover {
        border-color: #99f6e4;
        transform: translateY(-2px);
        box-shadow: var(--vx-shadow-md);
    }

    .vx-cat-item.active {
        border-color: var(--vx-primary);
        background: var(--vx-primary-soft);
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.15);
    }

    .vx-cat-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .vx-cat-item.active .vx-cat-avatar {
        border-color: var(--vx-primary);
    }

    .vx-cat-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .vx-cat-details {
        flex: 1;
        min-width: 0;
    }

    .vx-cat-name {
        font-weight: 700;
        font-size: 15px;
        color: var(--vx-navy);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .vx-cat-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .vx-cat-age-pill {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        margin-top: 4px;
        background: #e2e8f0;
        color: #475569;
    }

    .vx-cat-item.active .vx-cat-age-pill {
        background: var(--vx-primary-light);
        color: var(--vx-primary-dark);
    }

    .vx-selected-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--vx-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .vx-cat-item.active .vx-selected-badge {
        display: flex;
    }

    /* Active Cat Overview Card */
    .vx-active-cat-box {
        background: var(--vx-card-bg);
        border: 1px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: var(--vx-shadow-sm);
        margin-top: 12px;
    }

    .vx-active-cat-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .vx-stat-tag {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
    }

    .vx-tag-green {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .vx-tag-amber {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Option Cards (Status Vaksinasi) */
    .vx-mode-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media(max-width: 720px) {
        .vx-mode-grid {
            grid-template-columns: 1fr;
        }
    }

    .vx-mode-card {
        background: var(--vx-card-bg);
        border: 2px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .vx-mode-card:hover {
        border-color: #99f6e4;
        box-shadow: var(--vx-shadow-md);
    }

    .vx-mode-card.active {
        border-color: var(--vx-primary);
        background: #fbfefd;
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.12);
    }

    .vx-mode-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vx-mode-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f1f5f9;
        color: var(--vx-slate);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: all 0.2s;
    }

    .vx-mode-card.active .vx-mode-icon {
        background: var(--vx-primary-light);
        color: var(--vx-primary-dark);
    }

    .vx-radio-mark {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .vx-mode-card.active .vx-radio-mark {
        border-color: var(--vx-primary);
        background: var(--vx-primary);
    }

    .vx-radio-mark::after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ffffff;
        display: none;
    }

    .vx-mode-card.active .vx-radio-mark::after {
        display: block;
    }

    .vx-mode-name {
        font-family: 'Montserrat', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--vx-navy);
    }

    .vx-mode-desc {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.5;
    }

    /* Booster Date Picker Card */
    .vx-date-box {
        background: var(--vx-card-bg);
        border: 1px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 20px 24px;
        box-shadow: var(--vx-shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .vx-input-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .vx-date-input {
        flex: 1;
        min-width: 240px;
        padding: 12px 16px;
        border: 2px solid var(--vx-border);
        border-radius: var(--vx-radius-sm);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
        background: #ffffff;
        color: var(--vx-navy);
    }

    .vx-date-input:focus {
        border-color: var(--vx-primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .vx-preset-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid var(--vx-border);
        background: #f8fafc;
        color: var(--vx-slate);
        cursor: pointer;
        transition: all 0.15s;
    }

    .vx-preset-btn:hover {
        background: var(--vx-primary-light);
        border-color: var(--vx-primary);
        color: var(--vx-primary-dark);
    }

    /* Hitung Button */
    .vx-btn-calc {
        width: 100%;
        padding: 16px 24px;
        border-radius: var(--vx-radius-md);
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: #ffffff;
        font-family: 'Montserrat', sans-serif;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.3px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.35);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .vx-btn-calc:hover {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        box-shadow: 0 10px 24px -2px rgba(13, 148, 136, 0.45);
        transform: translateY(-1px);
    }

    .vx-btn-calc:active {
        transform: translateY(1px);
    }

    /* Results Container */
    #vx-results {
        display: none;
        flex-direction: column;
        gap: 24px;
        animation: vxFadeIn 0.35s ease forwards;
    }

    @keyframes vxFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .vx-results-header {
        background: var(--vx-card-bg);
        border: 1px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 22px 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: var(--vx-shadow-sm);
    }

    .vx-results-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--vx-navy);
    }

    .vx-results-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .vx-results-actions {
        display: flex;
        gap: 8px;
    }

    .vx-action-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid var(--vx-border);
        background: #ffffff;
        color: var(--vx-slate);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }

    .vx-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    /* Timeline Stepper UI */
    .vx-timeline {
        position: relative;
        padding-left: 36px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .vx-timeline::before {
        content: "";
        position: absolute;
        left: 14px;
        top: 24px;
        bottom: 24px;
        width: 3px;
        background: #ccfbf1;
        border-radius: 2px;
    }

    .vx-timeline-step {
        position: relative;
        background: var(--vx-card-bg);
        border: 1px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 20px 24px;
        box-shadow: var(--vx-shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .vx-timeline-step:hover {
        transform: translateX(4px);
        box-shadow: var(--vx-shadow-md);
    }

    .vx-timeline-step.upcoming {
        border-left: 4px solid var(--vx-primary);
    }

    .vx-timeline-step.past {
        border-left: 4px solid #94a3b8;
        opacity: 0.88;
    }

    .vx-timeline-step.highlight {
        border-left: 4px solid #f59e0b;
        background: #fffdf9;
    }

    /* Timeline Indicator Node */
    .vx-node {
        position: absolute;
        left: -36px;
        top: 22px;
        width: 31px;
        height: 31px;
        border-radius: 50%;
        background: #ffffff;
        border: 3px solid var(--vx-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        color: var(--vx-primary);
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
    }

    .vx-timeline-step.past .vx-node {
        border-color: #94a3b8;
        color: #94a3b8;
        box-shadow: none;
    }

    .vx-timeline-step.highlight .vx-node {
        border-color: #f59e0b;
        color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
    }

    .vx-step-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 8px;
    }

    .vx-step-tag {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .vx-tag-teal {
        background: var(--vx-primary-light);
        color: var(--vx-primary-dark);
    }

    .vx-tag-amber {
        background: #fef3c7;
        color: #b45309;
    }

    .vx-tag-gray {
        background: #f1f5f9;
        color: #64748b;
    }

    .vx-step-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--vx-navy);
    }

    .vx-step-date-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--vx-navy);
        margin: 10px 0;
        width: fit-content;
    }

    .vx-step-date-box svg {
        color: var(--vx-primary);
    }

    .vx-step-desc {
        font-size: 13px;
        color: #475569;
        line-height: 1.55;
    }

    .vx-disease-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }

    .vx-disease-pill {
        font-size: 11.5px;
        padding: 3px 10px;
        background: #f1f5f9;
        color: #334155;
        border-radius: 6px;
        font-weight: 500;
    }

    /* Disclaimer Section */
    .vx-disclaimer-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: var(--vx-radius-md);
        padding: 22px 24px;
        color: #92400e;
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }

    .vx-disclaimer-icon {
        font-size: 26px;
        line-height: 1;
        flex-shrink: 0;
    }

    .vx-disclaimer-content h4 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #78350f;
    }

    .vx-disclaimer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .vx-disclaimer-item {
        background: rgba(255, 255, 255, 0.7);
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1.45;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Manual Cat Input Fallback */
    .vx-manual-card {
        background: var(--vx-card-bg);
        border: 1px solid var(--vx-border);
        border-radius: var(--vx-radius-md);
        padding: 24px;
        box-shadow: var(--vx-shadow-sm);
    }

    /* Print View Rules */
    @media print {
        .sidebar, .topbar, .vx-hero-chips, .vx-btn-calc, .vx-results-actions, .vx-mode-grid, .vx-cat-grid, .vx-section-title {
            display: none !important;
        }
        .wrap {
            margin-left: 0 !important;
        }
        .content {
            padding: 0 !important;
        }
        .vx-hero {
            background: #0d9488 !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
        }
        #vx-results {
            display: flex !important;
        }
    }
</style>

<div class="vx-wrapper">
    <!-- HERO HEADER -->
    <div class="vx-hero">
        <div class="vx-badge-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Standar Medis WSAVA</span>
        </div>
        <h1 class="vx-hero-title">Kalkulator Jadwal Vaksin Kucing 💉</h1>
        <p class="vx-hero-sub">
            Rencanakan vaksinasi anabul dengan presisi. Kalkulator ini secara otomatis menghitung usia dan tanggal optimal pemberian vaksin F3, F4, hingga Rabies untuk daya tahan tubuh maksimal.
        </p>
        <div class="vx-hero-chips">
            <span class="vx-chip">🐾 Vaksin F3 (Tricat)</span>
            <span class="vx-chip">🛡️ Vaksin F4 (Tetracat)</span>
            <span class="vx-chip">💉 Vaksin Rabies Wajib</span>
            <span class="vx-chip">⏳ Booster Rutin Tahunan</span>
        </div>
    </div>

    <!-- STEP 1: PILIH KUCING -->
    <div>
        <div class="vx-section-title">
            <span class="vx-step-num">1</span>
            <span>Pilih Kucing Pasien</span>
        </div>

        @if ($cats->isNotEmpty())
            <div class="vx-cat-grid">
                @foreach ($cats as $cat)
                    <div class="vx-cat-item {{ ($selectedCat->id ?? null) === $cat->id ? 'active' : '' }}"
                         onclick="selectCat('{{ $cat->id }}')"
                         id="cat-card-{{ $cat->id }}"
                         data-id="{{ $cat->id }}"
                         data-name="{{ $cat->name }}"
                         data-birth="{{ $cat->birth_date ?? '' }}"
                         data-breed="{{ $cat->breed ?? 'Kucing Peliharaan' }}"
                         data-photo="{{ $cat->photo_url ?? '' }}">
                        <div class="vx-selected-badge">✓</div>
                        <div class="vx-cat-avatar">
                            @if (!empty($cat->photo_url))
                                <img src="{{ $cat->photo_url }}" alt="{{ $cat->name }}">
                            @else
                                🐾
                            @endif
                        </div>
                        <div class="vx-cat-details">
                            <div class="vx-cat-name">{{ $cat->name }}</div>
                            <div class="vx-cat-sub">{{ $cat->breed ?? 'Kucing Domestik' }}</div>
                            @if(!empty($cat->birth_date))
                                <span class="vx-cat-age-pill" id="cat-pill-{{ $cat->id }}">
                                    {{ \Carbon\Carbon::parse($cat->birth_date)->age > 0 ? \Carbon\Carbon::parse($cat->birth_date)->age . ' Tahun' : \Carbon\Carbon::parse($cat->birth_date)->diffInMonths(now()) . ' Bulan' }}
                                </span>
                            @else
                                <span class="vx-cat-age-pill" style="color:var(--danger); background:#fee2e2;">
                                    Tgl Lahir Kosong
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Active Cat Overview Details -->
            <div class="vx-active-cat-box" id="active-cat-box">
                <div class="vx-active-cat-left">
                    <div style="font-size:32px;">🐱</div>
                    <div>
                        <div style="font-size:15px; font-weight:700; color:var(--vx-navy);" id="current-cat-name-display">
                            {{ $selectedCat->name ?? 'Kucing Terpilih' }}
                        </div>
                        <div style="font-size:13px; color:#64748b; margin-top:2px;" id="current-cat-birth-display">
                            @if(!empty($selectedCat->birth_date))
                                Lahir: {{ \Carbon\Carbon::parse($selectedCat->birth_date)->translatedFormat('d F Y') }}
                            @else
                                <span style="color:#ef4444; font-weight:600;">Tanggal lahir belum diisi di profil kucing</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <span class="vx-stat-tag {{ !empty($selectedCat->birth_date) ? 'vx-tag-green' : 'vx-tag-amber' }}" id="current-cat-status-tag">
                        {{ !empty($selectedCat->birth_date) ? 'Profil Siap' : 'Perlu Tanggal Lahir' }}
                    </span>
                </div>
            </div>

            <!-- Tanggal Lahir Fallback jika profil kucing terpilih belum memiliki tanggal lahir -->
            <div id="missing-birth-alert" style="{{ empty($selectedCat->birth_date) ? 'display:block;' : 'display:none;' }} margin-top:12px;">
                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="font-weight:700; font-size:13.5px; color:#92400e;">⚠️ Lengkapi Tanggal Lahir Sementara</div>
                        <div style="font-size:12.5px; color:#b45309;">Kucing ini belum memiliki tanggal lahir di profil. Masukkan di bawah untuk kalkulasi:</div>
                    </div>
                    <div>
                        <input type="date" id="temporary-birth-date" class="vx-date-input" style="padding:8px 12px; font-size:13px;" max="{{ now()->format('Y-m-d') }}" value="{{ now()->subWeeks(8)->format('Y-m-d') }}" onchange="updateTemporaryBirth(this.value)">
                    </div>
                </div>
            </div>
        @else
            <!-- Form Manual Jika Pemilik Belum Menambahkan Kucing -->
            <div class="vx-manual-card">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                    <div style="font-size:36px;">🐾</div>
                    <div>
                        <div style="font-weight:700; font-size:15px; color:var(--vx-navy);">Belum ada profil kucing terdaftar</div>
                        <div style="font-size:13px; color:#64748b;">Ketik nama dan tanggal lahir anabul untuk menghitung estimasi jadwal:</div>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                    <div>
                        <label style="font-size:12.5px; font-weight:600; color:var(--vx-navy); display:block; margin-bottom:6px;">Nama Kucing</label>
                        <input type="text" id="manual-cat-name" class="vx-date-input" placeholder="Contoh: Milo" value="Kucing Saya">
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; color:var(--vx-navy); display:block; margin-bottom:6px;">Tanggal Lahir</label>
                        <input type="date" id="manual-cat-birth" class="vx-date-input" max="{{ now()->format('Y-m-d') }}" value="{{ now()->subWeeks(8)->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- STEP 2: STATUS VAKSINASI -->
    <div>
        <div class="vx-section-title">
            <span class="vx-step-num">2</span>
            <span>Status Vaksinasi Saat Ini</span>
        </div>

        <div class="vx-mode-grid">
            <!-- Mode 1: Anak Kucing / Belum Pernah -->
            <div class="vx-mode-card active" id="mode-card-baru" onclick="switchMode('baru')">
                <div class="vx-mode-header">
                    <div class="vx-mode-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div class="vx-radio-mark"></div>
                </div>
                <div>
                    <div class="vx-mode-name">Belum Pernah Vaksinasi</div>
                    <div class="vx-mode-desc">Rangkaian imunisasi dasar untuk anak kucing (kitten) mulai usia 8 minggu hingga booster primer.</div>
                </div>
            </div>

            <!-- Mode 2: Booster Rutin Tahunan -->
            <div class="vx-mode-card" id="mode-card-booster" onclick="switchMode('booster')">
                <div class="vx-mode-header">
                    <div class="vx-mode-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                        </svg>
                    </div>
                    <div class="vx-radio-mark"></div>
                </div>
                <div>
                    <div class="vx-mode-name">Booster Rutin Tahunan</div>
                    <div class="vx-mode-desc">Untuk kucing yang sudah pernah divaksin lengkap, memerlukan pengulangan tahunan (F4 & Rabies).</div>
                </div>
            </div>
        </div>

        <!-- Input Tanggal Vaksin Terakhir (Hanya Muncul di Mode Booster) -->
        <div id="booster-date-panel" style="display:none; margin-top:16px;">
            <div class="vx-date-box">
                <div>
                    <label style="font-size:13.5px; font-weight:700; color:var(--vx-navy); display:block; margin-bottom:4px;">
                        Tanggal Vaksinasi Terakhir
                    </label>
                    <p style="font-size:12.5px; color:#64748b; margin-bottom:12px;">
                        Kapan terakhir kali anabul Anda disuntik vaksin tahunan?
                    </p>
                </div>
                <div class="vx-input-row">
                    <input type="date" id="input-last-vaccine" class="vx-date-input" max="{{ now()->format('Y-m-d') }}">
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" class="vx-preset-btn" onclick="setPresetDate(365)">1 Tahun Lalu</button>
                        <button type="button" class="vx-preset-btn" onclick="setPresetDate(180)">6 Bulan Lalu</button>
                        <button type="button" class="vx-preset-btn" onclick="setPresetDate(30)">1 Bulan Lalu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3: ACTION BUTTON -->
    <div>
        <button type="button" class="vx-btn-calc" onclick="generateSchedule()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="4" y="2" width="16" height="20" rx="2"/>
                <line x1="8" y1="6" x2="16" y2="6"/>
                <line x1="16" y1="14" x2="16" y2="18"/>
                <path d="M16 10h.01M12 10h.01M8 10h.01M12 14h.01M8 14h.01M12 18h.01M8 18h.01"/>
            </svg>
            <span>Hitung Rekomendasi Jadwal Vaksin</span>
        </button>
    </div>

    <!-- HASIL KALKULASI TIMELINE -->
    <div id="vx-results">
        <div class="vx-results-header">
            <div>
                <div class="vx-results-title" id="results-target-cat">Jadwal Vaksinasi Kucing</div>
                <div class="vx-results-sub" id="results-summary-text">
                    Rekomendasi terstruktur berdasarkan standar World Small Animal Veterinary Association.
                </div>
            </div>
            <div class="vx-results-actions">
                <button type="button" class="vx-action-btn" onclick="copyScheduleSummary()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    <span>Salin Rangkuman</span>
                </button>
                <button type="button" class="vx-action-btn" onclick="window.print()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    <span>Cetak Jadwal</span>
                </button>
            </div>
        </div>

        <!-- TIMELINE LIST -->
        <div class="vx-timeline" id="timeline-container">
            <!-- Diisi lewat JavaScript -->
        </div>

        <!-- DISCLAIMER & CLINICAL GUIDELINES -->
        <div class="vx-disclaimer-card">
            <div class="vx-disclaimer-icon">⚠️</div>
            <div class="vx-disclaimer-content" style="flex:1;">
                <h4>Syarat & Sanggahan Medis Klinis (WSAVA)</h4>
                <p style="font-size:12.5px; line-height:1.5;">
                    Vaksinasi hanya boleh diberikan apabila kondisi fisik kucing memenuhi syarat berikut:
                </p>
                <div class="vx-disclaimer-grid">
                    <div class="vx-disclaimer-item">
                        <span>⚖️</span>
                        <span><strong>Berat Badan:</strong> Minimal 800 g – 1.0 kg</span>
                    </div>
                    <div class="vx-disclaimer-item">
                        <span>🌡️</span>
                        <span><strong>Suhu Tubuh:</strong> Normal (38.0°C – 39.2°C)</span>
                    </div>
                    <div class="vx-disclaimer-item">
                        <span>🩺</span>
                        <span><strong>Bebas Gejala:</strong> Tidak diare, flu, bersin, atau jamuran</span>
                    </div>
                    <div class="vx-disclaimer-item">
                        <span>👩‍⚕️</span>
                        <span><strong>Dokter Hewan:</strong> Keputusan akhir tetap oleh dokter hewan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // State Aplikasi
    const hasRegisteredCats = @js($cats->isNotEmpty());
    let catsList = @js($cats);
    let selectedCat = @js($selectedCat ?? null);
    let currentMode = 'baru'; // 'baru' | 'booster'
    let lastCalculatedSchedules = [];

    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function formatDateIndo(dateObj) {
        return `${dateObj.getDate()} ${monthNames[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
    }

    function addDays(d, days) {
        const res = new Date(d);
        res.setDate(res.getDate() + days);
        return res;
    }

    // Ganti Kucing
    function selectCat(catId) {
        document.querySelectorAll('.vx-cat-item').forEach(el => el.classList.remove('active'));
        const activeCard = document.getElementById(`cat-card-${catId}`);
        if (activeCard) {
            activeCard.classList.add('active');
        }

        selectedCat = catsList.find(c => c.id == catId);
        if (!selectedCat) return;

        // Update display overview
        document.getElementById('current-cat-name-display').textContent = selectedCat.name;
        const birthDisplay = document.getElementById('current-cat-birth-display');
        const tagStatus = document.getElementById('current-cat-status-tag');
        const missingAlert = document.getElementById('missing-birth-alert');

        if (selectedCat.birth_date) {
            const b = new Date(selectedCat.birth_date);
            birthDisplay.innerHTML = `Lahir: <strong>${formatDateIndo(b)}</strong>`;
            tagStatus.className = 'vx-stat-tag vx-tag-green';
            tagStatus.textContent = 'Profil Siap';
            missingAlert.style.display = 'none';
        } else {
            birthDisplay.innerHTML = `<span style="color:#ef4444; font-weight:600;">Tanggal lahir belum diisi di profil kucing</span>`;
            tagStatus.className = 'vx-stat-tag vx-tag-amber';
            tagStatus.textContent = 'Perlu Tanggal Lahir';
            missingAlert.style.display = 'block';
        }

        // Sembunyikan hasil lama saat ganti kucing
        document.getElementById('vx-results').style.display = 'none';
    }

    function updateTemporaryBirth(val) {
        if (selectedCat) {
            selectedCat.birth_date = val;
            const b = new Date(val);
            document.getElementById('current-cat-birth-display').innerHTML = `Lahir Sementara: <strong>${formatDateIndo(b)}</strong>`;
            document.getElementById('current-cat-status-tag').className = 'vx-stat-tag vx-tag-green';
            document.getElementById('current-cat-status-tag').textContent = 'Tanggal Terisi';
        }
    }

    // Ganti Mode
    function switchMode(mode) {
        currentMode = mode;
        document.getElementById('mode-card-baru').classList.toggle('active', mode === 'baru');
        document.getElementById('mode-card-booster').classList.toggle('active', mode === 'booster');
        document.getElementById('booster-date-panel').style.display = mode === 'booster' ? 'block' : 'none';

        // Sembunyikan hasil lama
        document.getElementById('vx-results').style.display = 'none';
    }

    // Preset tanggal booster
    function setPresetDate(daysAgo) {
        const d = new Date();
        d.setDate(d.getDate() - daysAgo);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        document.getElementById('input-last-vaccine').value = `${yyyy}-${mm}-${dd}`;
    }

    // Hitung Jadwal
    function generateSchedule() {
        let catName = 'Kucing';
        let birthStr = null;

        if (hasRegisteredCats) {
            if (!selectedCat) {
                Swal.fire({ icon: 'warning', title: 'Pilih Kucing', text: 'Silakan pilih kucing terlebih dahulu.', confirmButtonColor: '#0d9488' });
                return;
            }
            catName = selectedCat.name;
            birthStr = selectedCat.birth_date;

            // Jika masih kosong, cek input temporary
            if (!birthStr) {
                const tempInput = document.getElementById('temporary-birth-date');
                if (tempInput && tempInput.value) {
                    birthStr = tempInput.value;
                }
            }
        } else {
            catName = document.getElementById('manual-cat-name').value.trim() || 'Kucing';
            birthStr = document.getElementById('manual-cat-birth').value;
        }

        const now = new Date();
        let schedules = [];

        if (currentMode === 'baru') {
            if (!birthStr) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Lahir Belum Diisi',
                    text: `Tanggal lahir ${catName} wajib diisi untuk menghitung usia awal vaksinasi.`,
                    confirmButtonColor: '#0d9488'
                });
                return;
            }

            const birth = new Date(birthStr);
            if (isNaN(birth.getTime())) {
                Swal.fire({ icon: 'error', title: 'Tanggal Tidak Valid', text: 'Format tanggal lahir tidak valid.', confirmButtonColor: '#0d9488' });
                return;
            }

            // Perhitungan Persis Sesuai Dart & Standar WSAVA
            const v1 = addDays(birth, 56);       // 8 minggu (56 hari)
            const v2 = addDays(v1, 28);          // 4 minggu setelah V1
            const rabies = addDays(birth, 84);   // 12 minggu (84 hari)
            const boosterKitten = addDays(v2, 28); // 4 minggu setelah V2

            schedules = [
                {
                    stepNum: 1,
                    title: 'Vaksin Tahap 1 (F3 / Tricat)',
                    date: v1,
                    tag: 'Usia ~8 Minggu',
                    tagClass: 'vx-tag-teal',
                    desc: 'Mencegah Feline Panleukopenia (distemper), Feline Viral Rhinotracheitis, dan Feline Calicivirus.',
                    diseases: ['Panleukopenia', 'Calicivirus', 'Rhinotracheitis']
                },
                {
                    stepNum: 2,
                    title: 'Vaksin Rabies',
                    date: rabies,
                    tag: 'Usia ~12 Minggu',
                    tagClass: 'vx-tag-amber',
                    desc: 'Wajib diberikan mulai usia minimal 12 minggu untuk mencegah infeksi virus Rabies pada kucing dan manusia.',
                    diseases: ['Rabies (Zoonosis)']
                },
                {
                    stepNum: 3,
                    title: 'Vaksin Tahap 2 (F4 / Tetracat)',
                    date: v2,
                    tag: 'Usia ~12-14 Minggu',
                    tagClass: 'vx-tag-teal',
                    desc: 'Penguat antibodi F3 sekaligus perlindungan tambahan terhadap bakteri Chlamydia psittaci (klamidiosis).',
                    diseases: ['F3 Booster', 'Chlamydia psittaci']
                },
                {
                    stepNum: 4,
                    title: 'Booster Rangkaian Dasar (Kitten Booster)',
                    date: boosterKitten,
                    tag: 'Usia ~16 Minggu',
                    tagClass: 'vx-tag-teal',
                    desc: 'Memastikan puncak titer antibodi terbentuk optimal sebelum beralih ke jadwal pengulangan tahunan.',
                    diseases: ['Antibodi Maksimal', 'Proteksi Penuh']
                }
            ];

            // Urutkan berdasarkan tanggal ascending
            schedules.sort((a, b) => a.date - b.date);

        } else {
            // Mode Booster
            const lastDateStr = document.getElementById('input-last-vaccine').value;
            if (!lastDateStr) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Tanggal Terakhir',
                    text: 'Pilih tanggal vaksinasi terakhir kucing Anda terlebih dahulu.',
                    confirmButtonColor: '#0d9488'
                });
                return;
            }

            const lastDate = new Date(lastDateStr);
            if (isNaN(lastDate.getTime())) {
                Swal.fire({ icon: 'error', title: 'Tanggal Tidak Valid', text: 'Format tanggal vaksin terakhir tidak valid.', confirmButtonColor: '#0d9488' });
                return;
            }

            const boosterDate = addDays(lastDate, 365);

            schedules = [
                {
                    stepNum: 1,
                    title: 'Booster Rutin Tahunan (F4 & Rabies)',
                    date: boosterDate,
                    tag: 'Setiap 1 Tahun Sekali',
                    tagClass: 'vx-tag-teal',
                    desc: 'Pengulangan rutin tahunan untuk mempertahankan titer antibodi terhadap Panleukopenia, Calici, Rhino, Chlamydia, dan Rabies.',
                    diseases: ['F4 Tetracat', 'Rabies', 'Perpanjangan Imunitas']
                }
            ];
        }

        lastCalculatedSchedules = schedules;
        renderTimeline(catName, schedules);
    }

    function renderTimeline(catName, schedules) {
        const resultsEl = document.getElementById('vx-results');
        const titleEl = document.getElementById('results-target-cat');
        const summaryText = document.getElementById('results-summary-text');
        const timelineList = document.getElementById('timeline-container');

        titleEl.textContent = `Jadwal Vaksinasi untuk ${catName}`;
        summaryText.textContent = currentMode === 'baru'
            ? `Terdapat ${schedules.length} tahap imunisasi primer untuk membentuk antibodi komprehensif.`
            : `Rekomendasi tanggal suntik booster pengulangan tahunan berikutnya.`;

        timelineList.innerHTML = '';
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        schedules.forEach((item, idx) => {
            const itemDate = new Date(item.date);
            itemDate.setHours(0, 0, 0, 0);

            const isPast = itemDate < today;
            const diffDays = Math.round((itemDate - today) / (1000 * 60 * 60 * 24));

            let statusLabel = '';
            let stepClass = 'upcoming';

            if (isPast) {
                stepClass = 'past';
                statusLabel = `<span style="font-size:11px; font-weight:700; color:#64748b; background:#f1f5f9; padding:2px 8px; border-radius:10px;">✓ Sudah Lewat Waktunya</span>`;
            } else if (diffDays <= 14) {
                stepClass = 'highlight';
                statusLabel = `<span style="font-size:11px; font-weight:700; color:#b45309; background:#fef3c7; padding:2px 8px; border-radius:10px;">🔥 Jadwal Terdekat (${diffDays === 0 ? 'Hari ini!' : diffDays + ' hari lagi'})</span>`;
            } else {
                statusLabel = `<span style="font-size:11px; font-weight:700; color:#065f46; background:#ecfdf5; padding:2px 8px; border-radius:10px;">⏳ Mendatang (${diffDays} hari lagi)</span>`;
            }

            const stepEl = document.createElement('div');
            stepEl.className = `vx-timeline-step ${stepClass}`;
            stepEl.innerHTML = `
                <div class="vx-node">${idx + 1}</div>
                <div class="vx-step-top">
                    <span class="vx-step-tag ${item.tagClass}">${item.tag}</span>
                    ${statusLabel}
                </div>
                <div class="vx-step-title">${item.title}</div>
                <div class="vx-step-date-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>Rekomendasi Tanggal: ${formatDateIndo(item.date)}</span>
                </div>
                <p class="vx-step-desc">${item.desc}</p>
                <div class="vx-disease-pills">
                    ${item.diseases.map(d => `<span class="vx-disease-pill">🛡️ ${d}</span>`).join('')}
                </div>
            `;
            timelineList.appendChild(stepEl);
        });

        resultsEl.style.display = 'flex';
        resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function copyScheduleSummary() {
        if (!lastCalculatedSchedules || lastCalculatedSchedules.length === 0) return;
        const catName = (selectedCat ? selectedCat.name : 'Kucing');

        let text = `Jadwal Vaksinasi PawKu untuk ${catName}:\n\n`;
        lastCalculatedSchedules.forEach((s, i) => {
            text += `${i + 1}. ${s.title}\n   Rekomendasi: ${formatDateIndo(s.date)} (${s.tag})\n   Target: ${s.desc}\n\n`;
        });
        text += `Catatan: Kucing harus sehat (BB min 800g, suhu normal 38°C-39°C). Sesuai standar WSAVA.`;

        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Rangkuman jadwal berhasil disalin!',
                showConfirmButton: false,
                timer: 2500
            });
        }).catch(() => {
            Swal.fire({ icon: 'info', title: 'Salin Teks', text: text });
        });
    }
</script>
@endsection

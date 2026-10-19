@extends('owner.layout')
@section('title', $mitra ? 'Perbaiki Pengajuan' : 'Daftarkan Usaha')

@section('content')
    @php
        $isEdit = (bool) $mitra;
        $action = $isEdit ? route('owner.mitra.update', $mitra->id) : route('owner.mitra.store');
        $hadOld = count(session()->getOldInput()) > 0;

        // Langkah pertama yang punya error (supaya wizard langsung membuka langkah itu)
        $startStep = 0;
        if ($errors->any()) {
            $map = [
                3 => ['hours'],
                2 => ['address', 'latitude', 'longitude', 'province', 'city', 'district'],
                1 => ['name', 'description', 'rules_text', 'photos', 'logo', 'owner_note'],
                0 => ['mitra_type'],
            ];
            foreach ($map as $step => $keys) {
                foreach ($keys as $k) {
                    if ($errors->has($k) || $errors->has($k . '.*')) {
                        $startStep = $step;
                    }
                }
            }
            foreach ($errors->keys() as $k) {
                if (str_starts_with($k, 'hours')) {
                    $startStep = max($startStep, 3);
                }
                if (str_starts_with($k, 'photos')) {
                    $startStep = max($startStep, 1);
                }
            }
        }

        $selType = old('mitra_type', $mitra->mitra_type ?? '');
        $typeIcons = [
            'PAW_SHOP' => 'fa-store',
            'PAW_HEALTH' => 'fa-stethoscope',
            'PAW_HOTEL' => 'fa-hotel',
            'PAW_GROOMING' => 'fa-scissors',
        ];
        $lat = old('latitude', $mitra->latitude ?? '');
        $lng = old('longitude', $mitra->longitude ?? '');
        $steps = ['Jenis Usaha', 'Data Usaha', 'Lokasi', 'Jam Operasional'];
    @endphp

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .w-wrap {
            max-width: 1040px;
            margin: 0 auto;
            --w-radius: 12px
        }

        .w-wrap * {
            box-sizing: border-box
        }

        /* ---------- Stepper ---------- */
        .w-dots {
            display: flex;
            align-items: flex-start;
            margin: 24px 0 22px
        }

        .w-s {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 64px;
            flex-shrink: 0
        }

        .w-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            transition: all .2s
        }

        .w-dot.on {
            border-color: var(--primary);
            color: var(--primary)
        }

        .w-dot.cur {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff
        }

        .w-dot svg {
            width: 14px;
            height: 14px
        }

        .w-s-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--muted);
            text-align: center;
            line-height: 1.25
        }

        .w-s.on .w-s-label {
            color: var(--heading)
        }

        .w-line {
            flex: 1;
            height: 2px;
            background: var(--border);
            margin: 15px 0 0;
            border-radius: 2px
        }

        .w-line.on {
            background: var(--primary)
        }

        .w-stephead {
            margin: 0 0 14px;
            display: flex;
            align-items: baseline;
            justify-content: space-between
        }

        .w-stephead b {
            color: var(--heading);
            font-size: 18px
        }

        .w-stephead span {
            color: var(--muted);
            font-size: 13px
        }

        .w-step {
            display: none
        }

        .w-step.show {
            display: block
        }

        /* ---------- Section ---------- */
        .w-sec {
            padding: 22px 0;
            border-top: 1px solid var(--border)
        }

        .w-sec:first-child {
            padding-top: 0;
            border-top: 0
        }

        .w-sec:last-child {
            padding-bottom: 0
        }

        .w-sec-title {
            font-weight: 700;
            font-size: 15px;
            color: var(--heading);
            margin: 0 0 4px
        }

        .w-sec-sub {
            font-size: 13px;
            color: var(--muted);
            margin: 0 0 12px
        }

        .w-opt {
            font-weight: 500;
            color: var(--muted);
            font-size: 12.5px
        }

        /* ---------- Fields ---------- */
        .w-label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: var(--heading);
            margin: 14px 0 6px
        }

        .w-label i {
            color: #c0392b;
            font-style: normal
        }

        .w-input,
        .w-text {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: var(--w-radius);
            background: #fff;
            font: inherit;
            font-size: 14px;
            color: var(--body);
            transition: border-color .15s, box-shadow .15s
        }

        .w-input:focus,
        .w-text:focus {
            outline: 0;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, .06)
        }

        .w-text {
            resize: vertical;
            line-height: 1.5
        }

        .w-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        /* ---------- Info box ---------- */
        .w-info {
            background: var(--bg);
            border-left: 3px solid var(--primary);
            border-radius: 0 var(--w-radius) var(--w-radius) 0;
            padding: 11px 14px;
            font-size: 13px;
            line-height: 1.5;
            color: var(--body);
            margin: 12px 0
        }

        .w-note {
            font-size: 12px;
            color: var(--muted);
            margin: 8px 0 0
        }

        /* ---------- Jenis usaha (Font Awesome, warna per jenis) ---------- */
        .w-type {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            background: #fff;
            transition: border-color .15s, background .15s
        }

        .w-type:hover {
            border-color: var(--tc, var(--muted))
        }

        .w-type.sel {
            border-color: var(--tc, var(--primary));
            background: var(--ts, var(--bg))
        }

        .w-type input {
            position: absolute;
            opacity: 0;
            pointer-events: none
        }

        .w-type-ico {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--ts, var(--bg));
            color: var(--tc, var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
            transition: transform .15s, background .15s
        }

        .w-type:hover .w-type-ico,
        .w-type.sel .w-type-ico {
            transform: scale(1.06)
        }

        .w-type.sel .w-type-ico {
            background: #fff
        }

        .w-type-body {
            flex: 1;
            min-width: 0
        }

        .w-type-name {
            font-weight: 700;
            color: var(--heading);
            font-size: 15px
        }

        .w-type-tag {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px
        }

        .w-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--border);
            flex-shrink: 0;
            position: relative;
            transition: border-color .15s
        }

        .w-type.sel .w-radio {
            border-color: var(--tc, var(--primary))
        }

        .w-type.sel .w-radio::after {
            content: "";
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: var(--tc, var(--primary))
        }

        /* ---------- Pemilik ---------- */
        .w-owner {
            display: grid;
            grid-template-columns: 90px 1fr;
            gap: 8px 12px;
            font-size: 14px;
            background: var(--bg);
            border-radius: var(--w-radius);
            padding: 14px 16px
        }

        .w-owner span:nth-child(odd) {
            color: var(--muted)
        }

        .w-owner span:nth-child(even) {
            color: var(--heading);
            font-weight: 600;
            word-break: break-word
        }

        /* ---------- Logo ---------- */
        .w-logo {
            display: flex;
            align-items: center;
            gap: 16px
        }

        .w-logo-prev {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: var(--bg);
            border: 1.5px solid var(--border);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            flex-shrink: 0
        }

        .w-logo-prev svg {
            width: 30px;
            height: 30px
        }

        .w-logo-prev img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .w-logo input[type=file] {
            font-size: 13px;
            max-width: 100%
        }

        /* ---------- Foto ---------- */
        .w-photos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
            gap: 10px
        }

        .w-ph {
            position: relative;
            aspect-ratio: 1;
            border-radius: 14px;
            overflow: hidden;
            background: var(--bg);
            border: 1px solid var(--border)
        }

        .w-ph img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .w-ph button {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 0;
            background: rgba(0, 0, 0, .65);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0
        }

        .w-ph button svg {
            width: 12px;
            height: 12px
        }

        .w-ph em {
            position: absolute;
            left: 6px;
            bottom: 6px;
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            font-style: normal;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 8px
        }

        .w-add {
            aspect-ratio: 1;
            border-radius: 14px;
            border: 1.5px dashed var(--muted);
            background: var(--bg);
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background .15s
        }

        .w-add:hover {
            background: #fff
        }

        .w-add svg {
            width: 22px;
            height: 22px
        }

        /* ---------- Peta ---------- */
        #w-map {
            height: 400px;
            border-radius: var(--w-radius);
            border: 1px solid var(--border);
            z-index: 0
        }

        .w-geo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            margin: 0 0 10px
        }

        .w-geo:hover {
            background: var(--bg)
        }

        .w-geo svg {
            width: 16px;
            height: 16px
        }

        .w-coord {
            display: inline-block;
            font-size: 12.5px;
            color: var(--muted);
            margin-top: 10px
        }

        .w-coord.ok {
            color: var(--heading);
            font-weight: 600
        }

        /* ---------- Jam operasional ---------- */
        .w-day {
            display: grid;
            grid-template-columns: 110px 1fr auto;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 8px;
            background: #fff
        }

        .w-day.closed {
            background: var(--bg)
        }

        .w-day-name {
            font-weight: 700;
            font-size: 14px;
            color: var(--heading)
        }

        .w-times {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .w-times .w-input {
            padding: 8px 10px;
            width: 128px
        }

        .w-times-sep {
            color: var(--muted)
        }

        .w-times-off {
            font-size: 13px;
            color: var(--muted)
        }

        .w-day-opts {
            display: flex;
            gap: 14px;
            font-size: 13px;
            color: var(--body)
        }

        .w-day-opts label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap
        }

        .w-day-opts input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px
        }

        /* ---------- Tombol ---------- */
        .w-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: 0;
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            padding: 6px 0
        }

        .w-link:hover {
            text-decoration: underline
        }

        .w-nav {
            display: flex;
            align-items: stretch;
            gap: 12px;
            margin-top: 22px
        }

        .w-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 56px;
            padding: 16px 28px;
            border-radius: 16px;
            font: inherit;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--body);
            transition: background .15s, border-color .15s, box-shadow .15s, transform .15s
        }

        .w-btn i {
            font-size: 15px;
            transition: transform .15s
        }

        .w-btn:hover {
            background: var(--bg);
            border-color: var(--muted)
        }

        .w-btn:active {
            transform: scale(.985)
        }

        /* Lanjut: lebih dominan dari Kembali */
        .w-btn.pri {
            flex: 2;
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .18)
        }

        .w-btn.pri:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover)
        }

        .w-btn.pri:hover .fa-arrow-right {
            transform: translateX(4px)
        }

        .w-btn:not(.pri):hover .fa-arrow-left {
            transform: translateX(-4px)
        }

        .w-btn:disabled {
            opacity: .7;
            cursor: wait;
            box-shadow: none
        }

        .w-err {
            color: #c0392b;
            font-size: 12.5px;
            margin-top: 6px
        }

        /* ---------- Layout melebar ---------- */
        .w-grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 36px;
            align-items: start
        }

        .w-col>.w-sec:first-child {
            padding-top: 0;
            border-top: 0
        }

        .w-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px
        }

        .w-types .w-type {
            margin-bottom: 0
        }

        .w-days {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px
        }

        .w-days .w-day {
            grid-template-columns: 1fr auto;
            row-gap: 10px;
            margin-bottom: 0
        }

        .w-days .w-day-opts {
            grid-column: 2;
            grid-row: 1
        }

        .w-days .w-times,
        .w-days .w-times-off {
            grid-column: 1 / -1
        }

        .w-days .w-times .w-input {
            flex: 1;
            width: auto
        }

        .w-days .w-times-off {
            padding: 9px 0
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 860px) {

            .w-grid2,
            .w-days {
                grid-template-columns: 1fr
            }

            .w-grid2 .w-col+.w-col>.w-sec:first-child {
                padding-top: 22px;
                border-top: 1px solid var(--border)
            }

            #w-map {
                height: 320px
            }
        }

        @media (max-width: 600px) {
            .w-row {
                grid-template-columns: 1fr
            }

            .w-s {
                width: 52px
            }

            .w-s-label {
                font-size: 10.5px
            }

            .w-day {
                grid-template-columns: 1fr auto;
                row-gap: 10px
            }

            .w-day-name {
                grid-column: 1
            }

            .w-day-opts {
                grid-column: 2;
                grid-row: 1
            }

            .w-times {
                grid-column: 1 / -1
            }

            .w-times .w-input {
                flex: 1;
                width: auto
            }

            .w-btn {
                min-height: 52px;
                padding: 14px 16px;
                font-size: 15px
            }
        }
    </style>

    <div class="w-wrap">
        <h1 class="page-title">{{ $isEdit ? 'Perbaiki Pengajuan' : 'Daftarkan Usaha' }}</h1>
        <p class="page-sub">
            {{ $isEdit ? 'Perbaiki data sesuai catatan Admin, lalu kirim ulang untuk ditinjau.' : 'Isi 4 langkah singkat. Usaha akan ditinjau Admin sebelum aktif.' }}
        </p>

        @if ($isEdit && !empty($mitra->rejection_reason))
            <div class="alert alert-err"><b>Alasan dari Admin:</b> {{ $mitra->rejection_reason }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-err">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-err">
                <b>Periksa kembali isian Anda:</b>
                <ul style="margin:6px 0 0 18px;padding:0">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="w-dots" id="w-dots">
            @foreach ($steps as $i => $s)
                <div class="w-s" data-s="{{ $i }}">
                    <div class="w-dot" data-dot="{{ $i }}">{{ $i + 1 }}</div>
                    <div class="w-s-label">{{ $s }}</div>
                </div>
                @if (!$loop->last)
                <div class="w-line" data-line="{{ $i }}"></div>@endif
            @endforeach
        </div>
        <div class="w-stephead"><b id="w-title"></b><span id="w-count"></span></div>

        <form id="w-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate>
            @csrf
            @if ($isEdit) @method('PUT') @endif

            {{-- ===== Langkah 1: Jenis usaha ===== --}}
            <div class="w-step card" data-step="0" style="padding:20px">
                <div class="w-types">
                    @foreach ($types as $t)
                        <label class="w-type {{ $selType === $t['code'] ? 'sel' : '' }}"
                            style="--tc: {{ $t['color'] ?? 'var(--primary)' }}; --ts: {{ $t['soft'] ?? 'var(--bg)' }}">
                            <input type="radio" name="mitra_type" value="{{ $t['code'] }}" {{ $selType === $t['code'] ? 'checked' : '' }}>
                            <div class="w-type-ico">
                                <i class="fa-solid {{ $typeIcons[$t['code']] ?? 'fa-paw' }}"></i>
                            </div>
                            <div class="w-type-body">
                                <div class="w-type-name">{{ $t['label'] ?? $t['code'] }}</div>
                                @if (!empty($t['tagline']))
                                    <div class="w-type-tag">{{ $t['tagline'] }}</div>
                                @endif
                            </div>
                            <span class="w-radio"></span>
                        </label>
                    @endforeach
                </div>
                <div class="w-info">Jenis usaha tidak bisa diubah setelah disetujui Admin. Punya usaha lain? Daftarkan
                    sebagai mitra baru kapan saja.</div>
            </div>

            {{-- ===== Langkah 2: Data usaha ===== --}}
            <div class="w-step card" data-step="1" style="padding:22px">
                <div class="w-grid2">
                    <div class="w-col">
                        <div class="w-sec">
                            <div class="w-sec-title">Informasi Pemilik</div>
                            <div class="w-owner">
                                <span>Nama</span><span>{{ $owner->full_name ?? ($owner->username ?? '-') }}</span>
                                <span>Email</span><span>{{ session('supabase.user.email') ?? auth()->user()->email ?? '-' }}</span>
                                @if (!empty($owner->phone_number))<span>No.
                                HP</span><span>{{ $owner->phone_number }}</span>@endif
                            </div>
                            <p class="w-note">Data ini otomatis dari akunmu dan tidak bisa diubah di sini.</p>
                        </div>

                        <div class="w-sec">
                            <div class="w-sec-title">Logo <span class="w-opt">(opsional)</span></div>
                            <div class="w-logo" style="margin-top:10px">
                                <div class="w-logo-prev" id="w-logo-prev">
                                    @if ($isEdit && $mitra->logo_url)
                                        <img src="{{ $mitra->logo_url }}" alt="Logo">
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="3" />
                                            <circle cx="9" cy="9" r="1.5" />
                                            <path d="M21 15l-5-5L5 21" />
                                        </svg>
                                    @endif
                                </div>
                                <input type="file" name="logo" id="w-logo" accept="image/*">
                            </div>
                        </div>

                        <div class="w-sec">
                            <div class="w-sec-title">Informasi Usaha</div>
                            <label class="w-label" for="name">Nama Usaha <i>*</i></label>
                            <input class="w-input" id="name" name="name" maxlength="120"
                                value="{{ old('name', $mitra->name ?? '') }}">
                            <label class="w-label" for="description">Deskripsi Singkat</label>
                            <textarea class="w-text" id="description" name="description" rows="3"
                                maxlength="1000">{{ old('description', $mitra->description ?? '') }}</textarea>
                        </div>

                    </div>
                    <div class="w-col">
                        <div class="w-sec">
                            <div class="w-sec-title">Aturan &amp; Informasi Layanan <i
                                    style="color:#c0392b;font-style:normal">*</i></div>
                            <p class="w-sec-sub">Tampil ke calon pelanggan di halaman usahamu.</p>
                            <textarea class="w-text" id="rules_text" name="rules_text" rows="6" maxlength="5000"
                                placeholder="Tulis tarif, syarat, dan aturan penting sesukamu.">{{ old('rules_text', $mitra->rules_text ?? '') }}</textarea>
                        </div>

                        <div class="w-sec">
                            <div class="w-sec-title">Foto Usaha <span id="w-pcount"
                                    style="color:var(--primary);font-weight:600;margin-left:4px"></span></div>
                            <p class="w-sec-sub">Minimal 1, maksimal {{ $maxPhotos }}. Foto pertama jadi foto utama.</p>
                            <div class="w-photos" id="w-photos"></div>
                            <input type="file" id="w-photo-input" name="photos[]" accept="image/*" multiple
                                style="display:none">
                            <div id="w-keep"></div>
                            @if ($errors->has('photos'))
                            <div class="w-err">{{ $errors->first('photos') }}</div>@endif
                        </div>

                        @if ($isEdit)
                            <div class="w-sec">
                                <div class="w-sec-title">Catatan untuk Admin <span class="w-opt">(opsional)</span></div>
                                <textarea class="w-text" id="owner_note" name="owner_note" rows="3" maxlength="300"
                                    style="margin-top:10px"
                                    placeholder="Mis. foto sudah saya ganti dan titik peta dipindah">{{ old('owner_note') }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== Langkah 3: Lokasi ===== --}}
            <div class="w-step card" data-step="2" style="padding:22px">
                <div class="w-grid2">
                    <div class="w-col">
                        <div class="w-sec">
                            <div class="w-sec-title">Alamat</div>
                            @if ($prefill && !$isEdit)
                                <p class="w-sec-sub">Setiap mitra punya alamat sendiri, isi alamat usaha ini.</p>
                            @endif
                            <label class="w-label" for="address">Alamat Lengkap <i>*</i></label>
                            <textarea class="w-text" id="address" name="address" rows="2"
                                maxlength="500">{{ old('address', $mitra->address ?? '') }}</textarea>
                            <label class="w-label" for="province">Provinsi</label>
                            <input class="w-input" id="province" name="province" value="{{ old('province') }}">
                            <div class="w-row">
                                <div><label class="w-label" for="city">Kota</label><input class="w-input" id="city"
                                        name="city" value="{{ old('city') }}"></div>
                                <div><label class="w-label" for="district">Kecamatan</label><input class="w-input"
                                        id="district" name="district" value="{{ old('district') }}"></div>
                            </div>
                        </div>

                    </div>
                    <div class="w-col">
                        <div class="w-sec">
                            <div class="w-sec-title">Titik Koordinat <i style="color:#c0392b;font-style:normal">*</i></div>
                            <p class="w-sec-sub">Penting untuk fitur "cari terdekat". Klik peta untuk menaruh pin persis di
                                lokasi usahamu.</p>
                            <button type="button" class="w-geo" id="w-geo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3" />
                                    <circle cx="12" cy="12" r="8" />
                                    <path d="M12 2v3M12 19v3M2 12h3M19 12h3" />
                                </svg>
                                Gunakan lokasi saya sekarang
                            </button>
                            <div id="w-map"></div>
                            <span class="w-coord" id="w-coord">Belum ada titik dipilih.</span>
                            <input type="hidden" name="latitude" id="latitude" value="{{ $lat }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ $lng }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Langkah 4: Jam operasional ===== --}}
            <div class="w-step card" data-step="3" style="padding:20px">
                @if ($prefill && $prefillHours && !$isEdit)
                    <div class="w-info" id="w-prefill">Salin jam operasional dari <b>{{ $prefill->name }}</b>?
                        <button type="button" class="w-link" id="w-prefill-btn" style="margin-left:6px">Salin</button>
                    </div>
                @endif
                <div class="w-info" style="margin-top:0">Jadwal layanan hanya bisa dibuat di dalam jam operasional. Jam ini
                    bisa diubah kapan saja nanti.</div>
                <button type="button" class="w-link" id="w-copy-mon" style="margin-bottom:8px">Samakan semua hari dengan
                    Senin</button>

                <div class="w-days">
                    @foreach ($days as $i => $day)
                        @php
                            $h = $hours[$i];
                            $closed = $hadOld ? !empty(old("hours.$i.closed")) : $h['closed'];
                            $is24 = $hadOld ? !empty(old("hours.$i.is24h")) : $h['is24h'];
                            $open = old("hours.$i.open", $h['open']);
                            $close = old("hours.$i.close", $h['close']);
                        @endphp
                        <div class="w-day" data-day="{{ $i }}">
                            <span class="w-day-name">{{ $day }}</span>
                            <div class="w-times">
                                <input type="time" class="w-input d-open" name="hours[{{ $i }}][open]" value="{{ $open }}">
                                <span class="w-times-sep">-</span>
                                <input type="time" class="w-input d-close" name="hours[{{ $i }}][close]" value="{{ $close }}">
                            </div>
                            <div class="w-times-off" style="display:none"></div>
                            <div class="w-day-opts">
                                <label><input type="checkbox" class="d-closed" name="hours[{{ $i }}][closed]" value="1" {{ $closed ? 'checked' : '' }}> Libur</label>
                                <label class="d-24-wrap"><input type="checkbox" class="d-24" name="hours[{{ $i }}][is24h]"
                                        value="1" {{ $is24 ? 'checked' : '' }}> 24 Jam</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($errors->has('hours'))
                <div class="w-err">{{ $errors->first('hours') }}</div>@endif
            </div>

            <div class="w-nav">
                <button type="button" class="w-btn" id="w-prev">Kembali</button>
                <button type="button" class="w-btn pri" id="w-next">Lanjut</button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const STEPS = @json($steps);
            const MAX = {{ $maxPhotos }};
            const IS_EDIT = {{ $isEdit ? 'true' : 'false' }};
            const SUBMIT_LABEL = IS_EDIT ? 'Kirim Ulang untuk Ditinjau' : 'Kirim untuk Ditinjau';
            const PREFILL = @json($prefillHours);
            const ICON_X = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
            const ICON_PLUS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>';
            const ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-10"/></svg>';
            let step = {{ $startStep }};
            let existing = @json($gallery);   // URL foto lama (mode perbaikan)
            let files = [];                    // File baru
            let map, marker;

            const $ = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => [...r.querySelectorAll(s)];
            const form = $('#w-form');
            const toast = (text) => window.Swal ? Swal.fire({ icon: 'warning', title: 'Periksa lagi', text }) : alert(text);

            // ---------- Navigasi ----------
            function render() {
                $$('.w-step').forEach(el => el.classList.toggle('show', +el.dataset.step === step));
                $$('[data-s]').forEach(el => el.classList.toggle('on', +el.dataset.s <= step));
                $$('[data-dot]').forEach(el => {
                    const i = +el.dataset.dot;
                    el.classList.toggle('on', i <= step);
                    el.classList.toggle('cur', i === step);
                    el.innerHTML = i < step ? ICON_CHECK : String(i + 1);
                });
                $$('[data-line]').forEach(el => el.classList.toggle('on', +el.dataset.line < step));
                $('#w-title').textContent = STEPS[step];
                $('#w-count').textContent = 'Langkah ' + (step + 1) + ' dari ' + STEPS.length;
                $('#w-prev').style.display = step === 0 ? 'none' : '';
                $('#w-next').textContent = step < 3 ? 'Lanjut' : SUBMIT_LABEL;
                if (step === 2) initMap();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function validate(s) {
                if (s === 0 && !$('input[name=mitra_type]:checked')) return 'Pilih jenis usaha dulu.';
                if (s === 1) {
                    if (!$('#name').value.trim()) return 'Nama usaha wajib diisi.';
                    if (!$('#rules_text').value.trim()) return 'Aturan & informasi layanan wajib diisi.';
                    if (existing.length + files.length < 1) return 'Minimal 1 foto wajib diupload.';
                }
                if (s === 2) {
                    if (!$('#address').value.trim()) return 'Alamat lengkap wajib diisi.';
                    if (!$('#latitude').value || !$('#longitude').value) return 'Pilih titik lokasi di peta dulu.';
                }
                if (s === 3) {
                    for (const d of $$('.w-day')) {
                        const name = $('.w-day-name', d).textContent;
                        if ($('.d-closed', d).checked || $('.d-24', d).checked) continue;
                        const o = $('.d-open', d).value, c = $('.d-close', d).value;
                        if (!o || !c) return name + ': jam buka dan tutup wajib diisi.';
                        if (o === c) return name + ': jam buka dan tutup tidak boleh sama.';
                    }
                }
                return null;
            }

            $('#w-next').onclick = () => {
                const err = validate(step);
                if (err) return toast(err);
                if (step < 3) { step++; render(); return; }
                const btn = $('#w-next');
                btn.disabled = true; btn.textContent = 'Mengirim...';
                form.submit();
            };
            $('#w-prev').onclick = () => { if (step > 0) { step--; render(); } };

            // ---------- Langkah 1: kartu jenis ----------
            $$('.w-type input').forEach(inp => inp.addEventListener('change', () => {
                $$('.w-type').forEach(l => l.classList.toggle('sel', $('input', l).checked));
            }));

            // ---------- Langkah 2: logo & foto ----------
            $('#w-logo').onchange = (e) => {
                const f = e.target.files[0];
                if (!f) return;
                $('#w-logo-prev').innerHTML = '<img alt="Logo">';
                $('#w-logo-prev img').src = URL.createObjectURL(f);
            };

            function syncInput() {
                const dt = new DataTransfer();
                files.forEach(f => dt.items.add(f));
                $('#w-photo-input').files = dt.files;
                $('#w-keep').innerHTML = existing.map(u => {
                    const i = document.createElement('input');
                    i.type = 'hidden'; i.name = 'keep_gallery[]'; i.value = u;
                    return i.outerHTML;
                }).join('');
            }

            function renderPhotos() {
                const box = $('#w-photos');
                box.innerHTML = '';
                const all = [...existing.map(u => ({ src: u, ex: true })), ...files.map(f => ({ src: URL.createObjectURL(f), ex: false }))];
                all.forEach((p, idx) => {
                    const d = document.createElement('div');
                    d.className = 'w-ph';
                    d.innerHTML = '<img alt=""><button type="button" aria-label="Hapus foto">' + ICON_X + '</button>' + (idx === 0 ? '<em>Utama</em>' : '');
                    $('img', d).src = p.src;
                    $('button', d).onclick = () => {
                        if (p.ex) existing.splice(idx, 1); else files.splice(idx - existing.length, 1);
                        renderPhotos();
                    };
                    box.appendChild(d);
                });
                if (all.length < MAX) {
                    const a = document.createElement('button');
                    a.type = 'button'; a.className = 'w-add';
                    a.innerHTML = ICON_PLUS + '<span>Tambah foto</span>';
                    a.onclick = () => $('#w-photo-input').click();
                    box.appendChild(a);
                }
                $('#w-pcount').textContent = all.length + '/' + MAX;
                syncInput();
            }

            $('#w-photo-input').onchange = (e) => {
                const picked = [...e.target.files];
                let overflow = false;
                picked.forEach(f => {
                    if (existing.length + files.length < MAX) files.push(f); else overflow = true;
                });
                if (overflow) toast('Maksimal ' + MAX + ' foto.');
                renderPhotos();
            };

            // ---------- Langkah 3: peta ----------
            function setPoint(lat, lng, pan) {
                $('#latitude').value = lat.toFixed(7);
                $('#longitude').value = lng.toFixed(7);
                const c = $('#w-coord');
                c.textContent = 'Titik dipilih: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
                c.classList.add('ok');
                if (!marker) marker = L.marker([lat, lng]).addTo(map); else marker.setLatLng([lat, lng]);
                if (pan) map.setView([lat, lng], 17);
            }

            function initMap() {
                if (map) { setTimeout(() => map.invalidateSize(), 50); return; }
                const lat = parseFloat($('#latitude').value), lng = parseFloat($('#longitude').value);
                const has = !isNaN(lat) && !isNaN(lng);
                map = L.map('w-map').setView(has ? [lat, lng] : [-2.5, 118], has ? 16 : 5);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19, attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                map.on('click', e => setPoint(e.latlng.lat, e.latlng.lng, false));
                if (has) setPoint(lat, lng, false);
                setTimeout(() => map.invalidateSize(), 50);
            }

            $('#w-geo').onclick = () => {
                if (!navigator.geolocation) return toast('Browser tidak mendukung geolokasi.');
                navigator.geolocation.getCurrentPosition(
                    p => { initMap(); setPoint(p.coords.latitude, p.coords.longitude, true); },
                    () => toast('Tidak bisa mengambil lokasi. Izinkan akses lokasi atau klik peta manual.')
                );
            };

            // ---------- Langkah 4: jam ----------
            function refreshDay(d) {
                const closed = $('.d-closed', d).checked;
                const is24 = $('.d-24', d).checked;
                const off = $('.w-times-off', d);
                $('.d-24-wrap', d).style.display = closed ? 'none' : '';
                $('.w-times', d).style.display = (closed || is24) ? 'none' : 'flex';
                off.style.display = (closed || is24) ? '' : 'none';
                off.textContent = closed ? 'Tutup sepanjang hari' : (is24 ? 'Buka 24 jam' : '');
                d.classList.toggle('closed', closed);
            }
            $$('.w-day').forEach(d => {
                $('.d-closed', d).addEventListener('change', () => { if ($('.d-closed', d).checked) $('.d-24', d).checked = false; refreshDay(d); });
                $('.d-24', d).addEventListener('change', () => refreshDay(d));
                refreshDay(d);
            });

            function applyDay(d, v) {
                $('.d-closed', d).checked = !!v.closed;
                $('.d-24', d).checked = !!v.is24h;
                $('.d-open', d).value = v.open;
                $('.d-close', d).value = v.close;
                refreshDay(d);
            }

            $('#w-copy-mon').onclick = () => {
                const days = $$('.w-day'), m = days[0];
                const v = { closed: $('.d-closed', m).checked, is24h: $('.d-24', m).checked, open: $('.d-open', m).value, close: $('.d-close', m).value };
                days.slice(1).forEach(d => applyDay(d, v));
            };

            const pb = $('#w-prefill-btn');
            if (pb && PREFILL) pb.onclick = () => {
                $$('.w-day').forEach((d, i) => PREFILL[i] && applyDay(d, PREFILL[i]));
                $('#w-prefill').remove();
                toast('Jam operasional disalin. Cek lagi ya!');
            };

            // Enter di input tidak boleh langsung submit form
            form.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target.tagName === 'INPUT') e.preventDefault(); });

            renderPhotos();
            render();
        })();
    </script>
@endsection
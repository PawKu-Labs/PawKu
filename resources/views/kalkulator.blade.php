@extends('petshops.layout')
@section('title', 'Cek Status Berat Badan Kucing')

@push('styles')
    <style>
        .calc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
            margin-top: 10px;
        }

        .calc-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--ps-line);
            box-shadow: 0 6px 20px rgba(46, 158, 107, .07);
        }

        .calc-card-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--ps-text);
        }

        .calc-group {
            margin-bottom: 16px;
        }

        .calc-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--ps-text);
            margin-bottom: 6px;
        }

        .calc-input,
        .calc-select {
            width: 100%;
            padding: 12px 14px;
            font: inherit;
            font-size: 14px;
            border: 1px solid var(--ps-line);
            border-radius: 14px;
            background: #fff;
            color: var(--ps-text);
            outline: none;
            box-sizing: border-box;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .calc-input:focus,
        .calc-select:focus {
            border-color: var(--ps-primary);
            box-shadow: 0 0 0 3px rgba(46, 158, 107, .15);
        }

        .calc-row {
            display: flex;
            gap: 12px;
        }

        .calc-row>div {
            flex: 1;
        }

        .calc-error {
            color: var(--ps-danger);
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }

        .calc-submit {
            width: 100%;
            padding: 13px 20px;
            font-size: 15px;
            margin-top: 10px;
        }

        /* ---------- Result Styles ---------- */
        .calc-status-box {
            border-radius: 16px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .calc-status-box.s-kurus {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .calc-status-box.s-normal {
            background: #eef7f2;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .calc-status-box.s-gemuk {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .calc-status-box.s-unknown {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        .calc-status-tag {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: .85;
            margin-bottom: 4px;
        }

        .calc-status-title {
            font-size: 22px;
            font-weight: 800;
        }

        .calc-status-bb {
            text-align: right;
        }

        .calc-status-bb .bb-val {
            font-size: 24px;
            font-weight: 800;
        }

        .calc-status-bb .bb-lbl {
            font-size: 11px;
            opacity: .8;
        }

        .calc-bar-wrap {
            margin: 20px 0;
            background: #f8fafc;
            border: 1px solid var(--ps-line);
            border-radius: 16px;
            padding: 16px;
        }

        .calc-bar {
            position: relative;
            height: 12px;
            border-radius: 8px;
            background: linear-gradient(to right, #f59e0b 0% 30%, #2E9E6B 30% 70%, #ef4444 70% 100%);
            margin: 10px 0 6px;
        }

        .calc-marker {
            position: absolute;
            top: -6px;
            width: 24px;
            height: 24px;
            background: #fff;
            border: 3.5px solid var(--ps-text);
            border-radius: 50%;
            transform: translateX(-50%);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
            transition: left .3s ease;
        }

        .calc-bar-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            color: var(--ps-muted);
        }

        .calc-range-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef7f2;
            color: #15803d;
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .calc-saran {
            background: #fcfcfc;
            border: 1px solid var(--ps-line);
            border-radius: 16px;
            padding: 16px;
            margin-top: 16px;
        }

        .calc-saran-title {
            font-size: 14.5px;
            font-weight: 700;
            margin: 0 0 10px;
            color: var(--ps-text);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .calc-saran-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .calc-saran-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13.5px;
            line-height: 1.5;
            color: var(--ps-text);
        }

        .calc-saran-bullet {
            color: var(--ps-primary);
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .calc-trace-details {
            margin-top: 16px;
            border: 1px solid var(--ps-line);
            border-radius: 12px;
            padding: 10px 14px;
            background: #fafafa;
        }

        .calc-trace-summary {
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            color: var(--ps-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .calc-trace-content {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--ps-line);
            font-family: Consolas, monospace;
            font-size: 12px;
            line-height: 1.6;
            color: #475569;
        }

        .calc-disclaimer {
            margin-top: 16px;
            font-size: 12px;
            color: var(--ps-muted);
            line-height: 1.5;
            background: #f8fafc;
            border-left: 3px solid var(--ps-primary);
            padding: 10px 12px;
            border-radius: 0 8px 8px 0;
        }

        .calc-cta-box {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #eef7f2;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 14px 16px;
        }

        .calc-guide-item {
            display: flex;
            gap: 12px;
            padding: 14px;
            background: #f8fafc;
            border-radius: 14px;
            margin-bottom: 12px;
            border: 1px solid var(--ps-line);
        }

        .calc-guide-icon {
            font-size: 22px;
            flex-shrink: 0;
        }

        .calc-guide-text h4 {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 700;
            color: var(--ps-text);
        }

        .calc-guide-text p {
            margin: 0;
            font-size: 12.5px;
            color: var(--ps-muted);
            line-height: 1.4;
        }

        @media (max-width: 860px) {
            .calc-grid {
                grid-template-columns: 1fr;
            }

            .calc-cta-box {
                flex-direction: column;
                align-items: flex-start;
            }

            .calc-cta-box a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')
    <section class="ps-page">
        <a href="{{ url('/') }}" class="ps-back">&larr; Kembali ke Beranda</a>

        <h1 class="ps-title">Cek Status Berat Badan Kucing ⚖️</h1>
        <p class="ps-sub">Periksa status berat badan anabul Anda apakah ideal, kurus, atau berlebih berdasarkan standar ras dan usia.</p>

        <div class="calc-grid">
            {{-- ---------- FORM CARD ---------- --}}
            <div class="calc-card">
                <div class="calc-card-title">
                    <span>🐱</span> Formulir Data Kucing
                </div>

                <form method="POST" action="{{ route('kalkulator.hitung') }}">
                    @csrf

                    <div class="calc-group">
                        <label for="ras" class="calc-label">Ras Kucing</label>
                        <select id="ras" name="ras" class="calc-select" required>
                            <option value="">-- Pilih ras kucing --</option>
                            @foreach ($daftarRas as $r)
                                <option value="{{ $r }}" @selected(old('ras', $hasil['ras'] ?? '') === $r)>{{ $r }}</option>
                            @endforeach
                        </select>
                        @error('ras')
                            <div class="calc-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="calc-row calc-group">
                        <div>
                            <label for="umur" class="calc-label">Umur</label>
                            <input type="number" id="umur" name="umur" min="0" step="1"
                                class="calc-input" value="{{ old('umur') }}" placeholder="mis. 18" required>
                            @error('umur')
                                <div class="calc-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="satuan" class="calc-label">Satuan Usia</label>
                            <select id="satuan" name="satuan" class="calc-select" required>
                                <option value="bulan" @selected(old('satuan', 'bulan') === 'bulan')>Bulan</option>
                                <option value="tahun" @selected(old('satuan') === 'tahun')>Tahun</option>
                            </select>
                            @error('satuan')
                                <div class="calc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="calc-group">
                        <label for="bb" class="calc-label">Berat Badan Saat Ini (kg)</label>
                        <input type="number" id="bb" name="bb" min="0.1" max="20" step="0.1"
                            class="calc-input" value="{{ old('bb', $hasil['bb'] ?? '') }}" placeholder="mis. 4.2" required>
                        @error('bb')
                            <div class="calc-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="ps-btn ps-btn-primary calc-submit">
                        <span>🔍 Cek Status Berat Badan</span>
                    </button>
                </form>
            </div>

            {{-- ---------- HASIL ATAU PANDUAN ---------- --}}
            <div>
                @if ($hasil)
                    <div class="calc-card">
                        <div class="calc-card-title">
                            <span>📊</span> Hasil Analisis Kesehatan
                        </div>

                        <div class="calc-status-box s-{{ $hasil['status'] }}">
                            <div>
                                <div class="calc-status-tag">Status Berat Badan</div>
                                <div class="calc-status-title">
                                    @if ($hasil['status'] === 'normal')
                                        ✅ {{ $hasil['label'] }}
                                    @elseif ($hasil['status'] === 'kurus')
                                        ⚠️ {{ $hasil['label'] }}
                                    @elseif ($hasil['status'] === 'gemuk')
                                        🚨 {{ $hasil['label'] }}
                                    @else
                                        ℹ️ {{ $hasil['label'] }}
                                    @endif
                                </div>
                            </div>
                            <div class="calc-status-bb">
                                <div class="bb-val">{{ number_format($hasil['bb'], 1) }} <span style="font-size: 15px; font-weight: 600;">kg</span></div>
                                <div class="bb-lbl">{{ $hasil['ras'] }}</div>
                            </div>
                        </div>

                        @if ($hasil['posisi'] !== null)
                            <div class="calc-bar-wrap">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; font-weight: 700; color: var(--ps-text);">
                                    <span>Indikator Berat Badan</span>
                                    <span style="color: var(--ps-muted); font-weight: 500;">Skala Ideal</span>
                                </div>
                                <div class="calc-bar">
                                    <div class="calc-marker" style="left: {{ round($hasil['posisi'], 1) }}%;" title="Berat: {{ $hasil['bb'] }} kg"></div>
                                </div>
                                <div class="calc-bar-labels">
                                    <span>Kurus</span>
                                    <span>Ideal</span>
                                    <span>Gemuk</span>
                                </div>
                                <div style="text-align: center;">
                                    <div class="calc-range-badge">
                                        🎯 Rentang ideal ras {{ $hasil['ras'] }}: <strong>{{ number_format($hasil['min'], 1) }} - {{ number_format($hasil['max'], 1) }} kg</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="calc-saran">
                            <div class="calc-saran-title">
                                <span>💡</span> Rekomendasi &amp; Saran Perawatan
                            </div>
                            <ul class="calc-saran-list">
                                @foreach ($hasil['saran'] as $s)
                                    <li class="calc-saran-item">
                                        <span class="calc-saran-bullet">✔</span>
                                        <span>{{ $s }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <details class="calc-trace-details">
                            <summary class="calc-trace-summary">
                                <span>🔬 Detail Alur Keputusan</span>
                                <span style="font-size: 11px; opacity: .7;">▼ Klik untuk buka</span>
                            </summary>
                            <div class="calc-trace-content">
                                @foreach ($hasil['trace'] as $t)
                                    <div>&rarr; {{ $t }}</div>
                                @endforeach
                            </div>
                        </details>

                        <div class="calc-disclaimer">
                            <strong>Catatan Medis:</strong> Hasil analisis ini bersifat indikatif sebagai panduan awal. Konsultasikan dengan dokter hewan jika kucing mengalami penurunan atau kenaikan berat badan secara drastis.
                        </div>

                        <div class="calc-cta-box">
                            <div>
                                <div style="font-weight: 800; font-size: 14px; color: var(--ps-primary);">Butuh konsultasi atau perawatan?</div>
                                <div style="font-size: 12.5px; color: var(--ps-muted);">Temukan klinik hewan dan petshop terdekat di PawKu.</div>
                            </div>
                            <a href="{{ route('petshops.index') }}" class="ps-btn ps-btn-primary" style="white-space: nowrap; font-size: 13px;">
                                🏪 Cari Petshop
                            </a>
                        </div>
                    </div>
                @else
                    <div class="calc-card">
                        <div class="calc-card-title">
                            <span>💡</span> Informasi &amp; Panduan Berat Badan
                        </div>

                        <div class="calc-guide-item">
                            <div class="calc-guide-icon">🐱</div>
                            <div class="calc-guide-text">
                                <h4>Kitten / Anak Kucing (&lt; 12 Bulan)</h4>
                                <p>Kucing usia di bawah 1 tahun masih berada pada masa pertumbuhan pesat. Standar dewasa belum berlaku tetap, fokuslah pada pakan kaya nutrisi dan kenaikan bobot mingguan.</p>
                            </div>
                        </div>

                        <div class="calc-guide-item">
                            <div class="calc-guide-icon">⚖️</div>
                            <div class="calc-guide-text">
                                <h4>Kucing Dewasa (&ge; 12 Bulan)</h4>
                                <p>Setiap ras kucing memiliki rentang berat ideal yang spesifik. Misalnya, ras besar seperti Maine Coon normal di 5 - 8 kg, sedangkan ras Domestik umumnya 3 - 5 kg.</p>
                            </div>
                        </div>

                        <div class="calc-guide-item">
                            <div class="calc-guide-icon">🩺</div>
                            <div class="calc-guide-text">
                                <h4>Pantau Rutin Bulanan</h4>
                                <p>Menimbang berat badan sebulan sekali membantu mendeteksi risiko obesitas, diabetes felin, atau malnutrisi sedini mungkin demi kesehatan optimal anabul.</p>
                            </div>
                        </div>

                        <div class="calc-cta-box" style="margin-top: 20px;">
                            <div>
                                <div style="font-weight: 800; font-size: 14px; color: var(--ps-primary);">Kunjungi Petshop Terpercaya</div>
                                <div style="font-size: 12.5px; color: var(--ps-muted);">Cari pakan khusus diet atau konsultasi dengan dokter hewan mitra PawKu.</div>
                            </div>
                            <a href="{{ route('petshops.index') }}" class="ps-btn ps-btn-primary" style="white-space: nowrap; font-size: 13px;">
                                🏪 Telusuri Petshop
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

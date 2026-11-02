@extends('superadmin.layout')
@section('title', 'Pendapatan')

@push('styles')
    <style>
        .stat-btn {
            margin-top: 4px;
            align-self: flex-start
        }

        .stat-value.money {
            font-size: 26px
        }

        .chart-box {
            position: relative;
            height: 320px;
            width: 100%
        }

        .chart-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px
        }

        .chart-total {
            font-size: 12.5px;
            color: var(--muted)
        }

        .chart-total strong {
            color: var(--heading);
            font-size: 15px
        }

        dialog.chart-dialog {
            width: min(760px, 94vw)
        }
    </style>
@endpush

@section('content')
    <div class="hero">
        <div>
            <h1 class="page-title">Pendapatan</h1>
            <p class="page-sub">Pantau pengguna, mitra, keanggotaan premium, dan nilai transaksi booking di aplikasi PawKu.
            </p>
        </div>
    </div>

    @php
        // key => [label, kelas ikon, kelas aksen, ikon FA, kelas nilai]
        $cards = [
            'users' => ['Total Pengguna', 'i-blue', 'a-blue', 'fa-solid fa-users', ''],
            'premium' => ['Total Pengguna Premium', 'i-purple', 'a-purple', 'fa-solid fa-crown', ''],
            'mitra' => ['Jumlah Mitra', 'i-gold', 'a-gold', 'fa-solid fa-store', ''],
            'mitraPremium' => ['Jumlah Mitra Premium', 'i-red', 'a-red', 'fa-solid fa-star', ''],
            'revenue' => ['Total Transaksi Booking', 'i-green', 'a-green', 'fa-solid fa-sack-dollar', 'money'],
            'count' => ['Jumlah Transaksi', 'i-blue', 'a-blue', 'fa-solid fa-receipt', ''],
        ];
    @endphp

    <section>
        <div class="grid g3">
            @foreach($cards as $key => [$label, $iconCls, $accentCls, $faIcon, $valCls])
                <div class="card stat {{ $accentCls }}">
                    <div class="stat-top">
                        <span class="stat-label">{{ $label }}</span>
                        <div class="stat-icon {{ $iconCls }}"><i class="{{ $faIcon }}"></i></div>
                    </div>
                    <div class="stat-value {{ $valCls }}" id="val-{{ $key }}">-</div>
                    <button type="button" class="btn btn-outline stat-btn" data-chart="{{ $key }}">
                        <i class="fa-solid fa-chart-line"></i> Lihat Grafik
                    </button>
                </div>
            @endforeach
        </div>
    </section>

    <dialog id="chartDialog" class="chart-dialog">
        <div class="modal-header">
            <div class="modal-title" id="chartTitle">Grafik</div>
            <button type="button" class="modal-close" id="chartClose" aria-label="Tutup">&times;</button>
        </div>
        <div class="chart-meta">
            <div class="chart-total" id="chartTotal"></div>
            <div class="field" style="min-width:150px">
                <select class="input" id="chartRange" aria-label="Rentang waktu">
                    <option value="6">6 bulan terakhir</option>
                    <option value="12" selected>12 bulan terakhir</option>
                </select>
            </div>
        </div>
        <div class="chart-box"><canvas id="chartCanvas"></canvas></div>
    </dialog>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        (function () {
            /* ==========================================================
               DATA DUMMY (statis) - nanti ganti dengan data dari backend
               ========================================================== */
            const LABELS = ['Nov 2025', 'Des 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026',
                            'Mei 2026', 'Jun 2026', 'Jul 2026', 'Agu 2026', 'Sep 2026', 'Okt 2026'];

            const DATA = {
                // kumulatif (total sampai akhir bulan)
                users:        [120, 168, 225, 290, 362, 441, 530, 628, 735, 851, 976, 1112],
                premium:      [8, 14, 22, 31, 45, 58, 76, 97, 121, 150, 184, 223],
                mitra:        [10, 14, 19, 25, 31, 38, 46, 55, 64, 74, 85, 97],
                mitraPremium: [1, 2, 3, 5, 7, 9, 12, 15, 19, 23, 28, 34],
                // per bulan (Rupiah)
                revenue:      [3200000, 4850000, 6100000, 7400000, 9250000, 11800000,
                               13500000, 16200000, 18900000, 21700000, 25400000, 29800000],
                // per bulan (kali)
                count:        [32, 47, 58, 69, 84, 103, 121, 144, 166, 189, 218, 251],
            };

            const META = {
                users:        { title: 'Pertumbuhan Total Pengguna',        label: 'Total pengguna',        color: '#F59E3D', money: false, cumulative: true  },
                premium:      { title: 'Pertumbuhan Pengguna Premium',      label: 'Pengguna premium',      color: '#EA580C', money: false, cumulative: true  },
                mitra:        { title: 'Pertumbuhan Jumlah Mitra',          label: 'Jumlah mitra',          color: '#F59E0B', money: false, cumulative: true  },
                mitraPremium: { title: 'Pertumbuhan Mitra Premium',         label: 'Mitra premium',         color: '#ef4444', money: false, cumulative: true  },
                revenue:      { title: 'Nilai Transaksi Booking per Bulan', label: 'Nilai transaksi',       color: '#2E9E6B', money: true,  cumulative: false },
                count:        { title: 'Jumlah Transaksi per Bulan',        label: 'Jumlah transaksi',      color: '#14b8a6', money: false, cumulative: false },
            };

            const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
            const num = (n) => Number(n).toLocaleString('id-ID');
            const fmt = (key, n) => META[key].money ? rupiah(n) : num(n);
            const sum = (arr) => arr.reduce((a, b) => a + b, 0);
            const last = (k) => DATA[k][DATA[k].length - 1];

            /* ---------- Isi angka di card ---------- */
            document.getElementById('val-users').textContent = num(last('users'));
            document.getElementById('val-premium').textContent = num(last('premium'));
            document.getElementById('val-mitra').textContent = num(last('mitra'));
            document.getElementById('val-mitraPremium').textContent = num(last('mitraPremium'));
            document.getElementById('val-revenue').textContent = rupiah(sum(DATA.revenue));
            document.getElementById('val-count').textContent = num(sum(DATA.count)) + ' kali';

            /* ---------- Modal grafik ---------- */
            const dialog = document.getElementById('chartDialog');
            const canvas = document.getElementById('chartCanvas');
            const range = document.getElementById('chartRange');
            const titleEl = document.getElementById('chartTitle');
            const totalEl = document.getElementById('chartTotal');
            let chart = null;
            let current = null;

            function draw() {
                if (!current) return;
                const meta = META[current];
                const n = parseInt(range.value, 10);
                const labels = LABELS.slice(-n);
                const values = DATA[current].slice(-n);

                titleEl.textContent = meta.title;
                totalEl.innerHTML = meta.cumulative
                    ? 'Saat ini: <strong>' + fmt(current, values[values.length - 1]) + '</strong>'
                    : 'Total periode ini: <strong>' + fmt(current, sum(values)) + '</strong>';

                if (chart) chart.destroy();
                const ctx = canvas.getContext('2d');
                const fill = ctx.createLinearGradient(0, 0, 0, 320);
                fill.addColorStop(0, meta.color + '55');
                fill.addColorStop(1, meta.color + '00');

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: meta.label,
                            data: values,
                            borderColor: meta.color,
                            backgroundColor: fill,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: meta.color,
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (c) => ' ' + meta.label + ': ' + fmt(current, c.parsed.y) } },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    callback: (v) => meta.money ? 'Rp ' + (v / 1e6) + ' jt' : v,
                                },
                            },
                        },
                    },
                });
            }

            document.querySelectorAll('[data-chart]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    current = btn.dataset.chart;
                    dialog.showModal();
                    draw();
                });
            });

            range.addEventListener('change', draw);
            document.getElementById('chartClose').addEventListener('click', () => dialog.close());
            dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
            dialog.addEventListener('close', () => { if (chart) { chart.destroy(); chart = null; } });
        })();
    </script>
@endpush
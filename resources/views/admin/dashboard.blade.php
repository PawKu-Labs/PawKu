@extends('admin.layout')
@section('title', 'Backup Data')

@push('styles')
    <style>
        .btn:disabled {
            opacity: .6;
            cursor: not-allowed
        }

        .backup-layout {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: stretch
        }

        @media (max-width: 1050px) {
            .backup-layout {
                grid-template-columns: 1fr
            }
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--heading);
            margin-bottom: 4px
        }

        .card-sub {
            font-size: 12px;
            line-height: 1.6;
            color: var(--muted)
        }

        .tag {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 5px 9px;
            border-radius: 6px;
            white-space: nowrap
        }

        .label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: .3px
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px
        }

        .link-toggle {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer
        }

        .link-toggle:hover {
            text-decoration: underline
        }

        #backup-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        #backup-form .form-actions {
            margin-top: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px
        }

        .check-list {
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 2px 16px
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            font-size: 13.5px;
            color: var(--heading);
            font-weight: 500;
            border-bottom: 1px solid #eef0f6;
            cursor: pointer
        }

        .check-item:last-child {
            border-bottom: none
        }

        .check-item input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px
        }

        .check-item .pill {
            margin-left: auto
        }

        .format-group {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px
        }

        .format-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--r);
            cursor: pointer;
            font-size: 13.5px;
            color: var(--heading);
            font-weight: 600;
            transition: .2s
        }

        .format-option small {
            display: block;
            font-weight: 400;
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px
        }

        .format-option input {
            margin-top: 3px;
            accent-color: var(--primary)
        }

        .format-option:has(input:checked) {
            border-color: var(--primary);
            background: var(--primary-light)
        }

        @media (max-width: 560px) {
            .format-group {
                grid-template-columns: 1fr
            }
        }

        .note {
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.6
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%
        }

        @media (max-width: 700px) {
            .chart-container {
                height: 220px
            }
        }

        /* Kalender */
        .calendar-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 14px
        }

        .calendar-nav button {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 8px;
            cursor: pointer;
            color: var(--heading);
            font-size: 16px
        }

        .calendar-nav button:hover {
            background: var(--primary-light);
            color: var(--primary)
        }

        .calendar-month {
            font-size: 13px;
            font-weight: 700;
            color: var(--heading);
            min-width: 110px;
            text-align: center
        }

        .backup-calendar {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px
        }

        .calendar-weekday {
            text-align: center;
            font-size: 10.5px;
            color: var(--muted);
            font-weight: 700;
            padding: 7px 0
        }

        .calendar-day {
            min-width: 0;
            aspect-ratio: 1 / .85;
            border: 1px solid transparent;
            background: #f8fafc;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 11.5px;
            color: var(--heading);
            cursor: pointer
        }

        .calendar-day:hover {
            border-color: var(--primary)
        }

        .calendar-day.empty {
            background: transparent;
            cursor: default
        }

        .calendar-day.has-backup {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
            font-weight: 700
        }

        .calendar-day.selected {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary)
        }

        .calendar-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #16a34a
        }

        .calendar-day.selected .calendar-dot {
            background: var(--primary)
        }

        .calendar-legend {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11.5px;
            color: var(--muted);
            margin-top: 14px
        }

        .calendar-detail {
            margin-top: 16px;
            padding: 14px;
            border-radius: var(--r);
            background: #f8fafc;
            border: 1px solid var(--border)
        }

        .calendar-detail-date {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--heading);
            margin-bottom: 8px
        }

        .calendar-detail-empty {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6
        }

        .history-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px
        }

        .history-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ecfdf5;
            color: #065f46;
            border-radius: 8px;
            font-size: 15px
        }

        .history-name {
            font-size: 12px;
            color: var(--heading);
            font-weight: 600
        }

        .history-info {
            font-size: 11px;
            color: var(--muted);
            margin-top: 3px
        }
    </style>
@endpush

@section('content')
    @php
        $tables = $tables ?? [];
        $stats = $stats ?? [];
    @endphp

    <div class="hero">
        <div>
            <h1 class="page-title">Backup Data</h1>
            <p class="page-sub">Unduh salinan data PawKu dalam format SQL atau CSV, serta pantau riwayat backup.</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn">Kembali ke Dashboard</a>
        </div>
    </div>

    <section>
        <div class="group-title">Ringkasan</div>
        <div class="grid g3">
            <div class="card stat a-blue">
                <div class="stat-top">
                    <span class="stat-label">Backup Terakhir</span>
                    <div class="stat-icon i-blue">🕘</div>
                </div>
                <div class="stat-value" style="font-size:22px;">14 Okt 2026</div>
                <div class="sub">09:30 WIB · SQL</div>
            </div>

            <div class="card stat a-purple">
                <div class="stat-top">
                    <span class="stat-label">Total Backup Bulan Ini</span>
                    <div class="stat-icon i-purple">🗂️</div>
                </div>
                <div class="stat-value" style="font-size:22px;">8 kali</div>
                <div class="sub">Data simulasi Oktober 2026</div>
            </div>

            <div class="card stat a-green">
                <div class="stat-top">
                    <span class="stat-label">Status Backup</span>
                    <div class="stat-icon i-green">✔</div>
                </div>
                <div class="stat-value" style="font-size:22px;">
                    <span class="pill p-approved" style="font-size:13px;padding:5px 12px;">● Berhasil</span>
                </div>
                <div class="sub">Status simulasi, bukan status server</div>
            </div>
        </div>
    </section>

    <div class="backup-layout">
        <div class="card" style="display:flex;flex-direction:column;">
            <div class="card-head" style="margin-bottom:18px;">
                <div>
                    <h3 class="card-title">Unduh Backup</h3>
                    <p class="card-sub">Pilih data dan format file yang diinginkan</p>
                </div>
            </div>

            <form id="backup-form" action="{{ route('admin.backup.download') }}" method="POST">
                @csrf

                <div>
                    <div class="label-row">
                        <span class="label">PILIH DATA</span>
                        <button type="button" class="link-toggle" id="toggle-all">Hapus semua pilihan</button>
                    </div>
                    <div class="check-list">
                        @foreach($tables as $key => $label)
                            <label class="check-item">
                                <input type="checkbox" name="tables[]" value="{{ $key }}" checked>
                                <span>{{ $label }}</span>
                                @if(isset($stats[$key]))
                                    <span class="pill c-user">{{ number_format($stats[$key]) }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="label" style="margin-bottom:10px;">FORMAT</div>
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

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="btn-download">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Unduh Backup
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h3 class="card-title">Kalender Backup</h3>
                    <p class="card-sub">Pilih tanggal untuk melihat riwayat</p>
                </div>
                <span class="tag">Kalender</span>
            </div>

            <div class="calendar-nav">
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
                <div class="calendar-detail-empty">Klik tanggal pada kalender untuk melihat detail backup.</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3 class="card-title">Aktivitas Backup</h3>
                <p class="card-sub">Frekuensi backup selama 7 hari terakhir</p>
            </div>
            <span class="tag">GRAFIK DATA</span>
        </div>
        <div class="chart-container">
            <canvas id="backupChart"></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            // ----- Form unduh -----
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

            // ----- Grafik & kalender (DUMMY, tanpa request ke server) -----
            (function () {
                const backupHistory = {
                    "2026-10-02": [{ time: "09:15", format: "SQL", size: "1.24 MB", status: "Berhasil" }],
                    "2026-10-05": [{ time: "10:30", format: "CSV", size: "845 KB", status: "Berhasil" }],
                    "2026-10-07": [{ time: "08:45", format: "SQL", size: "1.31 MB", status: "Berhasil" }],
                    "2026-10-08": [{ time: "13:20", format: "SQL", size: "1.32 MB", status: "Berhasil" }],
                    "2026-10-10": [{ time: "09:00", format: "CSV", size: "862 KB", status: "Berhasil" }],
                    "2026-10-12": [{ time: "11:10", format: "SQL", size: "1.38 MB", status: "Berhasil" }],
                    "2026-10-14": [{ time: "09:30", format: "SQL", size: "1.42 MB", status: "Berhasil" }]
                };

                const chartCanvas = document.getElementById("backupChart");
                if (chartCanvas && typeof Chart !== "undefined") {
                    new Chart(chartCanvas, {
                        type: "bar",
                        data: {
                            labels: ["8 Okt", "9 Okt", "10 Okt", "11 Okt", "12 Okt", "13 Okt", "14 Okt"],
                            datasets: [{
                                label: "Jumlah backup",
                                data: [1, 0, 1, 0, 1, 0, 1],
                                backgroundColor: "#5b5ff0",
                                hoverBackgroundColor: "#1677ff",
                                borderRadius: 6,
                                maxBarThickness: 32
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (c) => c.raw + " aktivitas backup (dummy)" } }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: "#f1f5f9" }, title: { display: true, text: "Jumlah backup" } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }

                const calendar = document.getElementById("backupCalendar");
                const monthLabel = document.getElementById("calendarMonth");
                const detail = document.getElementById("calendarDetail");
                if (!calendar || !monthLabel || !detail) return;

                let currentMonth = new Date(2026, 9, 1);
                let selectedDate = "2026-10-14";

                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                const weekdays = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

                const pad = (v) => String(v).padStart(2, "0");
                const dateKey = (y, m, d) => y + "-" + pad(m + 1) + "-" + pad(d);

                function showDetail(key) {
                    selectedDate = key;
                    const entries = backupHistory[key] || [];
                    const p = key.split("-");
                    const date = new Date(Number(p[0]), Number(p[1]) - 1, Number(p[2]));

                    detail.replaceChildren();

                    const heading = document.createElement("div");
                    heading.className = "calendar-detail-date";
                    heading.textContent = date.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
                    detail.appendChild(heading);

                    if (entries.length === 0) {
                        const empty = document.createElement("div");
                        empty.className = "calendar-detail-empty";
                        empty.textContent = "Tidak ada riwayat backup pada tanggal ini (data dummy).";
                        detail.appendChild(empty);
                        return;
                    }

                    entries.forEach((entry) => {
                        const item = document.createElement("div");
                        item.className = "history-item";

                        const icon = document.createElement("div");
                        icon.className = "history-icon";
                        icon.textContent = "✓";

                        const meta = document.createElement("div");
                        const name = document.createElement("div");
                        name.className = "history-name";
                        name.textContent = "Backup " + entry.format + " · " + entry.status;
                        const info = document.createElement("div");
                        info.className = "history-info";
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

                    weekdays.forEach((day) => {
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
                        const cell = document.createElement("button");
                        cell.type = "button";
                        cell.className = "calendar-day";
                        cell.textContent = day;

                        if (backupHistory[key]) {
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

                        cell.addEventListener("click", () => { showDetail(key); renderCalendar(); });
                        calendar.appendChild(cell);
                    }
                }

                document.getElementById("prevMonth").addEventListener("click", () => {
                    currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
                    renderCalendar();
                });
                document.getElementById("nextMonth").addEventListener("click", () => {
                    currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
                    renderCalendar();
                });

                renderCalendar();
                showDetail(selectedDate);
            })();
        </script>
    @endpush
@endsection
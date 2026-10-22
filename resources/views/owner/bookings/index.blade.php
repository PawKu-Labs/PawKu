@extends('owner.layout')
@section('title', 'Riwayat Booking')

@push('styles')
    <style>
        .bk-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin: 18px 0;
        }

        .bk-select {
            padding: 9px 12px;
            font: inherit;
            font-size: 13.5px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--heading);
            min-width: 160px;
        }

        .bk-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 158, 107, .15);
        }

        .bk-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .bk-card {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 18px;
        }

        .bk-main {
            display: flex;
            gap: 14px;
            min-width: 0;
            flex: 1 1 320px;
        }

        .bk-ava {
            flex: 0 0 auto;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid var(--primary);
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 22px;
            background: rgba(46, 158, 107, .1);
        }

        .bk-ava img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .bk-title {
            font-weight: 700;
            font-size: 16px;
            color: var(--heading);
        }

        .bk-meta {
            font-size: 13px;
            color: var(--muted);
            margin-top: 3px;
        }

        .bk-note {
            font-size: 12.5px;
            color: var(--muted);
            margin-top: 8px;
            font-style: italic;
        }

        .bk-side {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .bk-price {
            font-weight: 700;
            font-size: 16px;
            color: var(--heading);
        }

        .bk-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .bk-ok {
            background: #dcfce7;
            color: #166534;
        }

        .bk-err {
            background: #fee2e2;
            color: #b91c1c;
        }

        .bk-muted {
            background: #f1f5f9;
            color: #475569;
        }

        .bk-info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .bk-empty {
            text-align: center;
            padding: 48px 20px;
        }

        .bk-empty .ico {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            background: rgba(46, 158, 107, .1);
        }

        .bk-pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .bk-disabled {
            opacity: .45;
            pointer-events: none;
        }

        @media (max-width: 600px) {
            .bk-side {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">Riwayat Booking 📋</h1>
    <p class="page-sub">Lihat semua booking layanan untuk kucing Anda.</p>

    <form method="GET" class="bk-filter" id="bkFilter">
        <select name="status" class="bk-select" aria-label="Filter status" onchange="this.form.submit()">
            <option value="">Semua status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s['value'] }}" @selected($status === $s['value'])>{{ $s['label'] }}</option>
            @endforeach
        </select>

        <select name="cat_id" class="bk-select" aria-label="Filter kucing" onchange="this.form.submit()">
            <option value="">Semua kucing</option>
            @foreach ($cats as $c)
                <option value="{{ $c->id }}" @selected($catId === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>

        @if ($filtered)
            <a href="{{ route('owner.bookings.index') }}" class="btn btn-outline">Reset</a>
        @endif
    </form>

    @if ($bookings->isEmpty())
        <div class="card bk-empty">
            <div class="ico">📋</div>
            <p style="font-weight:700; font-size:18px; color:var(--heading); margin-bottom:6px;">
                {{ $filtered ? 'Tidak ada booking yang cocok' : 'Belum ada riwayat booking' }}
            </p>
            <p style="font-size:13px; color:var(--muted);">
                {{ $filtered ? 'Coba ubah atau reset filter.' : 'Booking layanan untuk kucingmu akan muncul di sini.' }}
            </p>
        </div>
    @else
        <div class="bk-list">
            @foreach ($bookings as $b)
                <div class="card bk-card">
                    <div class="bk-main">
                        <div class="bk-ava">
                            @if (!empty($b->cat_photo))
                                <img src="{{ $b->cat_photo }}" alt="Foto {{ $b->cat_name }}" loading="lazy">
                            @else
                                🐾
                            @endif
                        </div>
                        <div style="min-width:0;">
                            <div class="bk-title">{{ $b->service_name ?: 'Layanan' }}</div>
                            <div class="bk-meta">🐱 {{ $b->cat_name ?? '-' }}
                                @if ($b->mitra_name) · 🏪 {{ $b->mitra_name }} @endif
                            </div>
                            @if ($b->date_label)
                                <div class="bk-meta">📅 {{ $b->date_label }}
                                    @if ($b->time_label) · {{ $b->time_label }} @endif
                                </div>
                            @endif
                            @if ($b->queue_number)
                                <div class="bk-meta">Antrian #{{ $b->queue_number }}</div>
                            @endif
                            @if ($b->check_in_label || $b->check_out_label)
                                <div class="bk-meta">
                                    @if ($b->check_in_label) Check-in {{ $b->check_in_label }} @endif
                                    @if ($b->check_out_label) · Check-out {{ $b->check_out_label }} @endif
                                </div>
                            @endif
                            @if ($b->notes)
                                <div class="bk-note">“{{ $b->notes }}”</div>
                            @endif
                            @if ($b->created_label)
                                <div class="bk-meta" style="font-size:11.5px; margin-top:8px;">Dibuat {{ $b->created_label }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="bk-side">
                        <span class="bk-badge bk-{{ $b->status_tone }}">{{ $b->status_label }}</span>
                        @if ($b->price_label)
                            <span class="bk-price">{{ $b->price_label }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($bookings->hasPages())
            <div class="bk-pager">
                @if ($bookings->onFirstPage())
                    <span class="btn btn-outline bk-disabled">‹ Sebelumnya</span>
                @else
                    <a href="{{ $bookings->previousPageUrl() }}" class="btn btn-outline">‹ Sebelumnya</a>
                @endif

                <span>Halaman {{ $bookings->currentPage() }} dari {{ $bookings->lastPage() }}</span>

                @if ($bookings->hasMorePages())
                    <a href="{{ $bookings->nextPageUrl() }}" class="btn btn-outline">Berikutnya ›</a>
                @else
                    <span class="btn btn-outline bk-disabled">Berikutnya ›</span>
                @endif
            </div>
        @endif
    @endif
@endsection
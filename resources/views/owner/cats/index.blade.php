@extends('owner.layout')
@section('title', 'Kucing Saya')

@push('styles')
    <style>
        .cats-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .cat-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s;
        }

        .cat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, .07);
        }

        .cat-ava {
            flex: 0 0 auto;
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: 3px solid var(--accent);
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: color-mix(in srgb, var(--accent) 14%, white);
            font-size: 26px;
        }

        .cat-ava img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .cat-info {
            min-width: 0;
            flex: 1;
        }

        .cat-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--heading);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cat-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .cat-tag {
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 20px;
            color: var(--accent);
            background: color-mix(in srgb, var(--accent) 12%, white);
        }

        .cat-go {
            color: var(--accent);
            font-size: 20px;
            line-height: 1;
        }

        .cats-empty {
            text-align: center;
            padding: 48px 20px;
            margin-top: 18px;
        }

        .cats-empty .ico {
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
    </style>
@endpush

@section('content')
    @php
        $accents = ['#2E9E6B', '#3B82F6', '#F59E0B', '#EC4899', '#8B5CF6', '#14B8A6'];
    @endphp

    <div class="cats-head">
        <div>
            <h1 class="page-title">Kucing Saya 🐱</h1>
            <p class="page-sub">Kelola profil kucing kesayangan Anda.</p>
        </div>
        <a href="{{ route('owner.cats.create') }}" class="btn btn-primary">+ Tambah Kucing</a>
    </div>

    @if ($cats->isEmpty())
        <div class="card cats-empty">
            <div class="ico">🐾</div>
            <p style="font-weight:700; font-size:18px; color:var(--heading); margin-bottom:6px;">Belum ada kucing</p>
            <p style="font-size:13px; color:var(--muted); margin-bottom:18px;">Yuk tambahkan profil kucing pertamamu.</p>
            <a href="{{ route('owner.cats.create') }}" class="btn btn-primary">Tambah Kucing</a>
        </div>
    @else
        <div class="cat-grid">
            @foreach ($cats as $i => $cat)
                <a href="{{ route('owner.cats.edit', $cat->id) }}" class="card cat-card"
                    style="--accent: {{ $accents[$i % count($accents)] }};">
                    <div class="cat-ava">
                        @if (!empty($cat->photo_url))
                            <img src="{{ $cat->photo_url }}" alt="Foto {{ $cat->name }}" loading="lazy">
                        @else
                            🐾
                        @endif
                    </div>
                    <div class="cat-info">
                        <div class="cat-name">{{ $cat->name }}</div>
                        <div class="cat-tags">
                            <span class="cat-tag">{{ $cat->breed ?: 'Ras tidak diketahui' }} • {{ $cat->age_label }}</span>
                            <span class="cat-tag">{{ $cat->gender === 'betina' ? 'Betina' : 'Jantan' }}</span>
                            @if ($cat->is_neutered)
                                <span class="cat-tag">Steril</span>
                            @endif
                        </div>
                    </div>
                    <span class="cat-go">›</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
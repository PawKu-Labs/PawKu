@extends('petshops.layout')
@section('title', 'Cari Petshop')

@section('content')
    <section class="ps-page">
        <h1 class="ps-title">Cari Petshop 🏪</h1>
        <p class="ps-sub">Temukan petshop, klinik, grooming, dan hotel kucing terpercaya.</p>

        <form method="GET" action="{{ route('petshops.index') }}" class="ps-search" role="search">
            <input type="search" name="q" value="{{ $q }}" maxlength="100"
                placeholder="Cari nama atau alamat petshop" aria-label="Cari petshop">
            <button type="submit" class="ps-btn ps-btn-primary">Cari</button>
            @if ($q !== '')
                <a href="{{ route('petshops.index') }}" class="ps-btn ps-btn-soft">Hapus</a>
            @endif
        </form>

        @if ($shops->isEmpty())
            <div class="ps-empty">
                <div class="ico">🏪</div>
                <p style="font-weight:800; font-size:18px; margin:0 0 6px;">
                    {{ $q === '' ? 'Belum ada petshop terdaftar' : 'Petshop tidak ditemukan' }}
                </p>
                <p class="ps-sub" style="margin:0;">
                    {{ $q === '' ? 'Petshop baru perlu disetujui admin dulu sebelum muncul di sini.' : 'Coba kata kunci lain.' }}
                </p>
            </div>
        @else
            <div class="ps-grid">
                @foreach ($shops as $shop)
                    @php $t = $shop->type; @endphp
                    <a href="{{ route('petshops.show', $shop->id) }}" class="ps-card">
                        <div class="ps-thumb" style="background: {{ $t['soft'] }};">
                            @if ($shop->image)
                                <img src="{{ $shop->image }}" alt="Foto {{ $shop->name }}" loading="lazy">
                            @else
                                {{ $t['icon'] }}
                            @endif
                        </div>
                        <div class="ps-card-body">
                            <div class="ps-card-name">{{ $shop->name }}</div>
                            <span class="ps-pill"
                                style="color: {{ $t['color'] }}; background: {{ $t['soft'] }};">{{ $t['icon'] }}
                                {{ $t['label'] }}</span>
                            @if (!empty($shop->address))
                                <div class="ps-addr">📍 {{ $shop->address }}</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($shops->hasPages())
                <div class="ps-pager">
                    @if ($shops->previousPageUrl())
                        <a href="{{ $shops->previousPageUrl() }}" class="ps-btn ps-btn-soft">← Sebelumnya</a>
                    @endif
                    <span>Halaman {{ $shops->currentPage() }} dari {{ $shops->lastPage() }}</span>
                    @if ($shops->nextPageUrl())
                        <a href="{{ $shops->nextPageUrl() }}" class="ps-btn ps-btn-soft">Berikutnya →</a>
                    @endif
                </div>
            @endif
        @endif
    </section>
@endsection
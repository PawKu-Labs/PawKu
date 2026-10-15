@extends('mitra.layout')
@section('title', $tab)

@section('content')
    <div>
        <h1 class="page-title">{{ $tab }}</h1>
        <p class="page-sub">Halaman ini sedang disiapkan (Sprint 3).</p>
    </div>
    <div class="card" style="text-align:center;padding:40px 20px;">
        <div style="font-size:40px;margin-bottom:8px;">🚧</div>
        <p>Fitur {{ $tab }} untuk {{ $mitra->name }} segera hadir.</p>
    </div>
@endsection
@extends('owner.layout')
@section('title', 'Status Mitra')

@section('content')
    @php
        $mi = \App\Support\MitraType::info($mitra->mitra_type);
        $rejected = $mitra->status === 'rejected';
    @endphp
    <div>
        <a href="{{ route('owner.mitra') }}" style="font-size:13px;color:var(--primary);text-decoration:none;">← Mitra
            Saya</a>
        <h1 class="page-title" style="margin-top:8px">{{ $mi['icon'] }} {{ $mitra->name }}</h1>
        <p class="page-sub">{{ $mi['label'] }}</p>
    </div>

    <div class="card" style="max-width:640px;display:flex;flex-direction:column;gap:12px;">
        @if ($rejected)
            <div><span class="pill p-rejected">Ditolak</span></div>
            <p style="font-weight:600;color:var(--heading)">Pendaftaran usaha ini ditolak.</p>
            <div class="alert alert-err">
                <strong>Alasan penolakan:</strong><br>
                {{ !empty($mitra->rejection_reason) ? $mitra->rejection_reason : 'Admin tidak mencantumkan alasan.' }}
            </div>
        @else
            <div><span class="pill p-pending">Menunggu persetujuan</span></div>
            <p style="font-weight:600;color:var(--heading)">Pendaftaran sedang ditinjau Super Admin.</p>
            <p>Dashboard dan layanan akan terbuka setelah usaha disetujui.</p>
        @endif

        @if (!empty($mitra->owner_note))
            <div style="font-size:13px"><strong>Catatan Anda:</strong> {{ $mitra->owner_note }}</div>
        @endif
        @if (!empty($mitra->resubmitted_at))
            <div style="font-size:12px;color:var(--muted)">Diajukan ulang:
                {{ \Illuminate\Support\Carbon::parse($mitra->resubmitted_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                WIB
            </div>
        @endif
        @if ($mitra->status === 'rejected')
            <a href="{{ route('owner.mitra.edit', $mitra->id) }}" class="btn-add">Perbaiki &amp; kirim ulang</a>
        @endif
    </div>
@endsection
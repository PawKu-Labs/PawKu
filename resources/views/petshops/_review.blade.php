<div class="ps-review">
    <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
        <div style="min-width:0;">
            <div style="font-weight:700;">{{ $r->reviewer }}</div>
            <div class="ps-stars">{{ str_repeat('★', $r->stars) }}{{ str_repeat('☆', 5 - $r->stars) }}</div>
        </div>
        @if ($r->date !== '')
            <small style="color:var(--ps-muted); white-space:nowrap;">{{ $r->date }}</small>
        @endif
    </div>

    @if (trim((string) $r->comment) !== '')
        <p style="margin:10px 0 0; font-size:14px; line-height:1.5;">{{ $r->comment }}</p>
    @endif

    @if ($r->cat_name || $r->service_name)
        <div style="margin-top:10px; font-size:12px; color:var(--ps-muted);">
            🐾
            @if ($r->cat_name)
                Kucing yang dilayani: {{ $r->cat_name }}@if ($r->service_name) • {{ $r->service_name }}@endif
            @else
                {{ $r->service_name }}
            @endif
        </div>
    @endif
</div>
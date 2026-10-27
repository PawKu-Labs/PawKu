@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination"
        style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 18px;">
        <div>
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline" style="opacity:.5;pointer-events:none;">&laquo; Sebelumnya</span>
            @else
                <a class="btn btn-outline" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
            @endif
        </div>

        <span class="sub">Halaman {{ $paginator->currentPage() }}</span>

        <div>
            @if ($paginator->hasMorePages())
                <a class="btn btn-outline" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
            @else
                <span class="btn btn-outline" style="opacity:.5;pointer-events:none;">Berikutnya &raquo;</span>
            @endif
        </div>
    </nav>
@endif
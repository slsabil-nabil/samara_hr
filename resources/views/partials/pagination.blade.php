@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $startPage = max($currentPage - 2, 1);
        $endPage = min($currentPage + 2, $lastPage);
    @endphp

    <nav class="pagination-wrap" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-summary">
            عرض {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} من {{ $paginator->total() }}
        </div>

        <div class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="pagination-item is-disabled">السابق</span>
            @else
                <a class="pagination-item" href="{{ $paginator->previousPageUrl() }}" rel="prev">السابق</a>
            @endif

            @if ($startPage > 1)
                <a class="pagination-item" href="{{ $paginator->url(1) }}">1</a>

                @if ($startPage > 2)
                    <span class="pagination-item is-disabled">...</span>
                @endif
            @endif

            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page === $currentPage)
                    <span class="pagination-item is-active">{{ $page }}</span>
                @else
                    <a class="pagination-item" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="pagination-item is-disabled">...</span>
                @endif

                <a class="pagination-item" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="pagination-item" href="{{ $paginator->nextPageUrl() }}" rel="next">التالي</a>
            @else
                <span class="pagination-item is-disabled">التالي</span>
            @endif
        </div>
    </nav>
@endif
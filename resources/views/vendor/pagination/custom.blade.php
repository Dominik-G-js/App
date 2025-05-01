@if ($paginator->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Zobrazeno {{ $paginator->firstItem() }} až {{ $paginator->lastItem() }} z {{ $paginator->total() }} výsledků
        </div>
        <div class="pagination-controls">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="prev-link disabled">&laquo; Předchozí</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="prev-link" rel="prev">&laquo; Předchozí</a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="next-link" rel="next">Další &raquo;</a>
            @else
                <span class="next-link disabled">Další &raquo;</span>
            @endif
        </div>
    </div>

    <div class="pagination-numbers text-center mt-4">
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            $startPage = max(1, $currentPage - 4);
            $endPage = min($lastPage, $startPage + 9);

            if ($endPage - $startPage < 9) {
                $startPage = max(1, $endPage - 9);
            }
        @endphp

        @if ($startPage > 1)
            <a href="{{ $paginator->url(1) }}" class="page-number">1</a>

            @if ($startPage > 2)
                <span class="page-ellipsis">...</span>
            @endif
        @endif

        @for ($i = $startPage; $i <= $endPage; $i++)
            <a href="{{ $paginator->url($i) }}" class="page-number {{ $currentPage == $i ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        @if ($endPage < $lastPage)
            @if ($endPage < $lastPage - 1)
                <span class="page-ellipsis">...</span>
            @endif

            <a href="{{ $paginator->url($lastPage) }}" class="page-number {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
        @endif
    </div>
@endif

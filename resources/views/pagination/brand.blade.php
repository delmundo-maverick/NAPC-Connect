@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btn-ghost btn-sm cursor-not-allowed opacity-40" aria-disabled="true">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-primary btn-sm">Previous</a>
        @endif

        <p class="muted font-semibold">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-primary btn-sm">Next</a>
        @else
            <span class="btn-ghost btn-sm cursor-not-allowed opacity-40" aria-disabled="true">Next</span>
        @endif
    </nav>
@endif

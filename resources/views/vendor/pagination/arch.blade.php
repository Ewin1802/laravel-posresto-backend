@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav class="pager" role="navigation" aria-label="Navigasi halaman">
        <span>
            @if ($paginator->total() > 0)
                Menampilkan <strong class="num">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong>
                dari <strong class="num">{{ number_format($paginator->total(), 0, ',', '.') }}</strong> data
            @endif
        </span>

        @if ($paginator->hasPages())
            <ul>
                @if ($paginator->onFirstPage())
                    <li class="is-disabled" aria-disabled="true"><span class="pg"><i class="fas fa-chevron-left"></i></span></li>
                @else
                    <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></a></li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="is-disabled" aria-disabled="true"><span class="pg">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="is-current" aria-current="page"><span class="pg">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></a></li>
                @else
                    <li class="is-disabled" aria-disabled="true"><span class="pg"><i class="fas fa-chevron-right"></i></span></li>
                @endif
            </ul>
        @endif
    </nav>
@endif

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center gap-6 mt-16">

        {{-- Progress Line --}}
        <div class="w-full max-w-xs h-px bg-neutral-200 relative">
            <div
                class="absolute top-0 left-0 h-px bg-black transition-all duration-500"
                style="width: {{ $paginator->currentPage() / $paginator->lastPage() * 100 }}%"
            ></div>
        </div>

        <div class="flex items-center gap-6">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="w-11 h-11 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-300 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 6-6 6 6 6" />
                    </svg>
                </span>
            @else

                <a href="{{ $paginator->previousPageUrl() }}"
                rel="prev"
                aria-label="{{ __('pagination.previous') }}"
                class="w-11 h-11 rounded-full border border-neutral-200 flex items-center justify-center hover:bg-black hover:border-black hover:text-white transition-colors duration-300"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 6-6 6 6 6" />
                </svg>
                </a>
            @endif

            {{-- Page Count --}}
            <div class="flex items-baseline gap-2 font-medium tabular-nums select-none">
                <span class="text-lg">{{ str_pad($paginator->currentPage(), 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-neutral-300">—</span>
                <span class="text-neutral-400 text-sm">{{ str_pad($paginator->lastPage(), 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            {{-- Next --}}
            @if ($paginator->hasMorePages())

                <a href="{{ $paginator->nextPageUrl() }}"
                rel="next"
                aria-label="{{ __('pagination.next') }}"
                class="w-11 h-11 rounded-full border border-neutral-200 flex items-center justify-center hover:bg-black hover:border-black hover:text-white transition-colors duration-300"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 6 6 6-6 6" />
                </svg>
                </a>
            @else
                <span class="w-11 h-11 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-300 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 6 6 6-6 6" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Results Summary --}}
        <p class="text-xs uppercase tracking-[0.18em] text-neutral-400">
            @if ($paginator->firstItem())
                {{ __('Showing') }} {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} {{ __('of') }} {{ $paginator->total() }}
            @else
                {{ $paginator->count() }} {{ __('results') }}
            @endif
        </p>
    </nav>
@endif

@props(['paginator'])

@if ($paginator->hasPages())
    <nav {{ $attributes->merge(['class' => 'flex items-center justify-between gap-3 border-t border-slate-100 pt-4']) }} aria-label="Pagination">
        <p class="text-[11px] text-ocular-copy/60">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }}
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="grid size-9 place-items-center border border-slate-200 text-slate-300" aria-hidden="true">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-9 place-items-center border border-slate-200 text-ocular-teal hover:bg-ocular-teal/5" aria-label="Halaman sebelumnya">‹</a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($paginator->onEachSide(1)->linkCollection() as $link)
                    @if ($link['page'] === null)
                        @continue
                    @endif
                    @if ($link['label'] === '...')
                        <span class="grid size-9 place-items-center text-slate-400">…</span>
                    @elseif ($link['active'])
                        <span class="grid size-9 place-items-center bg-ocular-teal text-xs font-bold text-white" aria-current="page">{{ $link['label'] }}</span>
                    @else
                        <a href="{{ $link['url'] }}" class="grid size-9 place-items-center border border-slate-200 text-xs text-ocular-teal hover:bg-ocular-teal/5">{{ $link['label'] }}</a>
                    @endif
                @endforeach
            </div>

            <span class="px-2 text-xs font-bold text-ocular-teal sm:hidden">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-9 place-items-center border border-slate-200 text-ocular-teal hover:bg-ocular-teal/5" aria-label="Halaman berikutnya">›</a>
            @else
                <span class="grid size-9 place-items-center border border-slate-200 text-slate-300" aria-hidden="true">›</span>
            @endif
        </div>
    </nav>
@endif

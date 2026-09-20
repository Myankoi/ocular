@props([
    'title' => 'Pencarian dan filter',
    'description' => null,
    'active' => 0,
])

@php($filterId = $attributes->get('id', 'filter-dialog-' . (string) str()->uuid()))

<div class="inline-flex min-w-0 items-center" data-filter-dialog-root>
    <button type="button" data-filter-open="{{ $filterId }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 border border-ocular-teal/30 bg-white px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-ocular-teal transition hover:bg-ocular-teal/5" aria-haspopup="dialog" aria-controls="{{ $filterId }}">
        <i data-lucide="sliders-horizontal" class="size-4 shrink-0 text-ocular-orange"></i>
        <span>Filter</span>
        @if ($active > 0)
            <span class="grid size-5 place-items-center bg-ocular-orange text-[9px] text-white">{{ $active }}</span>
        @endif
    </button>

    <dialog id="{{ $filterId }}" data-filter-dialog class="border-0 bg-transparent p-0 backdrop:bg-slate-950/50" style="position: fixed; inset: 0; width: min(92vw, 42rem); max-width: min(92vw, 42rem); height: fit-content; max-height: 90vh; margin: auto">
        <div class="max-h-[90vh] overflow-y-auto border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-ocular-surface px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="grid size-8 shrink-0 place-items-center border-l-2 border-ocular-orange bg-white text-ocular-teal"><i data-lucide="sliders-horizontal" class="size-4"></i></span>
                    <div><h2 class="text-sm font-black uppercase tracking-[0.16em] text-ocular-teal">{{ $title }}</h2>@if ($description)<p class="mt-1 text-xs text-ocular-copy/60">{{ $description }}</p>@endif</div>
                </div>
                <button type="button" data-filter-close="{{ $filterId }}" class="grid size-8 place-items-center border border-slate-200 bg-white text-lg leading-none text-ocular-copy transition hover:border-ocular-teal hover:text-ocular-teal" aria-label="Tutup filter">×</button>
            </div>
            <div class="p-5">{{ $slot }}</div>
        </div>
    </dialog>
</div>

<script>
    (() => {
        const id = @js($filterId);
        const dialog = document.getElementById(id);
        const root = dialog?.closest('[data-filter-dialog-root]');
        if (!dialog || !root) return;

        root.querySelector(`[data-filter-open="${id}"]`)?.addEventListener('click', () => dialog.showModal());
        root.querySelector(`[data-filter-close="${id}"]`)?.addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    })();
</script>

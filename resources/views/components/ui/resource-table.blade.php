@props([
    'title',
    'meta' => null,
    'minWidth' => 'min-w-[720px]',
])

<x-ui.card padding="p-0" {{ $attributes->merge(['class' => 'overflow-hidden border border-slate-200']) }}>
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
        <div>
            <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">{{ $title }}</h2>
            @if ($meta)
                <p class="mt-1 text-xs text-ocular-copy/60">{{ $meta }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>

    <div class="hidden overflow-x-auto md:block">
        <table class="w-full {{ $minWidth }} text-left text-sm">
            {{ $slot }}
        </table>
    </div>

    @isset($mobile)
        <div class="divide-y divide-slate-100 md:hidden">{{ $mobile }}</div>
    @endisset

    @isset($pagination)
        <div class="px-4 pb-4 sm:px-5">{{ $pagination }}</div>
    @endisset
</x-ui.card>

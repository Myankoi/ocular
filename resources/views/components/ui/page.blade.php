@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @if ($title)
        <header class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <div class="min-w-0">
                @if ($eyebrow)
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-ocular-teal">{{ $eyebrow }}</p>
                @endif
                <h1 class="mt-1 text-2xl font-black tracking-tight text-ocular-teal sm:text-3xl">{{ $title }}</h1>
                @if ($description)
                    <p class="mt-1 max-w-2xl text-sm text-ocular-copy/75">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex min-w-0 flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center lg:justify-end">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    {{ $slot }}
</div>

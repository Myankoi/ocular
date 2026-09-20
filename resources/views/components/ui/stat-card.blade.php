@props([
    'label',
    'value',
    'tone' => 'teal',
    'meta' => null,
])

@php
    $tones = [
        'teal' => 'text-ocular-teal border-ocular-accent/20',
        'orange' => 'text-ocular-orange border-ocular-orange/20',
        'success' => 'text-emerald-600 border-emerald-200',
        'warning' => 'text-amber-600 border-amber-200',
        'danger' => 'text-rose-600 border-rose-200',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'border bg-white p-4 shadow-[var(--shadow-card)] ' . ($tones[$tone] ?? $tones['teal'])]) }}>
    <p class="text-[10px] font-bold uppercase tracking-widest text-ocular-accent">{{ $label }}</p>
    <div class="mt-1 flex items-end justify-between gap-2">
        <p class="font-mono text-2xl font-bold leading-none">{{ $value }}</p>
        @if ($meta)
            <span class="text-[10px] font-bold text-ocular-copy/60">{{ $meta }}</span>
        @endif
    </div>
</div>

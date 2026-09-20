@props([
    'variant' => 'primary',
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'bg-ocular-orange text-white hover:bg-ocular-orange-dark focus-visible:ring-ocular-orange',
        'teal' => 'border border-ocular-teal/30 bg-ocular-teal/5 text-ocular-teal hover:bg-ocular-teal/10 focus-visible:ring-ocular-teal',
        'outline' => 'border border-ocular-teal/30 bg-white text-ocular-teal hover:bg-ocular-teal/5 focus-visible:ring-ocular-teal',
        'muted' => 'border border-slate-200 bg-slate-50 text-ocular-copy hover:bg-slate-100 focus-visible:ring-ocular-teal',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:ring-rose-500',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => 'inline-flex min-h-11 items-center justify-center gap-2 px-4 py-3 text-xs font-black uppercase tracking-[0.12em] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ' . ($variants[$variant] ?? $variants['primary']),
]) }}>
    {{ $slot }}
</button>

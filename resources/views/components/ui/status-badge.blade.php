@props([
    'status',
    'label' => null,
])

@php
    $status = strtolower((string) $status);
    $styles = [
        'hadir' => 'bg-emerald-50 text-emerald-700',
        'sakit' => 'bg-amber-50 text-amber-700',
        'izin' => 'bg-blue-50 text-blue-700',
        'alpha' => 'bg-rose-50 text-rose-700',
        'open' => 'bg-emerald-50 text-emerald-700',
        'closed' => 'bg-slate-100 text-slate-600',
    ];
    $labels = [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alpha' => 'Alpha',
        'open' => 'Terbuka',
        'closed' => 'Ditutup',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex px-2 py-1 text-[10px] font-black uppercase tracking-wider ' . ($styles[$status] ?? 'bg-slate-100 text-slate-600'),
]) }}>
    {{ $label ?? ($labels[$status] ?? ucfirst($status)) }}
</span>

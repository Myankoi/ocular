@props([
    'message' => null,
    'type' => 'success',
])

@php
    $classes = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'error' => 'border-rose-200 bg-rose-50 text-rose-700',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
        'info' => 'border-blue-200 bg-blue-50 text-blue-700',
    ];
@endphp

@if ($message)
    <div role="status" {{ $attributes->merge(['class' => 'border-l-4 p-3 text-sm ' . ($classes[$type] ?? $classes['info'])]) }}>
        {{ $message }}
    </div>
@endif

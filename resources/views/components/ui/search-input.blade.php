@props([
    'name' => 'q',
    'value' => '',
    'placeholder' => 'Cari...',
    'id' => null,
])

@php($id = $id ?? $name)

<div class="relative flex-1">
    <label for="{{ $id }}" class="sr-only">{{ $placeholder }}</label>
    <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-ocular-accent"></i>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'min-h-12 w-full border border-slate-300 bg-white pl-10 pr-4 text-sm text-ocular-copy outline-none transition placeholder:text-slate-400 focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20']) }}
    >
</div>

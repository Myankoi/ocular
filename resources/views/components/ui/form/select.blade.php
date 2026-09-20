@props([
    'label',
    'name',
])

<label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
    {{ $label }}
    <select
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm text-ocular-copy outline-none transition focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20']) }}
    >
        {{ $slot }}
    </select>
    @error($name)
        <span class="mt-1 block text-sm font-medium normal-case tracking-normal text-rose-600">{{ $message }}</span>
    @enderror
</label>

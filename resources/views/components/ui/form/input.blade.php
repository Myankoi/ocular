@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
])

<label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
    {{ $label }}
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm text-ocular-copy outline-none transition placeholder:text-slate-400 focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20']) }}
    >
    @error($name)
        <span class="mt-1 block text-sm font-medium normal-case tracking-normal text-rose-600">{{ $message }}</span>
    @enderror
</label>

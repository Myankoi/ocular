@props([
    'label',
    'name',
    'checked' => false,
    'value' => '1',
])

<label {{ $attributes->merge(['class' => 'flex items-center gap-3 border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-semibold text-ocular-copy']) }}>
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked($checked) class="size-4 border-slate-300 text-ocular-teal">
    <span>{{ $label }}</span>
</label>

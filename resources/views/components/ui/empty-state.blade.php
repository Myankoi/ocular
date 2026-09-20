@props([
    'message' => 'Belum ada data.',
    'colspan' => null,
])

@if ($colspan)
    <tr>
        <td colspan="{{ $colspan }}" class="px-5 py-12 text-center text-sm text-ocular-copy/60">{{ $message }}</td>
    </tr>
@else
    <p {{ $attributes->merge(['class' => 'px-4 py-12 text-center text-sm text-ocular-copy/60']) }}>{{ $message }}</p>
@endif

@props([
    'editHref' => null,
    'deleteAction' => null,
    'confirm' => 'Hapus data ini?',
    'deleteLabel' => 'Hapus',
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    @if ($editHref)
        <a href="{{ $editHref }}" class="inline-flex min-h-9 items-center justify-center border border-ocular-teal/20 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-teal transition hover:bg-ocular-teal/5">Edit</a>
    @endif

    @if ($deleteAction)
        <form method="POST" action="{{ $deleteAction }}">
            @csrf
            @method('DELETE')
            <button class="inline-flex min-h-9 items-center justify-center border border-rose-200 px-3 text-[10px] font-black uppercase tracking-wider text-rose-600 transition hover:bg-rose-50" onclick="return confirm(@js($confirm))">{{ $deleteLabel }}</button>
        </form>
    @endif

    {{ $slot }}
</div>

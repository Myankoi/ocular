@props([
    'padding' => 'p-4 sm:p-5',
])

<section {{ $attributes->merge(['class' => "bg-white shadow-[var(--shadow-card)] $padding"]) }}>
    {{ $slot }}
</section>

@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-semibold text-white bg-brand-600 shadow-sm'
            : 'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-ink-600 hover:bg-cream-100 hover:text-ink-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
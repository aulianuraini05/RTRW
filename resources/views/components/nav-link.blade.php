@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold text-white bg-indigo-600 shadow-sm'
            : 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
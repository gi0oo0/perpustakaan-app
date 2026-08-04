@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-2 border-primary text-start text-body-xs font-body text-primary bg-primary/5 focus:outline-none transition duration-200 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-2 border-transparent text-start text-body-xs font-body text-text-tertiary hover:text-text hover:bg-surface-light hover:border-surface-lighter focus:outline-none transition duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

@props(['href', 'active' => false, 'label' => ''])

@php
$classes = $active
    ? 'nav-active relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm font-medium'
    : 'nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm transition-colors duration-150';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($active)
        <span class="nav-indicator absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full"></span>
    @endif
    {{ $slot }}
    <span class="flex-1">{{ $label }}</span>
</a>

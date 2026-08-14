@props(['href', 'active' => false, 'label' => ''])

@php
$classes = $active
    ? 'relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm font-medium text-white bg-white/[0.06] [&>svg]:text-white/85'
    : 'relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm text-white/55 hover:text-white hover:bg-white/[0.04] [&>svg]:text-white/40 hover:[&>svg]:text-white/75 transition-colors duration-150';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($active)
        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full bg-primary"></span>
    @endif
    {{ $slot }}
    <span class="flex-1">{{ $label }}</span>
</a>

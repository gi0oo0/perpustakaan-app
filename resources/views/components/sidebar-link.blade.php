@props(['href', 'active' => false, 'label' => ''])

@php
$classes = $active
    ? 'relative flex items-center gap-3 px-3 py-2.5 rounded-[10px] font-body text-sm font-medium text-white bg-white/[0.07] [&>svg]:text-violet-400'
    : 'relative flex items-center gap-3 px-3 py-2.5 rounded-[10px] font-body text-sm text-white/55 hover:text-white hover:bg-white/[0.05] [&>svg]:text-white/40 hover:[&>svg]:text-white/70 transition-colors duration-150';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($active)
        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-primary"></span>
    @endif
    {{ $slot }}
    <span class="flex-1">{{ $label }}</span>
</a>

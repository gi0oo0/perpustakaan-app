@props(['href', 'active' => false, 'label' => ''])

@php
$classes = $active
    ? 'relative flex items-center gap-3 px-3 py-2.5 rounded-glass-sm font-body text-sm font-medium text-white bg-white/[0.09] border border-white/10'
    : 'relative flex items-center gap-3 px-3 py-2.5 rounded-glass-sm font-body text-sm text-white/55 hover:text-white hover:bg-white/[0.06] transition-all duration-200';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($active)
        <span class="absolute -left-3 w-1 h-6 rounded-r-full bg-gradient-soft"></span>
    @endif
    {{ $slot }}
    <span class="flex-1">{{ $label }}</span>
    @if ($active)
        <span class="w-1.5 h-1.5 rounded-full bg-gradient-accent shadow-glow"></span>
    @endif
</a>

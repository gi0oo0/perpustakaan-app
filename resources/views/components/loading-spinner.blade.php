@props([
    'size' => 30,
    'stroke' => 3.2,
    'track' => 'text-black/[0.14] dark:text-white/[0.15]',
    'segment' => 'text-primary',
    'class' => '',
])

<svg viewBox="0 0 28 28" width="{{ $size }}" height="{{ $size }}"
     class="oc-spinner shrink-0 {{ $class }}" role="status" aria-label="Memuat">
    <circle cx="14" cy="14" r="12" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" class="{{ $track }}"></circle>
    <circle cx="14" cy="14" r="12" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-dasharray="24 51.4" transform="rotate(-90 14 14)" class="{{ $segment }}"></circle>
</svg>
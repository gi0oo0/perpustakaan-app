@props([
    'minHeight' => '320px',
    'size' => 30,
])

<div x-cloak
     x-show="$store.loading.busy"
     class="w-full flex items-center justify-center"
     style="min-height: {{ $minHeight }};">
    <x-loading-spinner :size="$size" />
</div>
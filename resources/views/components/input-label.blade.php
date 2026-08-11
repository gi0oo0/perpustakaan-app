@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-body text-xs font-medium text-white/70 mb-2']) }}>
    {{ $value ?? $slot }}
</label>

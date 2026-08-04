@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-body text-body-xs font-normal text-text-secondary mb-2']) }}>
    {{ $value ?? $slot }}
</label>

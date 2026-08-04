@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-body text-body-xs text-success']) }}>
        {{ $status }}
    </div>
@endif

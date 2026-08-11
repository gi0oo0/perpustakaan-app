@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-body text-sm text-emerald-300']) }}>
        {{ $status }}
    </div>
@endif

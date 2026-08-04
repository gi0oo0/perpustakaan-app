<button {{ $attributes->merge(['type' => 'submit', 'class' => 'apple-btn-primary']) }}>
    {{ $slot }}
</button>

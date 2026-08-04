<button {{ $attributes->merge(['type' => 'submit', 'class' => 'apple-btn-danger']) }}>
    {{ $slot }}
</button>

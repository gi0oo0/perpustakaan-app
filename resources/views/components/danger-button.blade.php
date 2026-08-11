<button {{ $attributes->merge(['type' => 'submit', 'class' => 'glass-btn-danger']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'glass-btn-primary']) }}>
    {{ $slot }}
</button>

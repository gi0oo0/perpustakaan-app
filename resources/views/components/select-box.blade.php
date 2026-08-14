@props(['options' => [], 'value' => '', 'placeholder' => 'Pilih...', 'name' => null])

<div x-data="selectBox(@js($options), @js($value ?? ''), @js($placeholder), @js($name))"
     @click.outside="open = false"
     @keydown.escape="open = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="toggle"
            class="glass-input w-full flex items-center justify-between gap-2 cursor-pointer"
            aria-haspopup="listbox" :aria-expanded="open ? 'true' : 'false'">
        <span class="truncate font-body" :class="value ? 'text-white' : 'text-white/35'" x-text="selectedLabel"></span>
        <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
             :class="open ? 'rotate-180 text-white/70' : 'text-white/40'"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute z-50 mt-1.5 w-full max-h-60 overflow-y-auto rounded-glass border border-white/10 bg-night/90 backdrop-blur-2xl shadow-glass-lg">
        <template x-for="opt in options" :key="opt.value">
            <button type="button" @click="select(opt)"
                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 font-body text-sm text-start transition-colors"
                    :class="String(opt.value) === String(value) ? 'text-white bg-white/[0.07] font-medium' : 'text-white/60 hover:text-white hover:bg-white/[0.05]'">
                <span class="truncate" x-text="opt.label"></span>
                <svg class="w-4 h-4 flex-shrink-0" :class="String(opt.value) === String(value) ? 'text-primary' : 'opacity-0'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </template>
    </div>

    <template x-if="name">
        <input type="hidden" :name="name" :value="value">
    </template>
</div>

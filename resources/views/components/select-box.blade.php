@props(['options' => [], 'value' => '', 'placeholder' => 'Pilih...', 'name' => null])

<div x-data="selectBox(@js($options), @js($value ?? ''), @js($placeholder), @js($name))"
     @click.outside="open = false"
     @keydown.escape="open = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="toggle"
            class="glass-input w-full flex items-center justify-between gap-2 cursor-pointer transition-colors duration-150"
            :class="open ? 'border-[#2DB7A8]/60' : ''"
            aria-haspopup="listbox" :aria-expanded="open ? 'true' : 'false'">
        <span class="truncate font-body" :class="value ? 'text-white' : 'text-white/35'" x-text="selectedLabel"></span>
        <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-150"
             :class="open ? 'rotate-180 text-[#A5ADB3]' : 'text-[#A5ADB3]'"
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
         class="absolute z-[60] mt-1.5 w-full max-h-[240px] overflow-y-auto rounded-[8px] border border-white/[0.07] bg-[#202428] shadow-[0_8px_24px_rgba(0,0,0,0.28)] p-1">
        <template x-for="opt in options" :key="opt.value">
            <button type="button" @click="select(opt)"
                    class="w-full flex items-center justify-between gap-2 h-9 px-3 rounded-[6px] font-body text-[13px] text-start transition-colors duration-150"
                    :class="String(opt.value) === String(value) ? 'bg-[#2DB7A8]/[0.12] text-[#2DB7A8] font-medium' : 'text-[#F1F3F4] hover:bg-[#252A2E]'">
                <span class="truncate" x-text="opt.label"></span>
                <svg class="w-4 h-4 flex-shrink-0 transition-opacity duration-150" :class="String(opt.value) === String(value) ? 'text-[#2DB7A8]' : 'opacity-0'"
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
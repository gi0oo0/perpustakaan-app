@props(['value' => '', 'placeholder' => 'Pilih Tanggal', 'align' => 'left'])

<div x-data="datePicker(@js($value ?? ''), @js($placeholder))"
     @click.outside="open = false"
     @keydown.escape="open = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="toggle"
            class="glass-input w-full flex items-center justify-between gap-2 cursor-pointer transition-colors duration-150"
            :class="open ? 'border-[#2DB7A8]/60' : ''"
            aria-haspopup="dialog" :aria-expanded="open ? 'true' : 'false'">
        <span class="truncate font-body" :class="value ? 'text-white' : 'text-white/35'" x-text="label"></span>
        <svg class="w-4 h-4 flex-shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         :class="align === 'right' ? 'right-0' : 'left-0'"
         class="absolute z-50 mt-1.5 w-[296px] max-w-[calc(100vw-2rem)] rounded-[12px] border border-white/[0.06] bg-[#1B1F22] shadow-[0_8px_24px_rgba(0,0,0,0.25)] p-3">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-2.5">
            <template x-if="mode === 'calendar'">
                <button type="button" @click="prevMonth"
                        class="w-8 h-8 flex items-center justify-center rounded-[8px] text-[#A5ADB3] hover:text-white hover:bg-white/[0.05] transition-colors duration-150" aria-label="Bulan sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </template>
            <template x-if="mode === 'monthYear'">
                <button type="button" @click="prevYear"
                        class="w-8 h-8 flex items-center justify-center rounded-[8px] text-[#A5ADB3] hover:text-white hover:bg-white/[0.05] transition-colors duration-150" aria-label="Tahun sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </template>
            <button type="button" @click="toggleMode"
                    class="flex items-center gap-1.5 h-8 px-2 rounded-[8px] hover:bg-white/[0.05] transition-colors duration-150">
                <template x-if="mode === 'calendar'">
                    <span class="font-display font-semibold text-sm text-[#F1F3F4] capitalize" x-text="monthLabel"></span>
                </template>
                <template x-if="mode === 'monthYear'">
                    <input type="number" x-model.number="yearInput" @keydown.enter="applyYear" @blur="applyYear" @click.stop
                           min="1900" max="2100"
                           class="w-16 bg-[#202428] border border-white/[0.08] rounded-[8px] px-1.5 py-1 text-center font-display font-semibold text-sm text-[#F1F3F4] focus:outline-none focus:border-[#2DB7A8]">
                </template>
                <svg class="w-3 h-3 text-[#747C82]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <template x-if="mode === 'calendar'">
                <button type="button" @click="nextMonth"
                        class="w-8 h-8 flex items-center justify-center rounded-[8px] text-[#A5ADB3] hover:text-white hover:bg-white/[0.05] transition-colors duration-150" aria-label="Bulan berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </template>
            <template x-if="mode === 'monthYear'">
                <button type="button" @click="nextYear"
                        class="w-8 h-8 flex items-center justify-center rounded-[8px] text-[#A5ADB3] hover:text-white hover:bg-white/[0.05] transition-colors duration-150" aria-label="Tahun berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </template>
        </div>

        {{-- Month grid (quick jump) --}}
        <div x-show="mode === 'monthYear'" x-cloak class="grid grid-cols-3 gap-1.5 mb-1">
            <template x-for="(mn, mi) in monthNames" :key="mn">
                <button type="button" @click="goMonth(mi)"
                        class="py-2 rounded-[8px] font-body text-xs transition-colors duration-150"
                        :class="mi === month ? 'bg-[#2DB7A8]/15 text-white font-semibold' : 'text-[#A5ADB3] hover:bg-white/[0.05] hover:text-white'"
                        x-text="mn"></button>
            </template>
        </div>

        {{-- Weekday header --}}
        <div x-show="mode === 'calendar'" class="grid grid-cols-7 gap-1 mb-1">
            <template x-for="w in weekdays" :key="w">
                <span class="text-center text-[10px] font-medium tracking-wider text-[#7F898F]" x-text="w"></span>
            </template>
        </div>

        {{-- Days grid --}}
        <div x-show="mode === 'calendar'" class="grid grid-cols-7 gap-1">
            <template x-for="(d, i) in days" :key="i">
                <template x-if="d === null">
                    <span></span>
                </template>
                <template x-if="d !== null">
                    <button type="button" @click="selectDate(d)"
                            class="h-8 rounded-[8px] font-body text-[13px] transition-colors duration-150"
                            :class="isSelected(d)
                                ? 'bg-[#2DB7A8] text-[#0E201D] font-semibold'
                                : (isToday(d)
                                    ? 'text-[#2DB7A8] border border-[#2DB7A8]/45 font-medium hover:bg-[#2DB7A8]/10'
                                    : 'text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white')"
                            x-text="d"></button>
                </template>
            </template>
        </div>

        {{-- Footer --}}
        <div class="mt-3 pt-3 border-t border-white/[0.045] flex items-center gap-2">
            <button type="button" @click="setToday"
                    class="flex-1 h-8 rounded-[8px] font-body text-xs font-medium text-[#A5ADB3] hover:text-[#2DB7A8] hover:bg-white/[0.05] transition-colors duration-150">
                Hari Ini
            </button>
            <button type="button" @click="clear"
                    class="flex-1 h-8 rounded-[8px] font-body text-xs font-medium text-[#E7A0A5] hover:bg-white/[0.05] transition-colors duration-150">
                Bersihkan
            </button>
        </div>
    </div>
</div>
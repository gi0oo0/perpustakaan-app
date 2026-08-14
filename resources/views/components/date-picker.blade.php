@props(['value' => '', 'placeholder' => 'Pilih Tanggal', 'align' => 'left'])

<div x-data="datePicker(@js($value ?? ''), @js($placeholder))"
     @click.outside="open = false"
     @keydown.escape="open = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="toggle"
            class="glass-input w-full flex items-center justify-between gap-2 cursor-pointer"
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
         class="absolute z-50 mt-1.5 w-72 max-w-[calc(100vw-2rem)] rounded-glass border border-white/10 bg-night/90 backdrop-blur-2xl shadow-glass-lg p-3.5">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-3">
            <template x-if="mode === 'calendar'">
                <button type="button" @click="prevMonth"
                        class="p-1.5 rounded-glass-sm text-white/50 hover:text-white hover:bg-white/[0.07] transition-colors" aria-label="Bulan sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </template>
            <template x-if="mode === 'monthYear'">
                <button type="button" @click="prevYear"
                        class="p-1.5 rounded-glass-sm text-white/50 hover:text-white hover:bg-white/[0.07] transition-colors" aria-label="Tahun sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </template>
            <button type="button" @click="toggleMode"
                    class="flex items-center gap-1.5 px-2 py-1 rounded-glass-sm hover:bg-white/[0.07] transition-colors">
                <template x-if="mode === 'calendar'">
                    <span class="font-display font-semibold text-sm text-white capitalize" x-text="monthLabel"></span>
                </template>
                <template x-if="mode === 'monthYear'">
                    <input type="number" x-model.number="yearInput" @keydown.enter="applyYear" @blur="applyYear" @click.stop
                           min="1900" max="2100"
                           class="w-16 bg-white/[0.06] border border-white/10 rounded-glass-sm px-1.5 py-0.5 text-center font-display font-semibold text-sm text-white focus:outline-none focus:border-primary/70">
                </template>
                <svg class="w-3 h-3 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <template x-if="mode === 'calendar'">
                <button type="button" @click="nextMonth"
                        class="p-1.5 rounded-glass-sm text-white/50 hover:text-white hover:bg-white/[0.07] transition-colors" aria-label="Bulan berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </template>
            <template x-if="mode === 'monthYear'">
                <button type="button" @click="nextYear"
                        class="p-1.5 rounded-glass-sm text-white/50 hover:text-white hover:bg-white/[0.07] transition-colors" aria-label="Tahun berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </template>
        </div>

        {{-- Month grid (quick jump) --}}
        <div x-show="mode === 'monthYear'" x-cloak class="grid grid-cols-3 gap-1.5 mb-1">
            <template x-for="(mn, mi) in monthNames" :key="mn">
                <button type="button" @click="goMonth(mi)"
                        class="py-2 rounded-glass-sm font-body text-xs transition-colors"
                        :class="mi === month ? 'bg-primary/20 text-white font-semibold' : 'text-white/70 hover:bg-white/[0.08] hover:text-white'"
                        x-text="mn"></button>
            </template>
        </div>

        {{-- Weekday header --}}
        <div x-show="mode === 'calendar'" class="grid grid-cols-7 gap-1 mb-1">
            <template x-for="w in weekdays" :key="w">
                <span class="text-center text-[10px] font-semibold uppercase tracking-wide text-white/35" x-text="w"></span>
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
                            class="h-9 rounded-lg font-body text-sm transition-colors"
                            :class="isSelected(d)
                                ? 'bg-primary text-white font-semibold shadow-glow'
                                : (isToday(d)
                                    ? 'text-primary border border-primary/50 font-medium hover:bg-primary/10'
                                    : 'text-white/70 hover:bg-white/[0.08] hover:text-white')"
                            x-text="d"></button>
                </template>
            </template>
        </div>

        {{-- Footer --}}
        <div class="mt-3 pt-3 border-t border-white/10 flex items-center gap-2">
            <button type="button" @click="setToday"
                    class="flex-1 py-1.5 rounded-glass-sm font-body text-xs font-medium text-white/70 hover:text-white hover:bg-white/[0.07] transition-colors">
                Hari Ini
            </button>
            <button type="button" @click="clear"
                    class="flex-1 py-1.5 rounded-glass-sm font-body text-xs font-medium text-rose-300/80 hover:text-rose-300 hover:bg-rose-500/10 transition-colors">
                Bersihkan
            </button>
        </div>
    </div>
</div>

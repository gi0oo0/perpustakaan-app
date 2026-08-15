import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        items: [],
        show(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4200);
        },
        remove(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    });

    Alpine.store('loading', {
        pending: 0,
        active: false,
        timer: null,
        begin() {
            this.pending += 1;
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.active = true;
            }, 150);
        },
        end() {
            this.pending = Math.max(0, this.pending - 1);
            if (this.pending === 0) {
                if (this.timer) clearTimeout(this.timer);
                this.active = false;
            }
        },
        async run(promise) {
            this.begin();
            try {
                return await promise;
            } finally {
                this.end();
            }
        },
    });

    Alpine.data('reveal', () => ({
        init() {
            this.$el.classList.add('card-reveal');
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            this.$el.classList.add('is-visible');
                            observer.disconnect();
                        }
                    });
                },
                { threshold: 0.06 }
            );
            observer.observe(this.$el);
        },
    }));

    Alpine.data('countUp', () => ({
        displayed: 0,
        target: 0,
        init() {
            this.target = parseInt(this.$el.dataset.count || '0', 10);
            const duration = 1300;
            const start = performance.now();
            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                this.displayed = Math.round(this.target * (1 - Math.pow(1 - progress, 3)));
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        },
    }));

    Alpine.data('loanOptions', (duration = 7, denda = 500) => ({
        baseDuration: 7,
        baseRate: 500,
        maxDurationDays: 90,
        rates: [500, 1000, 2000, 3000, 5000, 10000],
        duration,
        denda,
        init() {
            if (this.duration > this.maxDuration()) {
                this.duration = this.maxDuration();
            }
        },
        maxDuration() {
            return Math.min(
                Math.floor((this.denda * this.baseDuration) / this.baseRate),
                this.maxDurationDays
            );
        },
        durationOptions() {
            const max = this.maxDuration();
            const out = [];
            for (let d = this.baseDuration; d <= max; d += this.baseDuration) {
                out.push(d);
            }
            if (out.length === 0 || out[out.length - 1] !== max) {
                out.push(max);
            }
            return out;
        },
        setRate(rate) {
            this.denda = rate;
            if (this.duration > this.maxDuration()) {
                this.duration = this.maxDuration();
            }
        },
        setDuration(dur) {
            this.duration = dur;
        },
    }));

    Alpine.data('themeToggle', () => ({
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    }));

    Alpine.store('bookPreview', {
        data: null,
        coverPalette: ['#2E3B4E', '#3A5A53', '#4E3A44', '#52543A', '#39425C', '#5A4636', '#4A4359', '#3E4A48'],
        coverColor(book) {
            return this.coverPalette[Math.abs(book.id) % this.coverPalette.length];
        },
    });

    Alpine.data('globalSearch', () => ({
        open: false,
        query: '',
        results: [],
        loading: false,
        timer: null,
        doSearch() {
            clearTimeout(this.timer);
            const q = this.query.trim();
            if (q.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            this.timer = setTimeout(async () => {
                try {
                    const res = await fetch(
                        '/books/search?q=' + encodeURIComponent(q),
                        { headers: { Accept: 'application/json' } }
                    );
                    const data = await res.json();
                    this.results = data;
                    this.open = true;
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            }, 250);
        },
        go(url) {
            window.location.href = url;
        },
        reset() {
            this.query = '';
            this.results = [];
            this.open = false;
        },
        focusInput() {
            this.$refs.input.focus();
            this.$refs.input.select();
        },
    }));

    Alpine.data('userTable', (users) => ({
        users: users || [],
        query: '',
        role: '',
        sort: 'recent',
        filtered() {
            const q = this.query.trim().toLowerCase();
            const out = this.users.filter((u) => {
                if (
                    q &&
                    !(u.name + ' ' + (u.nisn || '') + ' ' + u.email).toLowerCase().includes(q)
                ) {
                    return false;
                }
                if (this.role && u.role !== this.role) return false;
                return true;
            });
            return [...out].sort((a, b) => {
                switch (this.sort) {
                    case 'name':
                        return a.name.localeCompare(b.name, 'id');
                    case 'nisn':
                        return (a.nisn || '').localeCompare(b.nisn || '');
                    case 'email':
                        return a.email.localeCompare(b.email, 'id');
                    default:
                        return b.id - a.id;
                }
            });
        },
    }));

    Alpine.data('clock', () => ({
        now: new Date(),
        init() {
            this.timer = setInterval(() => {
                this.now = new Date();
            }, 1000);
        },
        destroy() {
            clearInterval(this.timer);
        },
    }));

    Alpine.data('bookCatalog', (items, isAdmin) => ({
        items: items || [],
        isAdmin,
        query: '',
        kategori: '',
        status: '',
        sort: 'recent',
        coverColor(book) {
            return this.$store.bookPreview.coverColor(book);
        },
        resetFilters() {
            this.query = '';
            window.dispatchEvent(new CustomEvent('selectbox:reset'));
        },
        previewBook(book) {
            this.$store.bookPreview.data = book;
            this.$dispatch('open-modal', 'book-preview');
        },
        filtered() {
            const q = this.query.trim().toLowerCase();
            const out = this.items.filter((b) => {
                if (
                    q &&
                    !b.title.toLowerCase().includes(q) &&
                    !b.author.toLowerCase().includes(q) &&
                    !(b.isbn || '').toLowerCase().includes(q) &&
                    !(b.kategori || '').toLowerCase().includes(q)
                ) {
                    return false;
                }
                if (this.kategori && b.kategori !== this.kategori) return false;
                if (this.status === 'available' && !b.available) return false;
                if (this.status === 'borrowed' && b.available) return false;
                return true;
            });
            return [...out].sort((a, b) => {
                switch (this.sort) {
                    case 'title':
                        return a.title.localeCompare(b.title, 'id');
                    case 'author':
                        return a.author.localeCompare(b.author, 'id');
                    case 'year':
                        return (b.publication_year || 0) - (a.publication_year || 0);
                    case 'stock':
                        return b.stock - a.stock;
                    default:
                        return b.id - a.id;
                }
            });
        },
    }));

    Alpine.store('loanExport', {
        url: '',
    });

    Alpine.data('loanTable', (loans, exportBase, isStaff) => ({
        loans: loans || [],
        exportBase: exportBase || '',
        isStaff,
        query: '',
        status: '',
        dateFrom: '',
        dateTo: '',
        sort: 'recent',
        init() {
            this.syncExport();
            this.$watch('status', () => this.syncExport());
            this.$watch('dateFrom', () => this.syncExport());
            this.$watch('dateTo', () => this.syncExport());
        },
        syncExport() {
            this.$store.loanExport.url = this.exportUrl();
        },
        filtered() {
            const q = this.query.trim().toLowerCase();
            const out = this.loans.filter((l) => {
                if (
                    q &&
                    !(
                        l.book_title +
                        ' ' +
                        l.isbn +
                        ' ' +
                        l.borrower_name +
                        ' ' +
                        l.borrower_nisn
                    ).toLowerCase().includes(q)
                ) {
                    return false;
                }
                if (this.status === 'returned' && !l.status_key.startsWith('returned')) return false;
                if (this.status !== 'returned' && this.status && l.status_key !== this.status) return false;
                if (this.dateFrom && l.loan_date_iso < this.dateFrom) return false;
                if (this.dateTo && l.loan_date_iso > this.dateTo) return false;
                return true;
            });
            return [...out].sort((a, b) => {
                switch (this.sort) {
                    case 'title':
                        return a.book_title.localeCompare(b.book_title, 'id');
                    case 'borrower':
                        return a.borrower_name.localeCompare(b.borrower_name, 'id');
                    case 'status':
                        return a.status_key.localeCompare(b.status_key);
                    case 'due':
                        return a.due_date.localeCompare(b.due_date);
                    default:
                        return b.loan_date_iso.localeCompare(a.loan_date_iso);
                }
            });
        },
        exportUrl() {
            const params = new URLSearchParams();
            if (this.status) params.set('status', this.status);
            if (this.dateFrom) params.set('date_from', this.dateFrom);
            if (this.dateTo) params.set('date_to', this.dateTo);
            const qs = params.toString();
            return this.exportBase + (qs ? '?' + qs : '');
        },
    }));

    Alpine.data('filePicker', () => ({
        fileName: '',
        fileSize: 0,
        isCsv: false,
        submitting: false,
        setFile(file) {
            if (!file) {
                this.fileName = '';
                this.fileSize = 0;
                this.isCsv = false;
                return;
            }
            this.fileName = file.name;
            this.fileSize = file.size;
            this.isCsv = /\.csv$/i.test(file.name) || file.type === 'text/csv';
        },
        onPick(event) {
            this.setFile(event.target.files[0]);
        },
        onDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.fileInput.files = dt.files;
            } catch (e) {}
            this.setFile(file);
        },
        get fileSizeLabel() {
            if (!this.fileSize) return '';
            if (this.fileSize < 1024) return this.fileSize + ' B';
            if (this.fileSize < 1048576) return (this.fileSize / 1024).toFixed(1) + ' KB';
            return (this.fileSize / 1048576).toFixed(2) + ' MB';
        },
    }));

    Alpine.data('selectBox', (options, value = '', placeholder = 'Pilih...', name = null) => ({
        options: options || [],
        value: value || '',
        placeholder,
        name: name || '',
        open: false,
        get selectedLabel() {
            const hit = this.options.find((o) => String(o.value) === String(this.value));
            return hit ? hit.label : this.placeholder;
        },
        init() {
            window.addEventListener('selectbox:reset', () => {
                this.value = '';
                this.$dispatch('selectbox:change', '');
            });
        },
        toggle() {
            this.open = !this.open;
        },
        select(opt) {
            this.value = opt.value;
            this.open = false;
            this.$dispatch('selectbox:change', opt.value);
        },
    }));

    Alpine.data('datePicker', (value = '', placeholder = 'Pilih Tanggal') => ({
        value: value || '',
        placeholder,
        open: false,
        mode: 'calendar',
        yearInput: new Date().getFullYear(),
        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        weekdays: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        viewDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
        init() {
            if (this.value) {
                const s = this.selected;
                if (s) this.viewDate = new Date(s.getFullYear(), s.getMonth(), 1);
            }
        },
        get selected() {
            if (!this.value) return null;
            const [y, m, d] = this.value.split('-').map(Number);
            if (!y || !m || !d) return null;
            return new Date(y, m - 1, d);
        },
        get label() {
            if (!this.value) return this.placeholder;
            const s = this.selected;
            return s
                ? s.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                : this.placeholder;
        },
        get year() {
            return this.viewDate.getFullYear();
        },
        get month() {
            return this.viewDate.getMonth();
        },
        get monthLabel() {
            return this.viewDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        },
        get days() {
            const lead = (new Date(this.year, this.month, 1).getDay() + 6) % 7;
            const total = new Date(this.year, this.month + 1, 0).getDate();
            const cells = [];
            for (let i = 0; i < lead; i++) cells.push(null);
            for (let d = 1; d <= total; d++) cells.push(d);
            return cells;
        },
        toggle() {
            this.open = !this.open;
        },
        prevMonth() {
            this.viewDate = new Date(this.year, this.month - 1, 1);
        },
        nextMonth() {
            this.viewDate = new Date(this.year, this.month + 1, 1);
        },
        toggleMode() {
            this.mode = this.mode === 'calendar' ? 'monthYear' : 'calendar';
            if (this.mode === 'monthYear') this.yearInput = this.year;
        },
        prevYear() {
            this.viewDate = new Date(this.year - 1, this.month, 1);
            this.yearInput = this.year;
        },
        nextYear() {
            this.viewDate = new Date(this.year + 1, this.month, 1);
            this.yearInput = this.year;
        },
        applyYear() {
            const y = parseInt(this.yearInput, 10);
            if (y >= 1900 && y <= 2100) {
                this.viewDate = new Date(y, this.month, 1);
            }
            this.yearInput = this.year;
        },
        goMonth(m) {
            this.viewDate = new Date(this.year, m, 1);
            this.mode = 'calendar';
        },
        isToday(d) {
            const t = new Date();
            return this.year === t.getFullYear() && this.month === t.getMonth() && d === t.getDate();
        },
        isSelected(d) {
            const s = this.selected;
            return !!s && this.year === s.getFullYear() && this.month === s.getMonth() && d === s.getDate();
        },
        selectDate(d) {
            const iso =
                this.year +
                '-' +
                String(this.month + 1).padStart(2, '0') +
                '-' +
                String(d).padStart(2, '0');
            this.value = iso;
            this.open = false;
            this.$dispatch('datepicker:change', iso);
        },
        setToday() {
            const t = new Date();
            this.viewDate = new Date(t.getFullYear(), t.getMonth(), 1);
            this.mode = 'calendar';
            this.selectDate(t.getDate());
        },
        clear() {
            this.value = '';
            this.open = false;
            this.$dispatch('datepicker:change', '');
        },
    }));
});

window.toast = (message, type = 'success') => Alpine.store('toast').show(message, type);

window.confirmDelete = (event, form) => {
    event.preventDefault();
    const dark = document.documentElement.classList.contains('dark');
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data ini akan dihapus permanen dan tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#E06B73',
        cancelButtonColor: dark ? '#30363B' : '#E2E8F0',
        background: dark ? '#1D2124' : '#FFFFFF',
        color: dark ? '#F1F3F4' : '#0F172A',
        reverseButtons: true,
        customClass: {
            popup: dark ? 'rounded-xl border border-[#30363B]' : 'rounded-xl border border-[#E2E8F0]',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
};

window.confirmReset = (event, form) => {
    event.preventDefault();
    const dark = document.documentElement.classList.contains('dark');
    Swal.fire({
        title: 'Reset password?',
        text: 'Password akun ini akan diatur ulang sama dengan NISN anggota.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, reset',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#E06B73',
        cancelButtonColor: dark ? '#30363B' : '#E2E8F0',
        background: dark ? '#1D2124' : '#FFFFFF',
        color: dark ? '#F1F3F4' : '#0F172A',
        reverseButtons: true,
        customClass: {
            popup: dark ? 'rounded-xl border border-[#30363B]' : 'rounded-xl border border-[#E2E8F0]',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
};

Alpine.start();

document.addEventListener('submit', function (event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.hasAttribute('data-no-auto-loading')) return;
    if (form.hasAttribute('onsubmit')) return;
    if (form.hasAttribute('@submit')) return;

    const btn = form.querySelector('button[type="submit"]');
    if (!btn || btn.disabled) return;
    if (btn.hasAttribute(':disabled') || btn.hasAttribute('x-show') || btn.querySelector('template')) return;

    btn.disabled = true;
    const label = btn.getAttribute('data-loading-text') || 'Menyimpan...';
    btn.innerHTML =
        '<span class="inline-flex items-center justify-center gap-2">' +
        '<svg class="oc-spinner w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>' +
        '<span>' + label + '</span>' +
        '</span>';
});

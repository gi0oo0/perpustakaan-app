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
        durations: [7, 14, 30],
        rates: [250, 500, 1000],
        duration,
        denda,
    }));

    Alpine.data('themeToggle', () => ({
        dark: !document.documentElement.classList.contains('light'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('light', !this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    }));

    Alpine.store('bookPreview', {
        data: null,
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

    Alpine.data('userSearch', (initialQuery = '') => ({
        open: false,
        query: initialQuery || '',
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
                    const params = new URLSearchParams({ q });
                    const role = document.querySelector('[name="role"]')?.value || '';
                    if (role) params.set('role', role);
                    const res = await fetch(
                        '/users/search?' + params.toString(),
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
        onPick(event) {
            const file = event.target.files[0];
            this.fileName = file ? file.name : '';
        },
    }));
});

window.toast = (message, type = 'success') => Alpine.store('toast').show(message, type);

window.confirmDelete = (event, form) => {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data ini akan dihapus permanen dan tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#fb7185',
        cancelButtonColor: '#334155',
        background: '#0b1220',
        color: '#ffffff',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl border border-white/10',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
};

Alpine.start();

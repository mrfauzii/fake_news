document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.lh-popular-page');
    if (!page) return;

    const searchRoute = page.dataset.searchUrl || '/pencarian';
    const grid = document.getElementById('popularGrid');
    const emptyState = document.getElementById('popularEmpty');
    const modal = document.getElementById('filterModal');
    const modalTitle = document.getElementById('filterModalTitle');
    const modalEyebrow = document.getElementById('filterModalEyebrow');
    const modalDescription = document.getElementById('filterModalDescription');
    const modalOptions = document.getElementById('filterModalOptions');
    const categoryLabel = document.getElementById('activeCategoryLabel');
    const periodLabel = document.getElementById('activePeriodLabel');
    const countLabel = document.getElementById('popularCountLabel');
    const triggerButtons = document.querySelectorAll('[data-filter-trigger]');
    const closeButtons = document.querySelectorAll('[data-filter-close]');

    const state = {
        category: 'all',
        period: 'Juni 2026',
        periodYear: 2026,
        modalType: null,
    };

    const monthNames = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];
    const categoryOptions = [
        { value: 'all', label: 'Semua Kategori', description: 'Tampilkan top 3 pencarian untuk bulan yang dipilih.' },
        { value: 'hoax', label: 'Hoax', description: 'Menampilkan berita hoax teratas.' },
        { value: 'fakta', label: 'Fakta', description: 'Menampilkan berita fakta teratas.' },
    ];

    const contentBlueprints = {
        hoax: [
            {
                headline: 'Pemerintah membagikan bantuan Sosial Ramadan 2026',
                excerpt: '[WASPADA PENTING] Pemerintah membagikan Bantuan Sosial Ramadan sebesar Rp1,5 juta bagi warga yang memiliki BPJS Kesehatan. Daftar sekarang melalui link Telegram ini agar dana segera cair.',
                query: 'Pemerintah membagikan bantuan Sosial Ramadan 2026',
                countBase: 2894,
            },
            {
                headline: 'Presiden Prabowo ajak masyarakat dukung Trump',
                excerpt: 'Beredar unggahan video yang mengeklaim Presiden Prabowo mengajak masyarakat mendukung Donald Trump dan memihak Amerika agar Indonesia aman dari teroris.',
                query: 'Presiden Prabowo ajak masyarakat dukung Trump',
                countBase: 2104,
            },
            {
                headline: 'Tautan dana darurat di pesan instan adalah penipuan berulang',
                excerpt: 'Unggahan yang mengaitkan bantuan dana darurat dengan tautan pesan instan tidak memiliki dasar resmi dan terdeteksi sebagai pola penipuan yang sering berulang.',
                query: 'Tautan dana darurat di pesan instan adalah penipuan berulang',
                countBase: 1704,
            },
        ],
        fakta: [
            {
                headline: 'Jadwal layanan publik dan bantuan sosial telah dikonfirmasi',
                excerpt: 'Hasil verifikasi menunjukkan informasi tentang jadwal layanan publik dan status bantuan sosial yang beredar telah dikonfirmasi oleh instansi terkait.',
                query: 'Jadwal layanan publik dan bantuan sosial telah dikonfirmasi',
                countBase: 986,
            },
            {
                headline: 'Proyek perbaikan jalan di kawasan wisata memang sedang berlangsung',
                excerpt: 'Pernyataan resmi dari pemerintah daerah memastikan bahwa proyek perbaikan jalan di kawasan wisata memang sedang berlangsung dan dapat dicek melalui pengumuman publik.',
                query: 'Proyek perbaikan jalan di kawasan wisata memang sedang berlangsung',
                countBase: 742,
            },
            {
                headline: 'Data cuaca dan peringatan resmi telah dirilis',
                excerpt: 'Informasi cuaca, peringatan dini, dan rilis resmi dari lembaga terkait dapat diverifikasi langsung pada kanal publik yang tersedia.',
                query: 'Data cuaca dan peringatan resmi telah dirilis',
                countBase: 621,
            },
        ],
    };

    const popularItems = buildPopularItems();

    function buildPopularItems() {
        return monthNames.flatMap((monthName, monthIndex) => buildMonthlyItems(monthName, 2026, monthIndex));
    }

    function buildMonthlyItems(monthName, year, monthIndex) {
        const period = `${monthName} ${year}`;
        const monthOffset = monthIndex * 37;

        // build only hoax and fakta items; 'all' will pick top 3 across these
        return [
            ...contentBlueprints.hoax.map((item, index) => createItem({
                category: 'hoax',
                period,
                badge: 'HOAX',
                count: item.countBase + monthOffset + (index * 13),
                headline: item.headline,
                excerpt: item.excerpt,
                query: `${item.query} ${monthName} ${year}`,
                sortOrder: index + 1,
            })),
            ...contentBlueprints.fakta.map((item, index) => createItem({
                category: 'fakta',
                period,
                badge: 'FAKTA',
                count: item.countBase + monthOffset + (index * 11),
                headline: item.headline,
                excerpt: item.excerpt,
                query: `${item.query} ${monthName} ${year}`,
                sortOrder: 4 + index,
            })),
        ];
    }

    function createItem({ category, period, badge, count, headline, excerpt, query, sortOrder }) {
        return {
            category,
            period,
            badge,
            count,
            headline,
            excerpt,
            query,
            sortOrder,
        };
    }

    function openModal(type) {
        state.modalType = type;

        modal.classList.toggle('lh-filter-modal--period', type === 'period');

        if (type === 'category') {
            modalEyebrow.textContent = 'Filter kategori';
            modalTitle.textContent = 'Pilih kategori pencarian';
            modalDescription.textContent = 'Pilih apakah Anda ingin melihat topik hoax, fakta, atau semua kategori.';
            renderOptions(categoryOptions, state.category, type);
        } else {
            const periodParts = parsePeriod(state.period);
            state.periodYear = periodParts.year;
            modalEyebrow.textContent = 'Filter periode';
            modalTitle.textContent = 'Pilih bulan dan tahun';
            modalDescription.textContent = 'Pilih periode bulan dan tahun untuk melihat tema pencarian yang paling ramai.';
            renderPeriodPicker(state.periodYear);
        }

        modal.removeAttribute('hidden');
        document.body.classList.add('lh-filter-lock');
    }

    function closeModal() {
        modal.setAttribute('hidden', '');
        document.body.classList.remove('lh-filter-lock');
        modal.classList.remove('lh-filter-modal--period');
        state.modalType = null;
    }

    function renderOptions(options, selectedValue, type) {
        modalOptions.innerHTML = options.map(option => {
            const isSelected = option.value === selectedValue;
            return `
                <button type="button" class="lh-filter-option ${isSelected ? 'lh-filter-option--selected' : ''}" data-option-value="${escapeHtml(option.value)}" data-option-type="${escapeHtml(type)}">
                    <span class="lh-filter-option__label">${escapeHtml(option.label)}</span>
                    <span class="lh-filter-option__description">${escapeHtml(option.description)}</span>
                </button>
            `;
        }).join('');
    }

    function renderPeriodPicker(year) {
        modalOptions.innerHTML = `
            <div class="lh-period-picker">
                <div class="lh-period-picker__yearbar">
                    <button type="button" class="lh-period-picker__nav" data-period-nav="prev" aria-label="Tahun sebelumnya">
                        <iconify-icon icon="mdi:chevron-left" width="30" height="30"></iconify-icon>
                    </button>
                    <div class="lh-period-picker__year">${escapeHtml(String(year))}</div>
                    <button type="button" class="lh-period-picker__nav" data-period-nav="next" aria-label="Tahun berikutnya">
                        <iconify-icon icon="mdi:chevron-right" width="30" height="30"></iconify-icon>
                    </button>
                </div>
                <div class="lh-period-picker__months">
                    ${monthNames.map(monthName => {
                        const optionValue = `${monthName} ${year}`;
                        const isSelected = optionValue === state.period;
                        return `
                            <button type="button" class="lh-period-picker__month ${isSelected ? 'lh-period-picker__month--selected' : ''}" data-option-value="${escapeHtml(optionValue)}" data-option-type="period">
                                ${escapeHtml(monthName)}
                            </button>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    function parsePeriod(period) {
        const match = String(period).match(/^(.+)\s(\d{4})$/);
        if (!match) {
            return { month: 'Juni', year: 2026 };
        }

        return {
            month: match[1],
            year: Number(match[2]),
        };
    }

    function shiftPeriodYear(delta) {
        state.periodYear += delta;
        renderPeriodPicker(state.periodYear);
    }

    function updateLabels() {
        const categoryDisplayMap = {
            all: 'Semua Kategori',
            hoax: 'Hoax',
            fakta: 'Fakta',
        };
        const categoryDisplay = categoryDisplayMap[state.category] || 'Semua Kategori';
        
        if (categoryLabel) {
            categoryLabel.textContent = categoryDisplay;
        }
        if (periodLabel) {
            periodLabel.textContent = state.period;
        }
        
        // Update trigger buttons in title
        const categoryTrigger = document.querySelector('[data-filter-trigger="category"]');
        const periodTrigger = document.querySelector('[data-filter-trigger="period"]');
        
        if (categoryTrigger) {
            categoryTrigger.textContent = categoryDisplay;
        }
        if (periodTrigger) {
            periodTrigger.textContent = state.period;
        }
    }

    function renderGrid() {
        // Filter items by selected period first
        const itemsForPeriod = popularItems.filter(item => item.period === state.period);

        let itemsToDisplay = [];

        if (state.category === 'all') {
            // across all categories, show top 3 by count
            itemsToDisplay = itemsForPeriod.slice().sort((a, b) => b.count - a.count).slice(0, 3);
        } else {
            // specific category (popular/hoax/fakta) show top 3 in that category
            itemsToDisplay = itemsForPeriod.filter(item => item.category === state.category)
                .slice()
                .sort((a, b) => b.count - a.count)
                .slice(0, 3);
        }

        if (countLabel) {
            countLabel.textContent = `${itemsToDisplay.length.toLocaleString('id-ID')} hasil ditemukan`;
        }

        emptyState.hidden = itemsToDisplay.length > 0;

        grid.innerHTML = itemsToDisplay.map((item, idx) => {
            const detailUrl = buildSearchUrl(item.query);
            const displayRank = idx + 1; // 1..3
            const rankClass = item.category === 'hoax' ? 'rank-hoax' : 'rank-fakta';

            return `
                <article class="lh-popular-card">
                    <div class="lh-popular-card__rank ${rankClass}" data-rank="${displayRank}">#${displayRank} ${item.badge}</div>
                    <div class="lh-popular-card__excerpt">${escapeHtml(item.excerpt)}</div>
                    <div class="lh-popular-card__content">
                        <div class="lh-popular-card__row">
                            <span class="lh-popular-card__badge">${escapeHtml(item.badge)}</span>
                            <h3 class="lh-popular-card__headline">${escapeHtml(item.headline)}</h3>
                        </div>
                        <p class="lh-popular-card__count"><strong>${item.count.toLocaleString('id-ID')}</strong> orang mencari informasi serupa</p>
                        <div class="lh-popular-card__footer">
                            <a class="lh-popular-card__btn" href="${escapeHtml(detailUrl)}">Detail Lengkap</a>
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    function buildSearchUrl(query) {
        const url = new URL(searchRoute, window.location.origin);
        url.searchParams.set('informasi', query);
        return url.toString();
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    triggerButtons.forEach(button => {
        button.addEventListener('click', function () {
            openModal(this.dataset.filterTrigger);
        });
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal || event.target.classList.contains('lh-filter-modal__overlay')) {
            closeModal();
        }
    });

    modalOptions.addEventListener('click', function (event) {
        const periodNavButton = event.target.closest('[data-period-nav]');
        if (periodNavButton) {
            const direction = periodNavButton.dataset.periodNav;
            shiftPeriodYear(direction === 'next' ? 1 : -1);
            return;
        }

        const optionButton = event.target.closest('[data-option-value]');
        if (!optionButton) return;

        const value = optionButton.dataset.optionValue;
        const type = optionButton.dataset.optionType;

        if (type === 'category') {
            state.category = value;
        } else {
            state.periodYear = parsePeriod(value).year;
            state.period = value;
        }

        updateLabels();
        renderGrid();
        closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
            closeModal();
        }
    });

    updateLabels();
    renderGrid();
});
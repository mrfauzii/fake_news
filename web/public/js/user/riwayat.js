// Data Riwayat Manager (Versi Database Live)
class RiwayatManager {
    constructor() {
        this.riwayatData = window.realRiwayatData || [];
        this.statusModal = null;
        console.log('RiwayatManager initialized with data:', this.riwayatData);
        this.render();
        this.attachEventListeners();
    }

    // Hapus umpan balik milik pengguna untuk sebuah request (frontend)
    deleteFeedback(item) {
        const requestId = item.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        this.showStatusModal({
            type: 'loading',
            title: 'Menghapus umpan balik',
            message: 'Mohon tunggu sebentar, umpan balik sedang dihapus.',
            dismissible: false,
        });

        fetch('/feedback', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ request_id: requestId })
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.success) {
                // hapus properti feedback dari data lokal sehingga user bisa kirim lagi
                this.riwayatData = this.riwayatData.map(d => {
                    if (parseInt(d.id) === parseInt(requestId)) {
                        delete d.feedback;
                    }
                    return d;
                });
                this.render();
                this.showToast('success', 'Umpan balik dihapus', data.message || 'Umpan balik berhasil dihapus.', 1800);
            } else {
                this.showToast('error', 'Gagal menghapus', data.message || 'Gagal menghapus umpan balik.', 2600);
            }
        })
        .catch(err => {
            console.error('Delete feedback error:', err);
            this.showToast('error', 'Terjadi kesalahan', 'Terjadi kesalahan saat menghapus umpan balik.', 2600);
        });
    }

    getData() {
        return this.riwayatData;
    }

    formatDateDisplay(date) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const d = new Date(date);
        const dayName = days[d.getDay()];
        const day = d.getDate();
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        
        return `${dayName}, ${day} ${month} ${year}`;
    }

    render() {
        const container = document.getElementById('riwayatContainer');
        const emptyState = document.getElementById('emptyState');
        const data = this.getData();

        if (data.length === 0) {
            container.style.display = 'none';
            emptyState.style.display = 'flex';
            return;
        }

        container.style.display = 'flex';
        emptyState.style.display = 'none';
        container.innerHTML = data.map(item => this.renderItem(item)).join('');
        this.attachItemEventListeners();
    }

    renderItem(item) {
        const hoaxPercent = item.status === 'fakta' ? 0 : (Number(item.confidence) || 0);
        const queryPreview = this.truncateText(item.query, 88);
        const resultLabel = item.status === 'benar' ? 'fakta' : item.status === 'palsu' ? 'Palsu' : 'Hoax';
        const feedbackHtml = item.feedback && item.feedback.feedback
            ? `
                <div class="riwayat-feedback-display">
                    <div class="riwayat-feedback-display__head">
                        <span class="riwayat-feedback-display__label">Umpan Balik Anda</span>
                        <span class="riwayat-feedback-display__badge">
                            <iconify-icon icon="mdi:check-circle-outline" width="14" height="14"></iconify-icon>
                            Terkirim
                        </span>
                    </div>
                    <p class="riwayat-feedback-display__text">${this.escapeHtml(item.feedback.feedback)}</p>
                    <div class="riwayat-feedback-display__footer">
                        <div class="riwayat-feedback-display__meta">
                            <iconify-icon icon="mdi:clock-outline" width="14" height="14"></iconify-icon>
                            ${this.formatDateDisplay(item.feedback.created_at)}
                        </div>
                        <button class="riwayat-action-btn hapus-feedback riwayat-feedback-delete" title="Hapus umpan balik" data-id="${item.id}">
                            <iconify-icon icon="mdi:trash-can-outline" width="16" height="16"></iconify-icon>
                        </button>
                    </div>
                </div>
              `
            : '';

        return `
            <div class="riwayat-group">
                <div class="riwayat-group-date">${this.formatDateDisplay(item.date)}</div>
                <div class="riwayat-item-container" data-id="${item.id}">
                    <div class="riwayat-section riwayat-query-box">
                        <button type="button" class="riwayat-section-toggle" data-section-toggle="query-${item.id}" aria-expanded="false">
                            <span class="riwayat-section-head">
                                <span class="riwayat-section-kicker">Informasi Berita</span>
                                <span class="riwayat-box-title">Klik untuk melihat detail berita</span>
                            </span>
                            <span class="riwayat-section-toggle-meta">
                                <span class="riwayat-section-preview">${this.escapeHtml(queryPreview)}</span>
                                <iconify-icon icon="mdi:chevron-down" width="18" height="18" class="riwayat-section-chevron"></iconify-icon>
                            </span>
                        </button>
                        <div class="riwayat-section-panel" id="query-${item.id}" hidden>
                            <p class="riwayat-query">${this.escapeHtml(item.query)}</p>
                        </div>
                    </div>

                    <div class="riwayat-section riwayat-results-box">
                        <button type="button" class="riwayat-section-toggle" data-section-toggle="result-${item.id}" aria-expanded="false">
                            <span class="riwayat-section-head">
                                <span class="riwayat-section-kicker">Hasil Pencarian</span>
                                <span class="riwayat-box-title">Klik untuk lihat ringkasan dan tautan</span>
                            </span>
                            <span class="riwayat-section-toggle-meta">
                                <span class="riwayat-result-chip ${item.status}">${hoaxPercent}% ${resultLabel}</span>
                                <iconify-icon icon="mdi:chevron-down" width="18" height="18" class="riwayat-section-chevron"></iconify-icon>
                            </span>
                        </button>
                        <div class="riwayat-section-panel" id="result-${item.id}" hidden>
                            <div class="riwayat-result-content">
                                <div class="riwayat-result-percent ${item.status}">
                                    <span class="riwayat-result-percent-value">${hoaxPercent}%</span>
                                    <span class="riwayat-result-percent-label">${resultLabel}</span>
                                </div>
                                <div class="riwayat-result-summary">${this.convertUrlsToLinks(item.description)}</div>
                                <p class="riwayat-result-footnote">Tautan pada teks dapat diklik langsung dan dibuka di tab baru.</p>
                            </div>
                        </div>

                        ${feedbackHtml}
                    </div>

                    <div class="riwayat-item-actions">
                        <button class="riwayat-action-btn umpan-balik" title="Kirim umpan balik" data-id="${item.id}">
                            <iconify-icon icon="mdi:comment" width="16" height="16"></iconify-icon>
                        </button>
                        <button class="riwayat-action-btn hapus" title="Hapus dari riwayat" data-id="${item.id}">
                            <iconify-icon icon="mdi:trash" width="16" height="16"></iconify-icon>
                        </button>
       
                    </div>
                </div>
            </div>
        `;
    }

    escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    truncateText(text, maxLength) {
        if (typeof text !== 'string') return '';
        const normalized = text.replace(/\s+/g, ' ').trim();
        return normalized.length <= maxLength ? normalized : `${normalized.slice(0, maxLength).trimEnd()}...`;
    }

    convertUrlsToLinks(text) {
        if (typeof text !== 'string') return this.escapeHtml(String(text));
        let escaped = this.escapeHtml(text);
        const urlRegex = /https?:\/\/[^\s<>\"]+/gi;
        // First convert URLs to anchors
        let converted = escaped.replace(urlRegex, (url) => {
            let cleanUrl = url;
            let suffix = '';
            while (cleanUrl && /[.,;:!?\)]$/.test(cleanUrl)) {
                suffix = cleanUrl.slice(-1) + suffix;
                cleanUrl = cleanUrl.slice(0, -1);
            }
            const href = cleanUrl.replace(/&amp;/g, '&');
            return `<a href="${href}" target="_blank" rel="noopener noreferrer" style="color: #c41e3a; text-decoration: underline; font-weight: 500; cursor: pointer;">${cleanUrl}</a>${suffix}`;
        });

        // Remove pipe separators between links so they stay tight and compact
        converted = converted.replace(/\s*\|\s*/g, '');

        return converted;
    }

    showStatusModal({ type = 'info', title = '', message = '', actions = [], autoClose = 0, dismissible = true }) {
        this.closeStatusModal();

        const iconMap = {
            loading: 'mdi:progress-clock',
            success: 'mdi:check-circle',
            error: 'mdi:alert-circle',
            confirm: 'mdi:help-circle',
            info: 'mdi:information',
        };

        const typeClass = `riwayat-status-modal--${type}`;
        const actionButtons = actions.length
            ? actions.map(action => `
                    <button type="button" class="riwayat-status-modal__btn ${action.variant ? `riwayat-status-modal__btn--${action.variant}` : ''}" data-action="${action.id}">
                        ${action.label}
                    </button>
                `).join('')
            : '';

        const modalHtml = `
            <div class="riwayat-status-modal ${typeClass}" id="riwayatStatusModal" role="dialog" aria-modal="true" aria-labelledby="riwayatStatusTitle" aria-describedby="riwayatStatusMessage">
                <div class="riwayat-status-modal__overlay"></div>
                <div class="riwayat-status-modal__card">
                    <div class="riwayat-status-modal__icon-wrap">
                        ${type === 'loading'
                            ? '<span class="riwayat-status-modal__spinner"></span>'
                            : `<iconify-icon icon="${iconMap[type] || iconMap.info}" width="30" height="30"></iconify-icon>`}
                    </div>
                    <div class="riwayat-status-modal__body">
                        <h3 class="riwayat-status-modal__title" id="riwayatStatusTitle">${this.escapeHtml(title)}</h3>
                        <p class="riwayat-status-modal__message" id="riwayatStatusMessage">${this.escapeHtml(message)}</p>
                    </div>
                    ${actionButtons ? `<div class="riwayat-status-modal__actions">${actionButtons}</div>` : ''}
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById('riwayatStatusModal');
        this.statusModal = modal;

        const close = () => this.closeStatusModal();

        if (dismissible) {
            modal.querySelector('.riwayat-status-modal__overlay')?.addEventListener('click', close);
        }

        modal.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', () => {
                const actionId = button.dataset.action;
                const action = actions.find(item => item.id === actionId);
                if (action?.handler) action.handler();
            });
        });

        if (autoClose > 0) {
            window.clearTimeout(this.statusModalTimer);
            this.statusModalTimer = window.setTimeout(() => {
                this.closeStatusModal();
            }, autoClose);
        }

        return modal;
    }

    closeStatusModal() {
        if (this.statusModal) {
            this.statusModal.remove();
            this.statusModal = null;
        }
        if (this.statusModalTimer) {
            window.clearTimeout(this.statusModalTimer);
            this.statusModalTimer = null;
        }
    }

    showToast(type, title, message, autoClose = 2200) {
        return this.showStatusModal({
            type,
            title,
            message,
            autoClose,
            dismissible: true,
        });
    }

    showConfirmDeleteModal({ title, message, onConfirm }) {
        return this.showStatusModal({
            type: 'confirm',
            title,
            message,
            dismissible: true,
            actions: [
                { id: 'cancel', label: 'Batal', variant: 'secondary', handler: () => this.closeStatusModal() },
                { id: 'confirm', label: 'Hapus', variant: 'danger', handler: onConfirm },
            ],
        });
    }

    // Fungsi Hapus (Soft Delete ke Server)
    deleteItem(id) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.showStatusModal({
            type: 'loading',
            title: 'Menghapus riwayat',
            message: 'Mohon tunggu sebentar, data sedang diproses.',
            dismissible: false,
        });

        fetch(`/riwayat-saya/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.riwayatData = this.riwayatData.filter(item => item.id !== parseInt(id));
                this.render();
                this.showToast('success', 'Riwayat terhapus', data.message || 'Item riwayat berhasil dihapus.', 1800);
            } else {
                this.showToast('error', 'Gagal menghapus', data.message || 'Gagal menghapus riwayat.', 2600);
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            this.showToast('error', 'Terjadi kesalahan', 'Terjadi kesalahan saat menghapus riwayat.', 2600);
        });
    }


    // Fungsi Hapus Semua Riwayat (Mass Soft Delete ke Server)
    deleteAll() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        this.showStatusModal({
            type: 'loading',
            title: 'Menghapus semua riwayat',
            message: 'Seluruh data riwayat sedang dihapus.',
            dismissible: false,
        });

        fetch('/riwayat-saya', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.riwayatData = [];
                this.render();
                this.showToast('success', 'Riwayat dihapus', data.message || 'Seluruh riwayat berhasil dihapus.', 2000);
            } else {
                this.showToast('error', 'Gagal menghapus', data.message || 'Gagal menghapus semua riwayat.', 2600);
            }
        })
        .catch(err => {
            console.error('Delete all error:', err);
            this.showToast('error', 'Terjadi kesalahan', 'Terjadi kesalahan saat menghapus semua riwayat.', 2600);
        });
    }

    attachEventListeners() {
        // 🔥 PERBAIKAN: Hubungkan kembali ke fungsi deleteAll() tanpa alert izin admin
        const hapusSemuaBtn = document.getElementById('hapusSemuaBtn');
        if (hapusSemuaBtn) {
            hapusSemuaBtn.addEventListener('click', () => {
                this.showConfirmDeleteModal({
                    title: 'Hapus semua riwayat?',
                    message: 'Tindakan ini akan menghapus seluruh riwayat pencarian Anda dan tidak dapat dibatalkan.',
                    onConfirm: () => this.deleteAll(),
                });
            });
        }
    }

    attachItemEventListeners() {
        // Accordion Toggle
        document.querySelectorAll('.riwayat-section-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const button = e.currentTarget;
                const targetId = button.dataset.sectionToggle;
                const panel = document.getElementById(targetId);
                if (!panel) return;

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!isExpanded));
                panel.hidden = isExpanded;
                button.closest('.riwayat-section')?.classList.toggle('is-open', !isExpanded);
                const icon = button.querySelector('.riwayat-section-chevron');
                if (icon) icon.setAttribute('icon', isExpanded ? 'mdi:chevron-down' : 'mdi:chevron-up');
            });
        });

        // Tombol Feedback
        document.querySelectorAll('.riwayat-action-btn.umpan-balik').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const data = this.getData();
                const itemData = data.find(d => d.id === parseInt(id));
                if (itemData) this.showFeedbackForm(itemData);
            });
        });

        // Tombol Hapus Umpan Balik
        document.querySelectorAll('.riwayat-action-btn.hapus-feedback').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const data = this.getData();
                const item = data.find(d => d.id === parseInt(id));
                if (!item) return;

                this.showConfirmDeleteModal({
                    title: 'Hapus umpan balik?',
                    message: 'Umpan balik Anda untuk pencarian ini akan dihapus. Setelah dihapus, Anda dapat mengirim umpan balik baru.',
                    onConfirm: () => this.deleteFeedback(item)
                });
            });
        });

        // 🔥 KEMBALI: Tombol Hapus 
        document.querySelectorAll('.riwayat-action-btn.hapus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.showConfirmDeleteModal({
                    title: 'Hapus item riwayat?',
                    message: 'Item ini akan dihapus dari riwayat pencarian Anda.',
                    onConfirm: () => this.deleteItem(id),
                });
            });
        });

    }
    
    // Modal Feedback
    showFeedbackForm(item) {
        // Jika user sudah mengirim feedback untuk item ini, tampilkan saja
        if (item.feedback && item.feedback.feedback) {
            return this.showStatusModal({
                type: 'info',
                title: 'Umpan balik sudah dikirim',
                message: 'Umpan balik untuk pencarian ini sudah tersimpan dan tampil di bawah hasil pencarian.',
                autoClose: 4000
            });
        }

        const feedbackHtml = `
            <div class="riwayat-feedback-modal" id="feedbackModal">
                <div class="riwayat-feedback-content">
                    <div class="riwayat-feedback-header">
                        <div class="riwayat-feedback-header__titlewrap">
                            <span class="riwayat-feedback-header__kicker">Riwayat Pencarian</span>
                            <h3>Kirim Umpan Balik</h3>
                        </div>
                        <button class="riwayat-feedback-close" id="closeFeedback" aria-label="Tutup modal">
                            <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                        </button>
                    </div>
                    <div class="riwayat-feedback-body">
                        <div class="riwayat-feedback-item">
                            <span class="riwayat-feedback-item__label">Pencarian</span>
                            <p>${this.escapeHtml(item.query)}</p>
                        </div>
                        <label class="riwayat-feedback-label" for="feedbackText">Isi umpan balik</label>
                        <textarea class="riwayat-feedback-textarea" id="feedbackText" placeholder="Tulis umpan balik Anda di sini..." rows="5"></textarea>
                        <p class="riwayat-feedback-hint">Masukan Anda membantu kami meningkatkan kualitas verifikasi.</p>
                    </div>
                    <div class="riwayat-feedback-footer">
                        <button class="riwayat-feedback-cancel" id="cancelFeedback">Batal</button>
                        <button class="riwayat-feedback-submit" id="submitFeedback">
                            <iconify-icon icon="mdi:send" width="16" height="16"></iconify-icon>
                            Kirim Umpan Balik
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const existing = document.getElementById('feedbackModal');
        if (existing) existing.remove();
        
        document.body.insertAdjacentHTML('beforeend', feedbackHtml);
        
        const modal = document.getElementById('feedbackModal');
        const closeBtn = document.getElementById('closeFeedback');
        const cancelBtn = document.getElementById('cancelFeedback');
        const submitBtn = document.getElementById('submitFeedback');
        
        const closeModal = () => modal.remove();
        
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        
        submitBtn.addEventListener('click', () => {
            const text = document.getElementById('feedbackText').value.trim();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            if (!text) {
                this.showToast('error', 'Umpan balik kosong', 'Silakan tulis umpan balik Anda terlebih dahulu.', 2400);
                return;
            }

            submitBtn.disabled = true;
            this.closeStatusModal();
            this.showStatusModal({
                type: 'loading',
                title: 'Mengirim umpan balik',
                message: 'Pesan Anda sedang dikirim ke server.',
                dismissible: false,
            });
            fetch('/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    feedback: text,
                    request_id: item.id
                }),
            })
            .then(resp => resp.json())
            .then(resdata => {
                // Jika berhasil, perbarui data lokal sehingga feedback muncul di halaman
                if (resdata.success) {
                    // update lokal riwayatData: tambahkan field feedback ke item
                    this.riwayatData = this.riwayatData.map(d => {
                        if (parseInt(d.id) === parseInt(item.id)) {
                            d.feedback = d.feedback || {};
                            d.feedback.feedback = resdata.data?.feedback || text;
                            d.feedback.created_at = resdata.data?.created_at || new Date().toISOString();
                        }
                        return d;
                    });
                    this.render();
                }

                closeModal();
                this.showToast(resdata.success ? 'success' : 'error', resdata.success ? 'Terkirim' : 'Gagal', resdata.message || 'Umpan balik diproses.', 2200);
            })
            .catch(err => {
                this.closeStatusModal();
                this.showToast('error', 'Gagal mengirim', 'Terjadi kesalahan saat mengirim umpan balik.', 2600);
                submitBtn.disabled = false;
            });
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        }, { once: true });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.riwayatManager = new RiwayatManager();
});
@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    {{-- Use top banner for session flash --}}
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', function(){ showAdminBanner("{{ session('success') }}", 'success'); });</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', function(){ showAdminBanner("{{ session('error') }}", 'error'); });</script>
    @endif

    <div class="page-header-top">
        <div class="page-title-box">
            <h1>Riwayat Global</h1>
            <p>Daftar lengkap seluruh verifikasi berita yang telah dilakukan oleh sistem dan moderator</p>
        </div>

        <div class="page-header-right">
            <div class="search-wrapper">
                <form action="{{ url()->current() }}" method="GET" class="search-wrapper" id="searchForm">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="search-input" id="searchInput">
                    <button type="submit" class="search-btn">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

            <button class="btn-export" onclick="window.location.href='{{ route('riwayat.unduh_csv') }}'">
                <i class="fa fa-download"></i>
                Export CSV
            </button>
        </div>
    </div>

    <div class="riwayat-grid" id="riwayatGrid">
        @forelse ($data as $item)
            @php
                $imagePath = data_get($item, 'gambar');
                $userText = data_get($item, 'deskripsi', '(Tidak ada teks input)');
                $hasImage = !empty($imagePath);
            @endphp

            <div class="riwayat-card searchable-card"
    data-title="{{ strtolower(data_get($item, 'judul', '')) }}" 
    data-id="{{ $loop->index }}"
    data-request-id="{{ data_get($item, 'request_id') }}" 
    data-deleted="{{ data_get($item, 'is_deleted') ? 'true' : 'false' }}"
    data-deleted-date="{{ data_get($item, 'deleted_at') }}">

    {{-- SEMUA ISI ATAS DIJADIKAN SATU WADAH FLEXBOX --}}
    <div class="card-header">
        
        {{-- 1. JUDUL (SELALU DI ATAS) --}}
        <div class="warning-title" @if(data_get($item, 'is_deleted')) style="text-decoration: line-through; opacity: 0.6; color: #dc3545;" @endif>
            @if (!$hasImage) <i class="fa fa-exclamation-triangle warning-icon"></i> @endif
            {{ data_get($item, 'judul') }}
            @if (!$hasImage) <i class="fa fa-exclamation-triangle warning-icon"></i> @endif
            @if(data_get($item, 'is_deleted'))
                <span style="margin-left: 10px; background: #dc3545; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-decoration: none;">DIHAPUS</span>
            @endif
        </div>

        {{-- 2. KONTEN (GAMBAR ATAU TEKS) --}}
        @if ($hasImage && is_string($imagePath))
            {{-- Jika ada gambar --}}
            <div class="card-image-wrapper" @if(data_get($item, 'is_deleted')) style="opacity: 0.6;" @endif>
                <img src="{{ asset($imagePath) }}" class="card-img" onerror="this.parentNode.style.display='none'">
            </div>
        @else
            {{-- Jika tidak ada gambar (Tampilkan Teks) --}}
            <p class="desc" @if(data_get($item, 'is_deleted')) style="opacity: 0.6; text-decoration: line-through;" @endif>
                {{ $userText }}
            </p>
        @endif
        
    </div>

    <div class="divider"></div>

    <div class="card-bottom">
                    <div class="progress-circle">
    @php
        // Mengambil data persentase (Default 0 jika kosong)
        $hoaxPercent = data_get($item, 'hoax', 0);
        $benarPercent = data_get($item, 'benar', 0);
        
        // Total panjang busur setengah lingkaran (r = 50) adalah ~157
        $totalLength = 157; 
        
        // Kalkulasi panjang masing-masing warna berdasarkan persentase database
        $redStroke = ($hoaxPercent / 100) * $totalLength;
        $greenStroke = ($benarPercent / 100) * $totalLength;
    @endphp

    <svg viewBox="0 0 120 60">
        <path d="M10 60 A50 50 0 0 1 110 60" class="bg" />
        
        @if($hoaxPercent > 0)
            <path d="M10 60 A50 50 0 0 1 110 60" class="progress-red" 
                  style="stroke-dasharray: {{ $redStroke }} {{ $totalLength - $redStroke }};" />
        @endif
              
        @if($benarPercent > 0)
            <path d="M10 60 A50 50 0 0 1 110 60" class="progress-green" 
                  style="stroke-dasharray: {{ $greenStroke }} {{ $totalLength - $greenStroke }}; 
                         stroke-dashoffset: -{{ $redStroke }};" />
        @endif
    </svg>
    <span>{{ $hoaxPercent }}%</span>
</div>

                    <div class="legend">
                        <p><span class="dot red"></span> Hoax: {{ data_get($item, 'hoax', 0) }}%</p>
                        <p><span class="dot green"></span> Benar: {{ data_get($item, 'benar', 0) }}%</p>
                    </div>

                    <button class="btn-detail open-popup" 
            data-judul="{{ data_get($item, 'judul') }}"
            data-deskripsi="{{ $userText }}" 
            data-gambar="{{ $hasImage && is_string($imagePath) ? asset($imagePath) : '' }}"
            data-penjelasan="{{ data_get($item, 'penjelasan') }}"
            data-hoax="{{ data_get($item, 'hoax', 0) }}" 
            data-benar="{{ data_get($item, 'benar', 0) }}" 
            data-user="{{ data_get($item, 'user') }}"
            data-date="{{ data_get($item, 'date') }}">
            Selengkapnya
        </button>
    </div>
</div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #999;">
                Tidak ada data riwayat yang ditemukan.
            </div>
        @endforelse
    </div>

<div class="pagination-wrapper">
    @if ($data->lastPage() > 1)

        @if($data->currentPage() > 1)
            <a href="{{ $data->previousPageUrl() }}">Previous</a>
        @else
            <a class="disabled">Previous</a>
        @endif

        @if($data->currentPage() > 3)
            <a href="{{ $data->url(1) }}" class="page-number">1</a>

            @if($data->currentPage() > 4)
                <span class="page-number disabled">...</span>
            @endif
        @endif

        @for($i = max(1, $data->currentPage() - 2); $i <= min($data->lastPage(), $data->currentPage() + 2); $i++)
            <a href="{{ $data->url($i) }}"
               class="page-number {{ $data->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if($data->currentPage() < $data->lastPage() - 2)

            @if($data->currentPage() < $data->lastPage() - 3)
                <span class="page-number disabled">...</span>
            @endif

            <a href="{{ $data->url($data->lastPage()) }}" class="page-number">
                {{ $data->lastPage() }}
            </a>
        @endif

        @if($data->hasMorePages())
            <a href="{{ $data->nextPageUrl() }}">Next</a>
        @else
            <a class="disabled">Next</a>
        @endif

    @endif
</div>

    {{-- POPUP OVERLAY --}}
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-box">
            <div class="popup-actions">
                <button id="deletePopup"><i class="fa fa-trash"></i></button>
                <button id="permanentDeletePopup" style="display:none;"><i class="fa fa-trash-can"></i></button>
                <button id="closePopup"><i class="fa fa-times"></i></button>
            </div>

            <div class="popup-top">
                <p class="popup-user" id="popupUser"></p>
                <p class="popup-date" id="popupDate"></p>
                <p class="popup-deleted" id="popupDeleteStatus" style="display: none;"></p>
            </div>

            <div class="popup-content">
    <div class="popup-title" id="popupTitle">Judul</div>
    
    {{-- Tambahan untuk nampilin gambar di popup --}}
    <img id="popupImage" src="" alt="Gambar Berita" style="display: none;">
    
    <p class="popup-desc" id="popupDesc">Isi berita</p>
</div>

            <div class="popup-divider"></div>

            <div class="popup-bottom">
                <div class="popup-legend">
                    <p><span class="dot red"></span> Data terdeteksi hoax sebesar <strong id="popupHoax">0%</strong></p>
                    <p><span class="dot green"></span> Data terdeteksi benar sebesar <strong id="popupBenar">0%</strong></p>
                </div>
                <div class="popup-result">
                    <p class="popup-penjelasan" id="popupPenjelasan"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PERMANEN --}}
    <div class="permanent-overlay" id="permanentOverlay">
        <div class="permanent-popup">
            <h2>Hapus Riwayat</h2>
            <p>Apakah anda yakin ingin menghapus riwayat ini secara permanen?</p>
            <div class="permanent-actions">
                <button id="confirmPermanentDelete" class="btn-permanent-delete">Hapus</button>
                <button id="cancelPermanentDelete" class="btn-permanent-cancel">Batal</button>
            </div>
        </div>
    </div>
<div class="permanent-overlay" id="restoreOverlay">
    <div class="permanent-popup">
        <h2>Pulihkan Riwayat</h2>
        <p>Apakah anda yakin ingin memulihkan riwayat ini kembali ke daftar aktif?</p>
        <div class="permanent-actions">
            <button id="confirmRestore" class="btn-permanent-delete" style="background-color: #28a745;">Ya, Pulihkan</button>
            <button id="cancelRestore" class="btn-permanent-cancel">Batal</button>
        </div>
    </div>
</div>
<div id="loadingOverlay">
    <i class="fa fa-spinner fa-spin"></i>
    <p style="margin:0;">Memproses...</p>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const popupOverlay = document.getElementById('popupOverlay');
            const closePopup = document.getElementById('closePopup');
            const deletePopup = document.getElementById('deletePopup');
            const permanentDeletePopup = document.getElementById('permanentDeletePopup');
            const permanentOverlay = document.getElementById('permanentOverlay');
            const confirmPermanentDelete = document.getElementById('confirmPermanentDelete');
            const cancelPermanentDelete = document.getElementById('cancelPermanentDelete');

            const popupTitle = document.getElementById('popupTitle');
            const popupDesc = document.getElementById('popupDesc');
            const popupImage = document.getElementById('popupImage');
            const popupHoax = document.getElementById('popupHoax');
            const popupBenar = document.getElementById('popupBenar');
            const popupDeleteStatus = document.getElementById('popupDeleteStatus');
            const popupUser = document.getElementById('popupUser');
            const popupDate = document.getElementById('popupDate');
            const popupPenjelasan = document.getElementById('popupPenjelasan');

            let currentCard = null;
            let debounceTimeout;

            // ==========================================
            // LOGIK FUNGSI BANNER ALERT ALA SETTING
            // ==========================================
            function triggerSettingAlert(message, type = 'success') {
                showAdminBanner(message, type);
            }
            // ==========================================

            function fetchLiveData(url) {
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newGrid = doc.getElementById('riwayatGrid');
                    const currentGrid = document.getElementById('riwayatGrid');
                    if (newGrid && currentGrid) { currentGrid.innerHTML = newGrid.innerHTML; }

                    const newPagination = doc.querySelector('.pagination-wrapper');
                    const currentPagination = document.querySelector('.pagination-wrapper');
                    if (currentPagination) { currentPagination.innerHTML = newPagination ? newPagination.innerHTML : ''; }
                })
                .catch(error => console.error('Error loading data:', error));
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimeout);
                    const keyword = this.value;
                    debounceTimeout = setTimeout(() => {
                        const url = new URL(window.location.origin + window.location.pathname);
                        if (keyword) { url.searchParams.set('search', keyword); }
                        url.searchParams.delete('page');
                        window.history.pushState({}, '', url);
                        fetchLiveData(url);
                    }, 300);
                });
            }

            if (searchForm) { searchForm.addEventListener('submit', (e) => e.preventDefault()); }

            document.addEventListener('click', function (e) {
                const targetAnchor = e.target.closest('.pagination-wrapper a');
                if (targetAnchor && !targetAnchor.classList.contains('disabled') && !targetAnchor.classList.contains('active')) {
                    e.preventDefault();
                    
                    // URL yang diambil dari href (masih http://)
                    const targetUrl = targetAnchor.getAttribute('href'); 
                    
                    // UBAH BAGIAN INI: Konversi URL agar mengikuti protocol HTTPS saat ini
                    const secureUrl = new URL(targetUrl);
                    secureUrl.protocol = window.location.protocol; 

                    window.history.pushState({}, '', secureUrl.href); // Gunakan secureUrl
                    fetchLiveData(secureUrl.href); // Gunakan secureUrl
                }
            });

            document.addEventListener('click', function(e) {
                const openBtn = e.target.closest('.open-popup');
                if (openBtn) {
                    currentCard = openBtn.closest('.riwayat-card');

                    popupTitle.innerHTML = `⚠️ ${openBtn.dataset.judul} ⚠️`;
                    popupDesc.innerText = openBtn.dataset.deskripsi;
                    popupPenjelasan.innerText = openBtn.dataset.penjelasan;
                    popupUser.innerText = openBtn.dataset.user;
                    popupDate.innerText = openBtn.dataset.date;
                    popupHoax.innerText = openBtn.dataset.hoax + '%';
                    popupBenar.innerText = openBtn.dataset.benar + '%';
                    const imageUrl = openBtn.dataset.gambar;
    
                        if (imageUrl) {
                            // Ada gambar -> Tampilkan gambar, sembunyikan teks
                            popupImage.src = imageUrl;
                            popupImage.style.display = 'block';
                            popupDesc.style.display = 'none';
                        } else {
                            // Nggak ada gambar -> Sembunyikan tag gambar, tampilkan teks
                            popupImage.src = '';
                            popupImage.style.display = 'none';
                            popupDesc.style.display = 'block';
                            popupDesc.innerText = openBtn.dataset.deskripsi;
                        }

                    if (currentCard.dataset.deleted === "true") {
                        popupDeleteStatus.style.display = 'block';
                        const deletedDate = new Date(currentCard.dataset.deletedDate);
                        popupDeleteStatus.textContent = `Dihapus • ${deletedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`;
                        deletePopup.innerHTML = '<i class="fa fa-undo"></i>';
                        permanentDeletePopup.style.display = 'block';
                    } else {
                        popupDeleteStatus.style.display = 'none';
                        deletePopup.innerHTML = '<i class="fa fa-trash"></i>';
                        permanentDeletePopup.style.display = 'none';
                    }
                    popupOverlay.classList.add('active');
                }
            });

            closePopup.addEventListener('click', () => popupOverlay.classList.remove('active'));
            popupOverlay.addEventListener('click', (e) => { if (e.target === popupOverlay) popupOverlay.classList.remove('active'); });

            // PROSES AJAX RESTORE
            const restoreOverlay = document.getElementById('restoreOverlay');
            const confirmRestore = document.getElementById('confirmRestore');
            const cancelRestore = document.getElementById('cancelRestore');

            // 1. Logika Trigger Restore (Ganti bagian fetch di dalam deletePopup)
            deletePopup.addEventListener('click', function() {
                if (!currentCard) return;

                if (currentCard.dataset.deleted === "true") {
                    // Tampilkan konfirmasi, jangan langsung fetch
                    restoreOverlay.classList.add('active'); 
                    return;
                }
                
                // Jika bukan deleted, lanjut ke logika hapus (soft-delete)
                permanentOverlay.classList.add('active');
            });

            // 2. Tutup modal restore
            cancelRestore.addEventListener('click', () => restoreOverlay.classList.remove('active'));

            // 3. Eksekusi Restore
            confirmRestore.addEventListener('click', function() {
                if (!currentCard) return;
                const requestId = currentCard.dataset.requestId;

                fetch(`/admin/history-management/restore/${requestId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        restoreOverlay.classList.remove('active');
                        popupOverlay.classList.remove('active');
                        triggerSettingAlert(data.message, 'success');
                        document.getElementById('loadingOverlay').style.display = 'block';
                        setTimeout(() => { location.reload(); }, 1200);
                    } else {
                        triggerSettingAlert('Gagal memulihkan data', 'error');
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    triggerSettingAlert('Gagal menghubungi server', 'error');
                });
            });

            cancelPermanentDelete.addEventListener('click', () => permanentOverlay.classList.remove('active'));
            permanentDeletePopup.addEventListener('click', () => permanentOverlay.classList.add('active'));

            // PROSES AJAX SOFT-DELETE & HARD-DELETE
            confirmPermanentDelete.addEventListener('click', function () {
                if (!currentCard) return;
                const requestId = currentCard.dataset.requestId;
                let url = `/admin/history-management/soft-delete/${requestId}`;
                let method = 'POST';

                if (currentCard.dataset.deleted === "true") {
                    url = `/admin/history-management/hard-delete/${requestId}`;
                    method = 'DELETE';
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        permanentOverlay.classList.remove('active');
                        popupOverlay.classList.remove('active');
                        // Panggil banner alert pengganti browser alert bawaan
                        triggerSettingAlert(data.message, 'success');
                        document.getElementById('loadingOverlay').style.display = 'block';
                        setTimeout(() => { location.reload(); }, 1200);
                    } else {
                        triggerSettingAlert('Terjadi kesalahan', 'error');
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    triggerSettingAlert('Gagal menghubungi server', 'error');
                });
            });
        });
    </script>
@endsection
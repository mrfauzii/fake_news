@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    {{-- CONTAINER UNTUK MENAMPILKAN ALERT FORMAT BANNER SETTING --}}
    <div id="alertContainer"></div>
        @if(session('success'))
            <div id="successAlert" style="background-color: #28a745; color: white; padding: 15px 25px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; min-width: 300px; font-weight: 500; transition: 0.5s; opacity: 1;">
                <i class="fa fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
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

            <div class="riwayat-card searchable-card {{ $hasImage ? 'image-card' : '' }}"
                data-title="{{ strtolower(data_get($item, 'judul', '')) }}" 
                data-id="{{ $loop->index }}"
                data-request-id="{{ data_get($item, 'request_id') }}" 
                data-deleted="{{ data_get($item, 'is_deleted') ? 'true' : 'false' }}"
                data-deleted-date="{{ data_get($item, 'deleted_at') }}">

                @if ($hasImage && is_string($imagePath))
                    <div class="card-image-wrapper" style="text-align: center; padding: 10px;">
                        <img src="{{ asset($imagePath) }}" class="card-img" style="max-height: 180px; object-fit: contain; width: 100%; border-radius: 4px;" onerror="this.parentNode.style.display='none'">
                    </div>
                @endif

                <div class="card-header">
                    <div class="warning-title" style="font-weight: bold; font-size: 14px; margin-bottom: 5px; color: #b8201d;">
                        @if (!$hasImage)
                            <i class="fa fa-exclamation-triangle warning-icon"></i>
                        @endif

                        {{ data_get($item, 'judul') }}

                        @if (!$hasImage)
                            <i class="fa fa-exclamation-triangle warning-icon"></i>
                        @endif
                    </div>

                    <p class="desc" style="color: #444; font-size: 13px; line-height: 1.4; margin-top: 8px;">
                        {{ $userText }}
                    </p>
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

            @for ($i = 1; $i <= $data->lastPage(); $i++)
                <a href="{{ $data->url($i) }}" class="page-number {{ $data->currentPage() == $i ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor

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
                const container = document.getElementById('alertContainer');

                container.innerHTML = `
                    <div id="successAlert" class="success-alert">
                        <i class="fa ${
                            type === 'success'
                                ? 'fa-circle-check'
                                : 'fa-circle-exclamation'
                        }"></i>
                        ${message}
                    </div>
                `;

                setTimeout(() => {
                    const alert = document.getElementById('successAlert');

                    if (alert) {
                        alert.style.transition = '0.5s';
                        alert.style.opacity = '0';

                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }
                }, 5000);
            }

            // Jalankan fungsi auto-hide untuk session flash biasa jika ada saat load
            const initialSuccessAlert = document.getElementById('successAlert');
            if (initialSuccessAlert) {
                setTimeout(() => {
                    initialSuccessAlert.style.transition = '0.5s';
                    initialSuccessAlert.style.opacity = '0';
                    setTimeout(() => { initialSuccessAlert.remove(); }, 500);
                }, 5000);
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
                    const targetUrl = targetAnchor.getAttribute('href');
                    window.history.pushState({}, '', targetUrl);
                    fetchLiveData(targetUrl);
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
            deletePopup.addEventListener('click', function() {
                if (!currentCard) return;
                const requestId = currentCard.dataset.requestId;

                if (currentCard.dataset.deleted === "true") {
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
                            popupOverlay.classList.remove('active');
                            // Panggil banner alert pengganti browser alert bawaan
                            triggerSettingAlert(data.message, 'success');
                            setTimeout(() => { location.reload(); }, 1200);
                        } else {
                            triggerSettingAlert('Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        triggerSettingAlert('Gagal menghubungi server', 'error');
                    });
                    return;
                }
                permanentOverlay.classList.add('active');
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
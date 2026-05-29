@extends('layouts.admin')

@section('title', 'Umpan Balik')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/umpanbalik-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
@endpush

@section('content')

{{-- 1. BAGIAN HEADER & FORM PENCARIAN --}}
<div class="page-header-top">
    <div class="page-title-box">
        <h1>Manajemen Umpan Balik</h1>
        <p>Pantau dan tanggapi masukan dari pengguna untuk meningkatkan akurasi sistem.</p>
    </div>

    <form action="{{ url('/admin/umpanbalik') }}" method="GET" class="search-wrapper">
        <input
            type="text"
            name="search"
            id="searchFeedback"
            class="search-input"
            placeholder="Search..."
            value="{{ request('search') }}"
        >
        <button type="submit" class="search-btn">
            <i class="fa fa-search"></i>
        </button>
    </form>
</div>

{{-- 2. DAFTAR KARTU UMPAN BALIK (TARGET AJAX) --}}
<div class="umpanbalik-list" id="feedbackList">

    @forelse($feedbacks as $item)
        <div class="umpanbalik-item new feedback-item">
            <div class="umpanbalik-left">
                <div>
                    <h4>{{ $item->username }}</h4>
                    <span>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('l, j F Y') }}</span>
                    <p>{{ $item->feedback }}</p>

                    <div class="umpanbalik-actions">
                        <button
                            class="btn-detail"
                            data-username="{{ $item->username }}"
                            data-date="{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('l, j F Y') }}"
                            data-feedback="{{ rawurlencode($item->feedback) }}"
                            data-link="{{ rawurlencode($item->link ?? '-') }}"
                            data-result="{{ ucfirst($item->final_label ?? '-') }}"
                        >
                            Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="padding:20px; text-align:center; color:#999;">
            Tidak ada data umpan balik yang ditemukan.
        </div>
    @endforelse

</div>

{{-- 3. KOMPONEN TOMBOL NAVIGASI HALAMAN (PAGINATION) --}}
<div class="pagination-wrapper">
    @if ($feedbacks->lastPage() > 1)

        {{-- Tombol Previous --}}
        @if($feedbacks->currentPage() > 1)
            <a href="{{ $feedbacks->previousPageUrl() }}">Previous</a>
        @else
            <a class="disabled">Previous</a>
        @endif

        {{-- Nomor Angka Halaman --}}
        @for ($i = 1; $i <= $feedbacks->lastPage(); $i++)
            <a href="{{ $feedbacks->url($i) }}"
               class="page-number {{ $feedbacks->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        {{-- Tombol Next --}}
        @if($feedbacks->hasMorePages())
            <a href="{{ $feedbacks->nextPageUrl() }}">Next</a>
        @else
            <a class="disabled">Next</a>
        @endif

    @endif
</div>

{{-- 4. MODAL POPUP DETAIL UMPAN BALIK --}}
<div id="feedbackPopup" class="popup-overlay" style="display:none;">
    <div class="popup-box">
        <button id="closePopup" class="popup-close">✕</button>

        <div class="popup-header">
            <h2>Detail Umpan Balik</h2>
            <p>Informasi lengkap masukan pengguna</p>
        </div>

        <div class="popup-info">
            <div class="info-card user-card">
                <div class="info-title">Nama Pengguna</div>
                <div class="info-value" id="popupUser"></div>

                <div class="info-title" style="margin-top:12px">Tanggal</div>
                <div class="info-value" id="popupDate"></div>
            </div>

            <div class="info-card">

                <div class="info-title">
                    Berita yang dicari
                </div>

                <div
                class="info-value"
                id="popupinput">
                </div>

                <div class="info-title">
                    Hasil Deteksi
                </div>

                <div class="info-value"
                id="popupResult">
                </div>

            </div>
        </div>

        <h3>Isi Feedback</h3>
        <div class="feedback-box" id="popupFeedback"></div>
    </div>
</div>

{{-- 5. LOGIKA JAVASCRIPT (AJAX LIVE SEARCH, PAGINATION & POPUP) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('feedbackPopup');
    const closeBtn = document.getElementById('closePopup');
    const searchInput = document.getElementById('searchFeedback');
    const searchForm = document.querySelector('.search-wrapper');
    
    let debounceTimeout;

    // A. Fungsi Ambil Data Menggunakan Fetch API (AJAX)
    function fetchLiveData(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Timpa list lama dengan list umpan balik yang baru
            const newFeedbackList = doc.getElementById('feedbackList');
            const currentFeedbackList = document.getElementById('feedbackList');
            if (newFeedbackList && currentFeedbackList) {
                currentFeedbackList.innerHTML = newFeedbackList.innerHTML;
            }
document.addEventListener('DOMContentLoaded',function(){

    const container=
    document.getElementById('feedbackList');

    const popup=
    document.getElementById('feedbackPopup');

    const closeBtn=
    document.getElementById('closePopup');

    fetch('/admin/umpanbalik-data')

    .then(response=>response.json())

    .then(result=>{

        container.innerHTML='';
        console.log(result);

        result.data.forEach(item=>{

            container.innerHTML += `
            <div class="umpanbalik-item new feedback-item"
                data-user="${item.username.toLowerCase()}"
                data-feedback="${item.feedback.toLowerCase()}"
                >

                <div class="umpanbalik-left">


                    <div>

                        <h4>${item.username}</h4>

                        <span>${item.date}</span>

                        <p>${item.feedback}</p>

                        <div class="umpanbalik-actions">

                            <button
                            class="btn-outline btn-detail"
                            data-username="${item.username}"
                            data-date="${item.date}"
                            data-feedback="${encodeURIComponent(item.feedback)}"
                            data-input_text="${encodeURIComponent(item.input_text)}"
                            data-images="${item.images}"
                            data-result="${item.result}"
                            >
                            Detail
                            </button>

                        </div>

                    </div>

                </div>

            // Timpa pagination lama dengan pagination yang baru
            const newPagination = doc.querySelector('.pagination-wrapper');
            const currentPagination = document.querySelector('.pagination-wrapper');
            if (currentPagination) {
                currentPagination.innerHTML = newPagination ? newPagination.innerHTML : '';
            }
        })
        .catch(error => console.error('Terjadi kesalahan saat memuat data:', error));
    }

    // B. Fitur Live Search Otomatis (Debounce 300ms)
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            const keyword = this.value;

            debounceTimeout = setTimeout(() => {
                const url = new URL(window.location.origin + window.location.pathname);
                
                if (keyword) {
                    url.searchParams.set('search', keyword);
                }
                url.searchParams.delete('page'); // Kembali ke halaman 1 saat mengetik kata kunci baru

                window.history.pushState({}, '', url); // Ubah URL browser tanpa reload
                fetchLiveData(url);
            }, 300);
        });
    }

    // Mencegah submit form konvensional agar tidak memicu reload halaman
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
        });

    });

    document.addEventListener('click',function(e){

    if(e.target.classList.contains('btn-detail')){
        document.getElementById(
            'popupUser'
        ).innerText =
        e.target.dataset.username;

        document.getElementById(
            'popupDate'
        ).innerText =
        e.target.dataset.date;

        document.getElementById(
            'popupFeedback'
        ).innerText =
        decodeURIComponent(
            e.target.dataset.feedback
        );
        //img or text
        const el = document.getElementById('popupinput');
        const data = e.target.dataset;

        const inputText =
            data.input_text && data.input_text !== "null"
                ? decodeURIComponent(data.input_text)
                : null;

        const image =
            data.images && data.images !== "null"
                ? data.images
                : null;

        // RESET mode
        el.classList.remove('image-mode');
        el.innerHTML = "";

        // TEXT MODE
        if (inputText) {
            el.innerText = inputText;

        // IMAGE MODE
        } else if (image) {
            el.classList.add('image-mode');
            el.innerHTML = `<img src="${image}">`;

        // EMPTY
        } else {
            el.innerText = "-";
        }


        document.getElementById(
        'popupResult'
        ).innerText =
        e.target.dataset.result;

        popup.style.display='flex';
    }

    // C. Interseptor Tombol Navigasi Angka Halaman (Pagination AJAX)
    document.addEventListener('click', function (e) {
        const targetAnchor = e.target.closest('.pagination-wrapper a');
        
        if (targetAnchor && !targetAnchor.classList.contains('disabled') && !targetAnchor.classList.contains('active')) {
            e.preventDefault();
            
            const targetUrl = targetAnchor.getAttribute('href');
            window.history.pushState({}, '', targetUrl); // Update URL browser
            fetchLiveData(targetUrl); // Muat data halaman baru
        }
    });

    // D. Handler Klik Tombol Detail (Event Delegation untuk Element Dinamis)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-detail')) {
            document.getElementById('popupUser').innerText = e.target.dataset.username;
            document.getElementById('popupDate').innerText = e.target.dataset.date;
            document.getElementById('popupFeedback').innerText = decodeURIComponent(e.target.dataset.feedback);
            document.getElementById('popupLink').innerText = decodeURIComponent(e.target.dataset.link);
            document.getElementById('popupResult').innerText = e.target.dataset.result;

            popup.style.display = 'flex';
        }
    });

    // E. Menutup Popup Modal
    closeBtn.addEventListener('click', () => popup.style.display = 'none');
    popup.addEventListener('click', function(e) {
        if (e.target === popup) { popup.style.display = 'none'; }
    });
});
</script>
<style>
#popupinput {
    max-height: 150px;
    overflow-y: auto;
    word-wrap: break-word;
    white-space: pre-wrap;
}

/* mode gambar */
#popupinput.image-mode {
    max-height: none;
    overflow: visible;
    display: flex;
    justify-content: center;
    align-items: center;
}

#popupinput.image-mode img {
    max-width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 10px;
}
</style>
@endsection
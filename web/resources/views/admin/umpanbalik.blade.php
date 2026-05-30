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
    data-input_text="{{ rawurlencode($item->input_text ?? '') }}"
    data-images="{{ $item->images ?? '' }}"
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
    let debounceTimeout;

    // --- FUNGSI UTAMA FETCH ---
    function fetchLiveData(url) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // 1. Update List Data
        const newList = doc.getElementById('feedbackList');
        const currentList = document.getElementById('feedbackList');
        if (newList && currentList) currentList.innerHTML = newList.innerHTML;

        // 2. Update Pagination (PENTING AGAR TOMBOL NEXT/PREV TERBARU TERPASANG)
        const newPagination = doc.querySelector('.pagination-wrapper');
        const currentPagination = document.querySelector('.pagination-wrapper');
        if (currentPagination && newPagination) {
            currentPagination.innerHTML = newPagination.innerHTML;
        }
    });
}

    // --- EVENT DELEGATION (KUNCI AGAR TOMBOL AJAX BISA DIKLIK) ---
    document.addEventListener('click', function(e) {
        // 1. Logika Klik Detail
        // Di dalam event listener 'click' pada bagian 'btn-detail'
if (e.target.classList.contains('btn-detail')) {
    const data = e.target.dataset;
    const popupInput = document.getElementById('popupinput');

    // Isi teks biasa
    document.getElementById('popupUser').innerText = data.username;
    document.getElementById('popupDate').innerText = data.date;
    document.getElementById('popupFeedback').innerText = decodeURIComponent(data.feedback);
    document.getElementById('popupResult').innerText = data.result;

    // Logika Khusus untuk Input Text atau Gambar
    const rawInput = decodeURIComponent(data.input_text || '');
    const imgUrl = data.images;

    popupInput.classList.remove('image-mode');
    popupInput.innerHTML = ""; // Bersihkan konten lama

    if (imgUrl && imgUrl !== "") {
        // Jika ada gambar
        popupInput.classList.add('image-mode');
        popupInput.innerHTML = `<img src="${imgUrl}" alt="Feedback Image">`;
    } else if (rawInput !== "") {
        // Jika ada teks
        popupInput.innerText = rawInput;
    } else {
        // Jika kosong
        popupInput.innerText = "-";
    }

    document.getElementById('feedbackPopup').style.display = 'flex';
}

        // 2. Logika Pagination
        if (e.target.closest('.pagination-wrapper a')) {
            e.preventDefault();
            const url = e.target.closest('a').getAttribute('href');
            fetchLiveData(url);
        }
    });

    // --- CLOSE POPUP ---
    closeBtn.addEventListener('click', () => popup.style.display = 'none');
    window.onclick = (e) => { if (e.target === popup) popup.style.display = 'none'; };

    // --- LIVE SEARCH ---
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.value);
                fetchLiveData(url);
            }, 500);
        });
    }
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
/* 1. Kontainer Utama: Berikan kemampuan scroll jika konten meluap */
#popupinput {
    text-align: left;      /* KUNCI: Paksa teks untuk selalu rata kiri */
    word-wrap: break-word; /* Mencegah teks panjang menembus kotak */
    white-space: pre-wrap; /* Mempertahankan spasi dan enter (baris baru) dari teks asli */
}
#popupinput img {
    max-width: 100%;
    max-height: 200px !important; /* PAKSA gambar agar tingginya maksimal 200px */
    object-fit: contain;          /* Gambar menyesuaikan rasio asli, tidak akan gepeng */
    border-radius: 8px;
    display: block;
    margin: 0 auto;
}
/* 2. Mode Gambar: Hapus batasan agar gambar bisa ditampilkan dalam ukuran aslinya */
#popupinput.image-mode {
    max-height: 400px; /* Batasi tinggi kontainer modal agar tidak terlalu panjang */
    display: block;    /* Ubah dari flex ke block agar scroll berfungsi normal */
    text-align: center;
}

/* 3. Gambar: Biarkan gambar mengikuti ukuran aslinya atau lebar kontainer */
#popupinput.image-mode img {
    max-width: 100%;   /* Gambar tidak akan lebih lebar dari modal */
    height: auto;      /* Tinggi proporsional */
    display: block;
    margin: 0 auto;
    border-radius: 10px;
}

</style>
@endsection
@extends('layouts.admin')

@section('title', 'Umpan Balik')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/umpanbalik-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
@endpush

@section('content')

<div class="feedback-header-top">
    <div class="feedback-title">
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
                            class=" btn-detail"
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
                <div class="info-title">Berita yang dicari</div>
                <div class="info-value" id="popupLink"></div>

                <div class="info-title">Hasil Deteksi</div>
                <div class="info-value" id="popupResult"></div>
            </div>
        </div>

        <h3>Isi Feedback</h3>
        <div class="feedback-box" id="popupFeedback"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('feedbackPopup');
    const closeBtn = document.getElementById('closePopup');

    /* Handler klik tombol Detail menggunakan Event Delegation */
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

    /* Handler Menutup Popup */
    closeBtn.addEventListener('click', () => popup.style.display = 'none');
    popup.addEventListener('click', function(e) {
        if (e.target === popup) { popup.style.display = 'none'; }
    });
});
</script>

@endsection
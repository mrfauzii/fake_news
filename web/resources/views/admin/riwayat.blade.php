@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
@endpush

@section('content')

<!-- HEADER -->
<div class="page-header">

    <h1>Riwayat Global</h1>

    <div class="page-header-right">

        <div class="search-wrapper">

            <input 
                type="text"
                placeholder="Search..."
                class="search-input"
                id="searchInput"
            >

            <button class="search-btn">
                <i class="fa fa-search"></i>
            </button>

        </div>

        <button class="btn-export">
            <i class="fa fa-download"></i>
            Export CSV
        </button>

    </div>

</div>

<p class="page-subtitle">
    Daftar lengkap seluruh verifikasi berita yang telah dilakukan oleh sistem dan moderator.
</p>

<!-- GRID -->
<div class="riwayat-grid">

    @foreach($data as $item)

    <div 
    class="riwayat-card searchable-card {{ $item['gambar'] ? 'image-card' : '' }}"
    data-title="{{ strtolower($item['judul']) }}"
    data-id="{{ $loop->index }}"
    >

        {{-- JIKA ADA GAMBAR --}}
        @if($item['gambar'])

            <img src="{{ asset($item['gambar']) }}" class="card-img">

        {{-- JIKA TIDAK ADA GAMBAR --}}
        @else

            <div class="card-header">

                <div class="warning-title">

                    <i class="fa fa-exclamation-triangle warning-icon"></i>

                    {{ $item['judul'] }}

                    <i class="fa fa-exclamation-triangle warning-icon"></i>

                </div>

                <p class="desc">
                    {{ $item['deskripsi'] }}
                </p>

            </div>

        @endif

        <!-- GARIS -->
        <div class="divider"></div>

        <!-- BOTTOM -->
        <div class="card-bottom">

            <!-- PROGRESS -->
            <div class="progress-circle">

                <svg viewBox="0 0 120 60">

                    <!-- background -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="bg"/>

                    <!-- merah -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="progress-red"/>

                    <!-- hijau -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="progress-green"/>

                </svg>

                <span>{{ $item['hoax'] }}%</span>

            </div>

            <!-- LEGEND -->
            <div class="legend">

                <p>
                    <span class="dot red"></span>
                    Data terdeteksi hoax sebesar {{ $item['hoax'] }}%
                </p>

                <p>
                    <span class="dot green"></span>
                    Data terdeteksi benar sebesar {{ $item['benar'] }}%
                </p>

            </div>

            <!-- BUTTON -->
            <button 
                class="btn-detail open-popup"

                data-judul="{{ $item['judul'] }}"
                data-deskripsi="{{ $item['deskripsi'] }}"
                data-hoax="{{ $item['hoax'] }}"
                data-benar="{{ $item['benar'] }}"
            >
                Selengkapnya
            </button>

        </div>

    </div>

    @endforeach

</div>

<!-- POPUP -->
<div class="popup-overlay" id="popupOverlay">

    <div class="popup-box">

        <!-- ACTION BUTTON -->
        <div class="popup-actions">

            <!-- DELETE -->
            <button class="popup-delete" id="deletePopup">
                <i class="fa fa-trash"></i>
            </button>

            <!-- CLOSE -->
            <button class="popup-close" id="closePopup">
                <i class="fa fa-times"></i>
            </button>

        </div>

        <!-- HEADER -->
        <div class="popup-top">

            <div class="popup-user">
                @Rini Wulandari
            </div>

            <div class="popup-date">
                Minggu, 7 Juni 2025
            </div>

        </div>

        <!-- CONTENT -->
        <div class="popup-content">

            <div class="popup-title" id="popupTitle">
                Judul
            </div>

            <p class="popup-desc" id="popupDesc">
                Isi berita
            </p>

        </div>

        <!-- LINE -->
        <div class="popup-divider"></div>

        <!-- BOTTOM -->
        <div class="popup-bottom">

            <!-- LEFT -->
            <div class="popup-legend">

                <p>
                    <span class="dot red"></span>
                    Data terdeteksi hoax sebesar
                    <strong id="popupHoax">70%</strong>
                </p>

                <p>
                    <span class="dot green"></span>
                    Data terdeteksi benar sebesar
                    <strong id="popupBenar">30%</strong>
                </p>

            </div>

            <!-- RIGHT -->
            <div class="popup-result">

                Hasil verifikasi menunjukkan bahwa sebagian besar informasi ini, yakni sekitar 70%, mengandung unsur hoaks atau ketidaksesuaian fakta. Mohon untuk memvalidasi kembali sumber informasi sebelum menyebarkannya.

            </div>

        </div>

    </div>

</div>

<!-- DELETE POPUP -->
<div class="delete-overlay" id="deleteOverlay">

    <div class="delete-popup">

        <h3>Hapus Riwayat</h3>

        <p>
            Apakah anda yakin ingin menghapus riwayat ini?
        </p>

        <div class="delete-actions">

            <button
                class="btn-delete-confirm"
                onclick="confirmDelete()"
            >
                Hapus
            </button>

            <button
                class="btn-cancel"
                onclick="closeDeletePopup()"
            >
                Batal
            </button>

        </div>

    </div>

</div>

<!-- SEARCH JS -->
<script>

document.getElementById('searchInput').addEventListener('keyup', function () {

    let keyword = this.value.toLowerCase();

    let cards = document.querySelectorAll('.searchable-card');

    cards.forEach(card => {

        let title = card.dataset.title;

        if (title.includes(keyword)) {

            card.style.display = 'block';

        } else {

            card.style.display = 'none';

        }

    });

});

</script>

<script>

const popupOverlay = document.getElementById('popupOverlay');
const closePopup = document.getElementById('closePopup');
const deletePopup = document.getElementById('deletePopup');

let currentCard = null;

const popupTitle = document.getElementById('popupTitle');
const popupDesc = document.getElementById('popupDesc');
const popupHoax = document.getElementById('popupHoax');
const popupBenar = document.getElementById('popupBenar');

document.querySelectorAll('.open-popup').forEach(button => {

    button.addEventListener('click', function () {
        currentCard = this.closest('.riwayat-card');

        popupTitle.innerHTML =
            `⚠️ ${this.dataset.judul} ⚠️`;

        popupDesc.innerText =
            this.dataset.deskripsi;

        popupHoax.innerText =
            this.dataset.hoax + '%';

        popupBenar.innerText =
            this.dataset.benar + '%';

        popupOverlay.classList.add('active');

    });

});

closePopup.addEventListener('click', function () {

    popupOverlay.classList.remove('active');

});

popupOverlay.addEventListener('click', function (e) {

    if (e.target === popupOverlay) {

        popupOverlay.classList.remove('active');

    }

});

deletePopup.addEventListener('click', function () {

    if (!currentCard) return;

    document
        .getElementById('deleteOverlay')
        .classList.add('active');

});

deletePopup.addEventListener('click', function () {

    if (!currentCard) return;

    document
        .getElementById('deleteOverlay')
        .classList.add('active');

});

function closeDeletePopup() {

    document
        .getElementById('deleteOverlay')
        .classList.remove('active');

}

function confirmDelete() {

    if (!currentCard) return;

    currentCard.remove();

    popupOverlay.classList.remove('active');

    closeDeletePopup();

}

</script>

@endsection
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

    <div class="riwayat-card searchable-card 
        {{ $item['gambar'] ? 'image-card' : '' }}"
        data-title="{{ strtolower($item['judul']) }}">

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
            <button class="btn-detail">
                Selengkapnya
            </button>

        </div>

    </div>

    @endforeach

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

@endsection
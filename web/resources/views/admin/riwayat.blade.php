@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
@endpush

@section('content')

<!-- HEADER -->
<div class="page-header">
    <h1>Riwayat Global</h1>

    <div class="search-wrapper">
        <input type="text" placeholder="Search..." class="search-input">
        <button class="search-btn">
            <i class="fa fa-search"></i>
        </button>
    </div>
</div>

<p class="page-subtitle">
    Daftar lengkap seluruh verifikasi berita yang telah dilakukan oleh sistem dan moderator.
</p>

<!-- GRID -->
<div class="riwayat-grid">

    <!-- CARD -->
    <div class="riwayat-card">

        <!-- HEADER -->
        <div class="card-header">
            <i class="fa fa-exclamation-triangle warning-icon"></i>
                [KABAR PENTING]
            <i class="fa fa-exclamation-triangle warning-icon"></i>
            <p class="desc">
                Pemerintah membagikan Bantuan Sosial Ramadan sebesar Rp1,5 juta bagi warga yang memiliki BPJS Kesehatan.
                Daftar sekarang melalui link Telegram ini: bit.ly/bansos-ramadhan2026 agar dana segera cair.
            </p>
        </div>

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

                    <!-- hijau (ujung kecil) -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="progress-green"/>
                </svg>

                <span>70%</span>
            </div>

            <!-- LEGEND -->
            <div class="legend">
                <p><span class="dot red"></span> Data terdeteksi hoax sebesar</p>
                <p><span class="dot green"></span> Data terdeteksi benar sebesar</p>
            </div>

            <!-- BUTTON -->
            <button class="btn-detail">Selengkapnya</button>

        </div>

    </div>

    <!-- CARD DENGAN GAMBAR -->
    <div class="riwayat-card">

        <!-- IMAGE -->
        <img src="{{ asset('img/contoh-berita.png') }}" class="card-img">

        <!-- GARIS -->
        <div class="divider"></div>

        <!-- BOTTOM -->
        <div class="card-bottom">

            <div class="progress-circle">
                <svg viewBox="0 0 120 60">
                    <!-- background -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="bg"/>

                    <!-- merah -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="progress-red"/>

                    <!-- hijau (ujung kecil) -->
                    <path d="M10 60 A50 50 0 0 1 110 60"
                        class="progress-green"/>
                </svg>

                <span>70%</span>
            </div>

            <div class="legend">
                <p><span class="dot red"></span> Data terdeteksi hoax sebesar</p>
                <p><span class="dot green"></span> Data terdeteksi benar sebesar</p>
            </div>

            <button class="btn-detail">Selengkapnya</button>

        </div>

    </div>

</div>

@endsection
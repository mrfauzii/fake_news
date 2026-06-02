@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/footer/footer.css') }}">
@endpush

@section('content')
@include('user.partials.navbar')

@include('user.partials.hero-bg')
<main class="footer-page">
    <section class="footer-page__hero">
        <div class="footer-page__container">
            {{-- <a href="javascript:history.back()" class="footer-page__back-button footer-page__back-button--accent" aria-label="Kembali ke halaman sebelumnya">
                <span class="footer-page__back-button-symbol" aria-hidden="true">&lt;</span>
                Kembali
            </a> --}}
            <p class="footer-page__eyebrow">Informasi</p>
            <h1>Kebijakan Privasi</h1>
            <p class="footer-page__lead">
                Halaman ini menjelaskan bagaimana data pengguna diproses saat menggunakan fitur
                Lensa Hoax secara umum.
            </p>
        </div>
    </section>

    <section class="footer-page__content">
        <div class="footer-page__container footer-page__stack">
            <article class="footer-card footer-card--accent">
                <h2>Data yang Dapat Kami Gunakan</h2>
                <p>
                    Kami dapat memproses data yang diperlukan untuk menjalankan fitur, seperti
                    input pencarian, riwayat penggunaan, dan data teknis yang membantu layanan berjalan.
                </p>
            </article>

            <div class="footer-page__grid footer-page__grid--two">
                <article class="footer-card">
                    <h2>Tujuan Penggunaan Data</h2>
                    <ul class="footer-list">
                        <li>Menjalankan fitur deteksi dan penyimpanan riwayat.</li>
                        <li>Meningkatkan kualitas hasil dan pengalaman penggunaan.</li>
                        <li>Menjaga stabilitas layanan dan kebutuhan operasional.</li>
                    </ul>
                </article>

                <article class="footer-card">
                    <h2>Perlindungan Data</h2>
                    <ul class="footer-list">
                        <li>Data hanya digunakan untuk kebutuhan layanan yang relevan.</li>
                        <li>Akses internal dibatasi sesuai kebutuhan operasional.</li>
                        <li>Kami menghindari pengumpulan data yang tidak diperlukan.</li>
                    </ul>
                </article>
            </div>

            <article class="footer-card">
                <h2>Hak Pengguna</h2>
                <p>
                    Pengguna dapat meminta penjelasan, pembaruan, atau penghapusan data tertentu
                    sesuai kebijakan dan fitur yang tersedia pada platform.
                </p>
            </article>
        </div>
    </section>
</main>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush
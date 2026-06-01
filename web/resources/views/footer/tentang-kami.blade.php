@extends('layouts.app')

@section('title', 'Tentang Kami')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/footer/footer.css') }}">
@endpush

@section('content')
@include('user.partials.navbar')
@include('user.partials.hero-bg')
<main class="footer-page">
    <section class="footer-page__hero">
        <div class="footer-page__container">
            <p class="footer-page__eyebrow">Informasi</p>
            <h1>Tentang Kami</h1>
            <p class="footer-page__lead">
                Lensa Hoax membantu pengguna memverifikasi informasi dengan cara yang cepat,
                jelas, dan mudah dipahami, tanpa membuat proses cek fakta terasa rumit.
            </p>
        </div>
    </section>

    <section class="footer-page__content">
        <div class="footer-page__container footer-page__grid">
            <article class="footer-card footer-card--accent">
                <h2>Misi Kami</h2>
                <p>
                    Kami berfokus pada penyediaan alat deteksi hoaks yang praktis untuk membantu
                    pengguna mengambil keputusan berbasis informasi yang lebih terpercaya.
                </p>
            </article>

            <article class="footer-card">
                <h2>Yang Kami Kerjakan</h2>
                <ul class="footer-list">
                    <li>Menganalisis teks dan gambar untuk membantu proses verifikasi.</li>
                    <li>Menyajikan hasil dengan bahasa yang ringkas dan mudah dibaca.</li>
                    <li>Mendukung alur cek informasi yang cepat untuk penggunaan harian.</li>
                </ul>
            </article>

            <article class="footer-card">
                <h2>Prinsip Kami</h2>
                <ul class="footer-list">
                    <li>Transparan dalam penyajian hasil analisis.</li>
                    <li>Sederhana dalam tampilan, tanpa elemen yang berlebihan.</li>
                    <li>Berorientasi pada pengalaman pengguna yang nyaman.</li>
                </ul>
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
@extends('layouts.app')

@section('title', 'FAQ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/footer/footer.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
@endpush

@section('content')
 @include('user.partials.navbar')
    @include('user.partials.hero-bg')
<main class="footer-page">
    <section class="footer-page__hero">
        <div class="footer-page__container">
            <p class="footer-page__eyebrow">Informasi</p>
            <h1>FAQ</h1>
            <p class="footer-page__lead">
                Temukan jawaban atas pertanyaan umum mengenai verifikasi informasi
                dan teknologi AI Lensa Hoax.
            </p>
        </div>
    </section>

    @php
        $faqs = [
            [
                'icon' => 'auto_awesome',
                'question' => 'Apa itu Lensa Hoax?',
                'answer' => 'Lensa Hoax adalah platform verifikasi disinformasi cerdas yang memadukan basis pengetahuan (knowledge base) dan machine learning. Sistem ini dirancang untuk menganalisis kebenaran informasi berbasis teks dan kemiripan konten visual secara real-time.',
                'whatsapp' => false
            ],
            [
                'icon' => 'image_search',
                'question' => 'Bagaimana cara kerja deteksi AI?',
                'answer' => 'Untuk teks, AI kami membandingkan makna semantik kalimat dengan basis pengetahuan resmi (seperti Komdigi) dan mencari referensi terpercaya di internet. Untuk gambar, sistem mencari tingkat kemiripan visual (visual similarity) dan relevansi waktu publikasi, bukan melalui analisis forensik piksel.',
                'whatsapp' => false
            ],
            [
                'icon' => 'account_circle',
                'question' => 'Apakah saya harus mendaftar?',
                'answer' => 'Tidak wajib. Anda dapat melakukan pengecekan teks dan gambar di Website sebagai tamu (Guest). Namun, jika Anda login, Anda dapat menyimpan riwayat pencarian, menghapus riwayat pribadi, dan menggabungkan akun (merge account) Website dengan riwayat pencarian WhatsApp Anda.',
                'whatsapp' => false
            ],
            [
                'icon' => 'verified',
                'question' => 'Seberapa akurat hasilnya?',
                'answer' => 'Berdasarkan pengujian performa, model AI kami mencapai akurasi 90% untuk klasifikasi teks dan 81% untuk gambar. Hasil verifikasi akan menampilkan label (Hoaks/Fakta), tingkat keyakinan (Confidence Score), serta penjelasan logis yang dihasilkan oleh sistem (Explainable AI).',
                'whatsapp' => false
            ],
            [
                'icon' => 'chat',
                'question' => 'Cara kerja WhatsApp Bot?',
                'answer' => 'Sangat praktis. Cukup teruskan (forward) klaim teks berita yang mencurigakan ke nomor WhatsApp Bot kami. Sistem akan memproses teks tersebut dan memberikan balasan hasil verifikasi secara instan. Harap dicatat, layanan WhatsApp Bot saat ini difokuskan khusus untuk pengecekan berbasis teks.',
                'whatsapp' => true
            ],
            [
                'icon' => 'shield_lock',
                'question' => 'Apakah data pencarian saya aman?',
                'answer' => 'Seluruh riwayat permintaan deteksi dan gambar yang diunggah disimpan di database kami secara terstruktur untuk keperluan riwayat akun Anda dan evaluasi sistem. Anda memiliki kontrol penuh untuk melihat dan menghapus riwayat pencarian pribadi Anda kapan saja melalui dashboard.',
                'whatsapp' => false
            ]
        ];
    @endphp

    <section class="footer-page__content">
        <div class="footer-page__container">
            <div class="footer-page__grid footer-page__grid--two">
                @foreach($faqs as $faq)
                    <div class="footer-card faq-card-interactive">
                        <div class="faq-icon-box">
                            <span class="material-symbols-outlined">{{ $faq['icon'] }}</span>
                        </div>
                        
                        <h2>{{ $faq['question'] }}</h2>
                        <p style="color: var(--footer-page-muted);">{{ $faq['answer'] }}</p>

                        @if($faq['whatsapp'])
                            <div class="faq-card__action">
                                <a href="https://wa.me/6289508135121" target="_blank" class="faq-whatsapp-btn">
                                    <span class="material-symbols-outlined">rocket_launch</span>
                                    Hubungi Bot Whatsapp
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush
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
                'answer' => 'Lensa Hoax adalah platform berbasis Artificial Intelligence (AI) yang dirancang khusus untuk memverifikasi keaslian informasi digital. Kami mendeteksi manipulasi teks, disinformasi, hingga rekayasa foto digital secara real-time.',
                'whatsapp' => false
            ],
            [
                'icon' => 'image_search',
                'question' => 'Bagaimana cara kerja deteksi AI?',
                'answer' => 'AI kami menggunakan metode digital forensics yang mendalam, meliputi analisis metadata file, pengecekan konsistensi pixel (Error Level Analysis), dan pencarian silang database visual untuk mengidentifikasi area yang telah dimanipulasi.',
                'whatsapp' => false
            ],
            [
                'icon' => 'account_circle',
                'question' => 'Apakah saya harus mendaftar?',
                'answer' => 'Tidak harus. Anda bisa menggunakan fitur \'Cek Tanpa Login\' untuk verifikasi cepat. Namun, dengan login menggunakan Google, Anda dapat menyimpan riwayat verifikasi dan mendapatkan laporan analisis yang lebih mendalam secara gratis.',
                'whatsapp' => false
            ],
            [
                'icon' => 'verified',
                'question' => 'Seberapa akurat hasilnya?',
                'answer' => 'Sistem kami memiliki tingkat akurasi tinggi yang terus diperbarui melalui machine learning. Setiap hasil verifikasi dilengkapi dengan \'Confidence Score\' dan penjelasan teknis mengapa sebuah konten dikategorikan sebagai hoax atau asli.',
                'whatsapp' => false
            ],
            [
                'icon' => 'chat',
                'question' => 'Cara kerja WhatsApp Bot?',
                'answer' => 'Sangat mudah. Cukup klik tombol \'Hubungi Bot Whatsapp\', simpan nomornya, dan teruskan (forward) pesan teks atau gambar yang mencurigakan. Bot kami akan memprosesnya dalam hitungan detik dan mengirimkan ringkasan hasilnya.',
                'whatsapp' => true
            ],
            [
                'icon' => 'shield_lock',
                'question' => 'Apakah data saya aman?',
                'answer' => 'Keamanan privasi Anda adalah prioritas kami. Semua data yang diunggah dienkripsi secara end-to-end dan hanya digunakan untuk keperluan analisis verifikasi. Kami tidak pernah membagikan data pribadi Anda kepada pihak ketiga.',
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
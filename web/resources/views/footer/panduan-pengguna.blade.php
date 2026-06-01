@extends('layouts.app')

@section('title', 'Panduan Pengguna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/footer/footer.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
@endpush

@section('content')
<main class="footer-page">
    <section class="footer-page__hero">
        <div class="footer-page__container">
            <p class="footer-page__eyebrow">Informasi</p>
            <h1>Panduan Pengguna</h1>
            <p class="footer-page__lead">
                Pelajari cara menggunakan Lensa Hoax untuk memverifikasi informasi digital dengan mudah dan efektif. 
                Ikuti langkah-langkah sederhana ini untuk memastikan keaslian konten yang Anda temui.
            </p>
        </div>
    </section>

    <section class="footer-page__content section-spacing">
        <div class="footer-page__container">
            <div class="footer-page__grid--two align-center content-gap-large">
                <div class="footer-page__stack">
                    <div>
                        <div class="footer-icon-badge">
                            <span class="material-symbols-outlined icon-size-sm">verified_user</span>
                            <span>Visi Kami</span>
                        </div>
                        <h2 class="section-main-title">Melawan Misinformasi dengan Kecerdasan Buatan</h2>
                        <p class="section-description">
                            Lensa Hoax hadir sebagai benteng pertahanan digital Anda. Kami mengintegrasikan teknologi AI tercanggih untuk menganalisis data secara objektif, memberikan transparansi di tengah arus informasi yang cepat.
                        </p>
                    </div>

                    <div class="footer-page__grid--two content-gap-small">
                        <div class="footer-card card-padding-sm radius-sm">
                            <span class="material-symbols-outlined icon-color-accent">article</span>
                            <h4 class="mini-card-title">Deteksi Narasi</h4>
                            <p class="mini-card-desc">Analisis pola teks hoaks.</p>
                        </div>
                        <div class="footer-card card-padding-sm radius-sm">
                            <span class="material-symbols-outlined icon-color-accent">image_search</span>
                            <h4 class="mini-card-title">Keaslian Gambar</h4>
                            <p class="mini-card-desc">Verifikasi manipulasi foto.</p>
                        </div>
                        <div class="footer-card card-padding-sm radius-sm">
                            <span class="material-symbols-outlined icon-color-accent">chat_bubble</span>
                            <h4 class="mini-card-title">WhatsApp Bot</h4>
                            <p class="mini-card-desc">Cek instan via pesan.</p>
                        </div>
                        <div class="footer-card card-padding-sm radius-sm">
                            <span class="material-symbols-outlined icon-color-accent">psychology</span>
                            <h4 class="mini-card-title">Penjelasan AI</h4>
                            <p class="mini-card-desc">Analisis sumber original.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="footer-page__content section-spacing">
        <div class="footer-page__container">
            <div class="footer-card container-card-soft">
                <h2 class="center-title-lg">Panduan Memulai</h2>
                <p class="center-desc-md">Hanya butuh beberapa detik untuk melindungi diri Anda dari hoaks.</p>
                
                <div class="footer-page__grid--two content-gap-md text-left">
                    <div class="footer-card radius-md">
                        <div class="footer-icon-box-large">
                            <span class="material-symbols-outlined">public</span>
                        </div>
                        <h3 class="guide-card-title">Akses Platform</h3>
                        <p class="guide-card-desc">Kunjungi situs Lensa Hoax melalui peramban Anda. Anda bisa langsung menggunakan fitur <strong>'Cek Tanpa Login'</strong> untuk verifikasi cepat tanpa hambatan pendaftaran.</p>
                    </div>

                    <div class="footer-card radius-md">
                        <div class="footer-icon-box-large">
                            <span class="material-symbols-outlined">account_circle</span>
                        </div>
                        <h3 class="guide-card-title">Integrasi Google</h3>
                        <p class="guide-card-desc">Daftar menggunakan Google Integration hanya dengan satu klik. Dengan masuk ke akun, seluruh riwayat verifikasi Anda akan tersimpan aman untuk referensi di masa mendatang.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="footer-page__content section-spacing">
        <div class="footer-page__container">
            <div class="center-header-wrapper">
                <h2 class="center-title-lg">Cara Melakukan Verifikasi</h2>
                <div class="accent-line-center"></div>
            </div>

            <div class="footer-page__grid">
                <div class="footer-card step-verification-card">
                    <div class="footer-step-badge">1</div>
                    <span class="material-symbols-outlined step-card-icon">content_paste</span>
                    <h3 class="step-card-title">Input Data</h3>
                    <p class="step-card-desc">Salin-tempel teks berita yang mencurigakan atau unggah gambar yang ingin dicek.</p>
                </div>

                <div class="footer-card step-verification-card">
                    <div class="footer-step-badge">2</div>
                    <span class="material-symbols-outlined step-card-icon">travel_explore</span>
                    <h3 class="step-card-title">Proses Penelusuran</h3>
                    <p class="step-card-desc">Klik tombol <strong>'Telusuri'</strong>. Sistem AI kami akan melakukan audit silang terhadap ribuan database berita terpercaya secara real-time.</p>
                </div>

                <div class="footer-card step-verification-card">
                    <div class="footer-step-badge">3</div>
                    <span class="material-symbols-outlined step-card-icon">description</span>
                    <h3 class="step-card-title">Membaca Laporan</h3>
                    <p class="step-card-desc">Dapatkan skor persentase validitas, analisis konteks komprehensif oleh AI, dan tautan langsung ke sumber orisinal sebagai bukti.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="footer-page__content section-spacing-top">
        <div class="footer-page__container">
            <div class="footer-card footer-card--accent banner-wrapper-padding">
                <div class="wa-section-layout">
                    <div class="footer-page__stack info-stack-gap">
                        <div>
                            <div class="mini-uppercase-badge">FITUR INSTAN</div>
                            <h2 class="wa-banner-title">Panduan WhatsApp Bot</h2>
                            <p class="wa-banner-desc">
                                Dapatkan verifikasi secepat kilat langsung dari aplikasi pesan Anda. Solusi praktis untuk melawan hoaks di dalam grup keluarga atau komunitas.
                            </p>
                        </div>

                        <ul class="footer-list">
                            <li>Simpan nomor official bot Lensa Hoax di kontak ponsel Anda.</li>
                            <li>Teruskan (Forward) pesan teks atau foto siber yang meragukan.</li>
                            <li>Terima balasan instan dari mesin AI kami berisi status keaslian data tersebut.</li>
                        </ul>

                        <div class="action-btn-space">
                            <a href="https://wa.me/6289508135121" target="_blank" class="whatsapp-btn-green">
                                <span class="material-symbols-outlined fill-icon">chat</span>
                                Hubungkan WhatsApp Sekarang
                            </a>
                        </div>
                    </div>

                    <div class="flex-center">
                        <div class="whatsapp-mockup">
                            <div class="wa-mockup-header">
                                <div class="wa-avatar-mini">LH</div>
                                <div>
                                    <p class="wa-name">Lensa Hoax Bot</p>
                                    <p class="wa-status">Online</p>
                                </div>
                            </div>
                            <div class="footer-page__stack bubble-stack-gap">
                                <div class="chat-bubble-user">
                                    "Apakah bantuan sosial di tautan ini resmi?"
                                </div>
                                <div class="chat-bubble-bot">
                                    <strong class="bot-alert-title">Hasil Verifikasi AI:</strong>
                                    Konten terindikasi <span class="badge-hoax-text">HOAKS</span>. Informasi serupa telah divalidasi oleh FactCheck.id...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Landing Page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
@endpush

@section('content')

    {{-- NAVBAR --}}
    @include('user.partials.navbar')

    {{-- HERO --}}
    <section class="hero">
        <div class="container hero-flex">

            <div class="hero-text">
                <h1>Lensa Hoax:<br>Verifikasi Informasi dan Keaslian Foto dengan AI</h1>
                <p>Jangan mudah percaya berita yang belum tentu benar. Pastikan keaslian teks berita dan foto kejadian
                    dengan sistem deteksi cerdas kami.</p>

                <div class="hero-btn">
                    <a href="{{ route('whatsapp.page') }}" class="btn-red"
                        style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fab fa-whatsapp"></i> Dapatkan melalui Whatsapp
                    </a>

                    <a href="{{ route('deteksi') }}" class="btn-outline"
                        style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        Coba Sekarang !
                    </a>
                </div>
            </div>

            <div class="hero-img">
                <img src="{{ asset('img/landing-page.png') }}">
            </div>

        </div>
    </section>

    <div class="hero-border"></div>


    {{-- STATISTIK SECTION --}}
    <section class="statistics-section">
        <div class="container stats-grid">
            <div class="stat-card">
                <h3>{{ number_format($totalVerified) }}</h3>
                <p>Informasi Terverifikasi</p>
            </div>
            <div class="stat-card">
                <h3>{{ $hoaxPercentage }}%</h3>
                <p>Hoax Terdeteksi</p>
            </div>
            <div class="stat-card">
                <h3>{{ number_format($totalHoax) }}</h3>
                <p>Data Hoax</p>
            </div>
            <div class="stat-card">
                <h3>{{ number_format($totalUsers) }}</h3>
                <p>Pengguna Terdaftar</p>
            </div>
        </div>
    </section>

    {{-- CARA MENGGUNAKAN (STEPPER) SECTION --}}
    <section class="how-to-use-section">
        <div class="container">
            <div class="section-header">
                <h2>Cara Menggunakan</h2>
                <p>Hanya empat langkah sederhana untuk memastikan kebenaran informasi Anda.</p>
            </div>

            <div class="stepper-wrapper">
                <div class="stepper-line"></div>

                <div class="step-item">
                    <div class="step-icon-box">
                        <span class="material-symbols-outlined">login</span>
                    </div>
                    <h4>1. Masuk Menu</h4>
                    <p>Akses bagian pencarian di platform kami.</p>
                </div>

                <div class="step-item">
                    <div class="step-icon-box">
                        <span class="material-symbols-outlined">edit_note</span>
                    </div>
                    <h4>2. Input Data</h4>
                    <p>Masukkan teks berita atau unggah foto yang mencurigakan.</p>
                </div>

                <div class="step-item">
                    <div class="step-icon-box">
                        <span class="material-symbols-outlined">ads_click</span>
                    </div>
                    <h4>3. Klik Telusuri</h4>
                    <p>Biarkan teknologi AI kami bekerja memproses informasi tersebut.</p>
                </div>

                <div class="step-item">
                    <div class="step-icon-box">
                        <span class="material-symbols-outlined">verified</span>
                    </div>
                    <h4>4. Tunggu Hasil</h4>
                    <p>Dapatkan laporan detail tingkat keaslian informasi Anda.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR UTAMA SECTION --}}
    <section class="main-features-section">
        <div class="container">
            <div class="features-header">
                <h2>Fitur Utama</h2>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">drag_handle</span>
                    </div>
                    <h3>Deteksi Narasi</h3>
                    <p>Menganalisis pola kalimat dan membandingkan dengan database berita terpercaya.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">image_search</span>
                    </div>
                    <h3>Keaslian Gambar</h3>
                    <p>Memeriksa metadata dan jejak manipulasi digital pada foto secara forensik.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">
                            login
                        </span>
                    </div>

                    <h3>Integrasi Google</h3>
                    <p>Masuk dengan mudah menggunakan akun Google untuk menyimpan riwayat Anda.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">history</span>
                    </div>
                    <h3>Riwayat Verifikasi</h3>
                    <p>Kelola dan lihat kembali semua hasil verifikasi yang pernah Anda lakukan.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">trending_up</span>
                    </div>
                    <h3>Pencarian Populer</h3>
                    <p>Pantau isu-isu hoax yang sedang hangat diperbincangkan publik.</p>
                </div>

                <div class="feature-card border-green">
                    <div class="feature-icon-wrapper bg-soft-green">
                        <span class="material-symbols-outlined icon-green">chat</span>
                    </div>
                    <h3>WhatsApp Bot</h3>
                    <p>Verifikasi cepat lewat chat WhatsApp tanpa perlu buka website.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">sms_failed</span>
                    </div>
                    <h3>Sistem Feedback</h3>
                    <p>Bantu tingkatkan akurasi AI kami dengan memberikan konfirmasi hasil.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper bg-soft-red">
                        <span class="material-symbols-outlined icon-red">psychology</span>
                    </div>
                    <h3>Penjelasan</h3>
                    <p>Dapatkan alasan logis di balik setiap kesimpulan verifikasi yang diberikan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY US SECTION --}}
    <section class="why-us-section">
        <div class="container why-us-flex">

            <div class="why-us-content">
                <h2>Kenapa Lensa Hoax?</h2>

                <div class="why-item">
                    <div class="why-icon-box">
                        <span class="material-symbols-outlined">bolt</span>
                    </div>
                    <div class="why-text">
                        <h4>Instan</h4>
                        <p>Hasil keluar dalam hitungan detik setelah data diinput.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon-box">
                        <span class="material-symbols-outlined">target</span>
                    </div>
                    <div class="why-text">
                        <h4>Akurat</h4>
                        <p>Menggunakan sistem analisis berlapis untuk hasil yang terpercaya.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon-box">
                        <span class="material-symbols-outlined">touch_app</span>
                    </div>
                    <div class="why-text">
                        <h4>Mudah</h4>
                        <p>Antarmuka minimalis yang ramah untuk semua kalangan usia.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon-box">
                        <span class="material-symbols-outlined">info</span>
                    </div>
                    <div class="why-text">
                        <h4>Informatif</h4>
                        <p>Laporan lengkap mencakup sumber asli dan alasan deteksi.</p>
                    </div>
                </div>
            </div>

            <div class="why-us-media">
                <img src="{{ asset('img/data-statistik.png') }}">
            </div>

        </div>
    </section>

    {{-- TEAM SECTION --}}
    <section class="team-section">
        <div class="container">
            <div class="team-header">
                <h2>Tim Pengembang</h2>
            </div>

            <div class="team-grid">
                {{-- purnama --}}
                <div class="team-card">
                    <div class="avatar-wrapper">
                        <img src="{{ asset('img/landing/pur.png') }}" alt="Foto Purnama Rizky Nugraha">
                    </div>
                    <h3>Purnama Rizky Nugraha</h3>
                    <p class="role">Project Manager & AI Engineer</p>

                    <div class="team-socials">
                        <a href="#" class="social-icon" title="Website/Portofolio">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="mailto:purnamanugara492@gmail.com" class="social-icon" title="Email">
                            <span class="material-symbols-outlined">mail</span>
                        </a>
                    </div>
                </div>
                <div class="team-card">
                    <div class="avatar-wrapper">
                        <img src="{{ asset('img/landing/user-3296_256.png') }}" alt="Foto Adinda Ivanka Maysanda Putri">
                    </div>
                    <h3>Adinda Ivanka Maysanda Putri</h3>
                    <p class="role">Frontend Developer</p>

                    <div class="team-socials">
                        <a href="#" class="social-icon" title="Website/Portofolio">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="mailto:email@kamu.com" class="social-icon" title="Email">
                            <span class="material-symbols-outlined">mail</span>
                        </a>
                    </div>
                </div>
                <div class="team-card">
                    <div class="avatar-wrapper">
                        <img src="{{ asset('img/desi-karmila.jpeg') }}" alt="Foto Desi Karmila">
                    </div>
                    <h3>Desi Karmila</h3>
                    <p class="role">Frontend Developer</p>

                    <div class="team-socials">
                        <a href="https://portofolio-pink-pi.vercel.app/" class="social-icon" title="Website/Portofolio">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="mailto:desikarmila211@gmail.com" class="social-icon" title="Email">
                            <span class="material-symbols-outlined">mail</span>
                        </a>
                    </div>
                </div>
                <div class="team-card">
                    <div class="avatar-wrapper">
                        <img src="{{ asset('img/landing/user-3296_256.png') }}" alt="Foto Muhammad Reishi Fauzi Auguri">
                    </div>
                    <h3>Muhammad Reishi Fauzi Auguri</h3>
                    <p class="role">Backend Developer</p>
                    <div class="team-socials">
                        <a href="#" class="social-icon" title="Website/Portofolio">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="mailto:email@kamu.com" class="social-icon" title="Email">
                            <span class="material-symbols-outlined">mail</span>
                        </a>
                    </div>
                </div>
                <div class="team-card">
                    <div class="avatar-wrapper">
                        <img src="{{ asset('img/landing/user-3296_256.png') }}" alt="Foto Firman Dzaki Rahman">
                    </div>
                    <h3>Firman Dzaki Rahman</h3>
                    <p class="role">Backend Developer</p>
                    <div class="team-socials">
                        <a href="#" class="social-icon" title="Website/Portofolio">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                        <a href="mailto:email@kamu.com" class="social-icon" title="Email">
                            <span class="material-symbols-outlined">mail</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CALL TO ACTION SECTION --}}
    <section class="cta-banner-section">
        <div class="cta-banner__container">
            <h2 class="cta-banner__title">Sudah Siap Melawan Hoax?</h2>
            <p class="cta-banner__description">
                Bergabunglah bersama ribuan pengguna lainnya dalam menciptakan ruang digital yang bersih dan terpercaya.
            </p>

            <!-- Tombol Aksi -->
            <div class="cta-banner__action">
                <a href="{{ route('deteksi') }}" class="cta-banner__button">Coba Sekarang !</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lensa Hoax</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600&display=swap" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="container">
        <img src="{{ asset('img/logo-lensa.png') }}" class="logo-img">

        <a href="{{ route('login') }}" class="btn-login">
            <i class="fa fa-user"></i> Daftar | Masuk
        </a>
    </div>
</div>

<!-- HERO -->
<section class="hero">
    <div class="container hero-flex">

        <div class="hero-text">
            <h1>Saring Fakta<br>Cegah Hoax</h1>
            <p>Pastikan kebenaran setiap informasi dan<br>berita agar terhindar dari hoax.</p>

            <div class="hero-btn">
                <button class="btn-red">
                    <i class="fab fa-whatsapp"></i> Dapatkan melalui Whatsapp
                </button>

                <button class="btn-outline">
                    Coba Sekarang !
                </button>
            </div>
        </div>

        <div class="hero-img">
            <img src="{{ asset('img/landing-page.png') }}">
        </div>

    </div>
</section>

<div class="hero-border"></div>

<!-- TOP FEATURES -->
<section class="top-features">
    <div class="container">

        <div class="box red">
            <img src="{{ asset('img/akses-penuh.png') }}" class="icon">
            <h4>Akses Penuh</h4>
            <p>Simpan riwayat dan kelola semua informasimu di Lensa Hoax</p>
            <a href="{{ route('login') }}" class="btn-card red-btn">Daftar atau Masuk</a>
        </div>

        <div class="box blue">
            <img src="{{ asset('img/cek-cepat.png') }}" class="icon">
            <h4>Cek Cepat Tanpa Login</h4>
            <p>Langsung ke beranda dan verifikasi informasi Anda sekarang!</p>
            <button class="btn-card blue-btn">Cek Tanpa Login</button>
        </div>

        <div class="box green">
            <img src="{{ asset('img/whatsapp.png') }}" class="icon">
            <h4>Dapatkan Via Whatsapp</h4>
            <p>Teruskan pesan atau kirim file melalui whatsapp secara langsung</p>
            <button class="btn-card green-btn">Hubungi Bot Whatsapp</button>
        </div>

    </div>
</section>

<!-- FITUR -->
<section class="fitur">
    <div class="container">
        <h2>Fitur Utama</h2>

        <div class="fitur-list">

            <div class="card">
                <img src="{{ asset('img/informasi-cepat.png') }}" class="icon-big">
                <h4>Cek Informasi Cepat</h4>
                <p>Verifikasi berita atau informasi hanya dalam beberapa detik dengan hasil yang mudah dipahami.</p>
            </div>

            <div class="card">
                <img src="{{ asset('img/deteksi-gambar.png') }}" class="icon-big">
                <h4>Deteksi Gambar</h4>
                <p>Unggah gambar untuk mengetahui apakah gambar tersebut asli atau hasil manipulasi.</p>
            </div>

            <div class="card">
                <img src="{{ asset('img/hasil-analisis.png') }}" class="icon-big">
                <h4>Hasil Analisis</h4>
                <p>Dapatkan hasil verifikasi lengkap dengan penjelasan yang ringkas dan terpercaya.</p>
            </div>

        </div>
    </div>
</section>

<!-- CARA KERJA -->
<section class="how">
    <div class="container">
        <h2>Cara Kerja</h2>

        <div class="steps">

            <div class="step-card">
                <img src="{{ asset('img/masukan-info.png') }}" class="icon">
                <h4>Masukkan Informasi</h4>
                <p>Ketik, tempel, atau unggah informasi yang ingin Anda cek.</p>
            </div>

            <div class="arrow">
                <i class="fa fa-arrow-right"></i>
            </div>

            <div class="step-card">
                <img src="{{ asset('img/proses-analisis.png') }}" class="icon">
                <h4>Proses Analisis</h4>
                <p>Sistem akan menganalisis data menggunakan teknologi deteksi hoax.</p>
            </div>

            <div class="arrow">➜</div>

            <div class="step-card">
                <img src="{{ asset('img/lihat-hasil.png') }}" class="icon">
                <h4>Lihat Hasil</h4>
                <p>Dapatkan hasil verifikasi beserta penjelasan apakah informasi tersebut valid atau hoax.</p>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    lensa_hoax@2026
</footer>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lensa Hoax - Dapatkan Melalui WhatsApp</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/whatsapp.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
</head>
<body>
    <div class="wa-page">
        @include('user.partials.navbar', ['variant' => 'wa', 'activeWhatsApp' => true])

        <main class="wa-content">
            <section class="wa-hero">
                <div class="wa-hero__visual" aria-hidden="true">
                    <img src="{{ asset('img/wa-top.png') }}" alt="Ilustrasi WhatsApp" class="wa-hero__image">
                </div>

                <div class="wa-hero__text">
                    <h1>Cek Hoaks Langsung via WhatsApp</h1>
                    <p>Periksa kebenaran informasi dengan mudah melalui WhatsApp.</p>

                    <p>
                        Klik <strong>Cek via WhatsApp</strong>, kirim atau teruskan pesan yang ingin diverifikasi,
                        lalu tunggu hasilnya. Setelah terhubung dengan bot Lensa Hoax, Anda dapat menggunakan
                        nomor WhatsApp yang sama untuk login ke website.
                    </p>

                    <p><strong>Klik Cek via WhatsApp untuk memulai.</strong></p>

                    <a id="wa-cta" href="https://wa.me/6289508135121" class="wa-btn-cta" target="_blank">
                    <iconify-icon icon="garden:whatsapp-fill-16" width="25" height="25"></iconify-icon>
                    Cek Via WhatsApp
                </a>
                </div>
            </section>

            <section class="wa-steps">
                <h2>Alur singkat penggunaan</h2>
                <div class="wa-steps__grid">
                    <article class="wa-step-card">
                        <iconify-icon icon="mdi:cursor-default-click" width="52" height="52"></iconify-icon>
                        <h3>1. Buka WhatsApp</h3>
                        <p>Tekan tombol Cek Via WhatsApp untuk memulai percakapan dengan bot.</p>
                    </article>

                    <div class="wa-step-arrow"><iconify-icon icon="mdi:arrow-right-bold" width="46" height="46"></iconify-icon></div>

                    <article class="wa-step-card">
                        <iconify-icon icon="mdi:message-question" width="52" height="52"></iconify-icon>
                        <h3>2. Kirim command</h3>
                        <p>Gunakan command seperti #detect, #info, #trending, atau #history sesuai kebutuhan.</p>
                    </article>

                    <div class="wa-step-arrow"><iconify-icon icon="mdi:arrow-right-bold" width="46" height="46"></iconify-icon></div>

                    <article class="wa-step-card">
                        <iconify-icon icon="line-md:loading-alt-loop" width="52" height="52"></iconify-icon>
                        <h3>3. Lihat hasilnya</h3>
                        <p>Sistem akan memproses pesan dan menampilkan hasil secara cepat dan akurat.</p>
                    </article>
                </div>
            </section>

            <section class="wa-commands">
                <div class="wa-commands__top">
                    <div class="wa-section-heading wa-section-heading--commands">
                        <span class="wa-section-heading__eyebrow">Command Bot</span>
                        <h2>Fitur utama via WhatsApp</h2>
                        <p>
                            Gunakan command berikut untuk mengakses fitur bot dengan cepat dan terarah.
                        </p>
                    </div>

                    {{-- <aside class="wa-tip-card" aria-label="Tips penggunaan command">
                        <div class="wa-tip-card__icon">
                            <iconify-icon icon="mdi:lightbulb-on-outline" width="28" height="28"></iconify-icon>
                        </div>
                        <div>
                            <h3>Tips</h3>
                            <p>Ketik command di WhatsApp persis seperti contoh untuk hasil yang akurat.</p>
                        </div>
                    </aside> --}}
                </div>
                <div class="wa-commands__layout">
                    <div class="wa-command-list">
                        <div class="wa-command-list__heading">01. Daftar Command</div>

                        <article class="wa-command-row wa-command-row--detect">
                            <div class="wa-command-row__icon">
                                <iconify-icon icon="mdi:search" width="28" height="28"></iconify-icon>
                            </div>
                            <div class="wa-command-row__content">
                                <h3>#detect</h3>
                                <p>Kirim berita atau informasi, lalu ketik #detect untuk memeriksa keaslian pesan terakhir yang Anda kirim.</p>
                            </div>
                            <div class="wa-command-row__example">
                                <span>Contoh penggunaan</span>
                                <strong>#detect</strong>
                            </div>
                        </article>

                        <article class="wa-command-row wa-command-row--info">
                            <div class="wa-command-row__icon">
                                <iconify-icon icon="mdi:information-outline" width="28" height="28"></iconify-icon>
                            </div>
                            <div class="wa-command-row__content">
                                <h3>#info</h3>
                                <p>Lihat informasi tentang sistem Lensa Hoax.</p>
                            </div>
                            <div class="wa-command-row__example">
                                <span>Contoh penggunaan</span>
                                <strong>#info</strong>
                            </div>
                        </article>

                        <article class="wa-command-row wa-command-row--trend">
                            <div class="wa-command-row__icon">
                                <iconify-icon icon="mdi:trending-up" width="28" height="28"></iconify-icon>
                            </div>
                            <div class="wa-command-row__content">
                                <h3>#trending</h3>
                                <p>Lihat daftar tren hoaks terpopuler saat ini.</p>
                            </div>
                            <div class="wa-command-row__example">
                                <span>Contoh penggunaan</span>
                                <strong>#trending</strong>
                            </div>
                        </article>

                        <article class="wa-command-row wa-command-row--history">
                            <div class="wa-command-row__icon">
                                <iconify-icon icon="mdi:history" width="28" height="28"></iconify-icon>
                            </div>
                            <div class="wa-command-row__content">
                                <h3>#history</h3>
                                <p>Lihat riwayat pencarian terakhir Anda di bot.</p>
                            </div>
                            <div class="wa-command-row__example">
                                <span>Contoh penggunaan</span>
                                <strong>#history</strong>
                            </div>
                        </article>

                        {{-- <div class="wa-command-note">
                            <div class="wa-command-note__icon">
                                <iconify-icon icon="mdi:shield-check-outline" width="22" height="22"></iconify-icon>
                            </div>
                            <div>
                                <h3>Catatan</h3>
                                <p>Pastikan command diketik dengan benar dan tanpa tambahan spasi. Gunakan bahasa yang sopan untuk hasil terbaik.</p>
                            </div>
                        </div> --}}
                    </div>

                    <div class="wa-command-side">
                        <div class="wa-command-side__heading">02. Cara Menggunakan</div>
                        <ol class="wa-command-steps">
                            <li>Klik "Check Via WhatsApp" untuk memulai.</li>
                            <li>Ketik command sesuai fitur yang ingin digunakan.</li>
                            <li>Kirim pesan dan bot akan memberikan respon.</li>
                        </ol>

                        <div class="wa-command-preview">
                            <img src="{{ asset('img/command.jpg') }}" alt="Contoh tampilan chat WhatsApp" class="wa-command-preview__image">
                        </div>
                    </div>
                </div>
            </section>

            <section class="wa-reason">
                <h2>Mengapa menggunakan WhatsApp?</h2>
                <div class="wa-reason__row">
                    <div class="wa-brand-circle">
                        <img src="{{ asset('img/wa-3d.png') }}" alt="Ilustrasi WhatsApp 3D" class="wa-brand-image">
                    </div>
                    <div class="wa-benefits">
                        <div class="wa-pill wa-pill--blue">Lebih cepat dan praktis</div>
                        <div class="wa-pill wa-pill--pink">Tidak memerlukan login atau pendaftaran</div>
                        <div class="wa-pill wa-pill--green">Dapat langsung meneruskan pesan dari grup</div>
                        <div class="wa-pill wa-pill--yellow">Cocok untuk semua pengguna, termasuk yang belum terbiasa dengan teknologi</div>
                    </div>
                </div>
            </section>

            <section class="wa-tips">
                <h2>Tips</h2>
                <div class="wa-tips__content">
                    <ul>
                        <li>Gunakan fitur forward di WhatsApp agar lebih cepat.</li>
                        <li>Anda dapat mengirim teks, gambar, maupun tautan.</li>
                        <li>Pastikan pesan yang dikirim jelas agar hasil lebih akurat.</li>
                    </ul>
                    <a id="wa-cta" href="https://wa.me/6289508135121" class="wa-btn-cta" target="_blank">
                    <iconify-icon icon="garden:whatsapp-fill-16" width="25" height="25"></iconify-icon>
                    Cek Via WhatsApp
                </a>
                </div>
            </section>
        </main>
        @include('layouts.footer')
    </div>
    <script src="{{ asset('js/user/profile-popup.js') }}"></script>
</body>
</html>

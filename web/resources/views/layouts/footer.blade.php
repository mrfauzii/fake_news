<footer style="
    background: linear-gradient(90deg, #73070B 0%, #B8201D 100%);
    color:white;
    font-family:'Poppins', sans-serif;
    margin-top:50px;
">

    <div style="
        max-width:1200px;
        margin:auto;
        padding:45px 20px;
        display:flex;
        flex-wrap:wrap;
        justify-content:space-between;
        gap:40px;
    ">

        <!-- Logo / Desc -->
        <div style="flex:1; min-width:260px;">

            <h2 style="
                font-size:28px;
                font-weight:700;
                margin-bottom:12px;
                color:#ffffff;
                letter-spacing:0.5px;
            ">
                Lensa Hoax
            </h2>

            <div style="
                width:70px;
                height:4px;
                background:#ffd166;
                border-radius:20px;
                margin-bottom:16px;
            "></div>

            <p style="
                color:#ffe5e5;
                font-size:14px;
                line-height:1.8;
                max-width:360px;
            ">
                Platform AI untuk mendeteksi berita palsu dari teks dan gambar.
                Mendukung integrasi WhatsApp dan penyimpanan riwayat pengguna.
            </p>
        </div>

        <!-- Dokumentasi -->
        <div style="min-width:200px;">

            <h3 style="
                font-size:17px;
                margin-bottom:16px;
                font-weight:600;
                color:white;
            ">
                Dokumentasi
            </h3>

            <div style="display:flex; flex-direction:column; gap:12px;">

                <a href="/panduan" style="
                    color:#ffe5e5;
                    text-decoration:none;
                    font-size:14px;
                ">
                    Panduan Pengguna
                </a>

                <a href="/faq" style="
                    color:#ffe5e5;
                    text-decoration:none;
                    font-size:14px;
                ">
                    FAQ
                </a>

            </div>
        </div>

        <!-- Informasi -->
        <div style="min-width:200px;">

            <h3 style="
                font-size:17px;
                margin-bottom:16px;
                font-weight:600;
                color:white;
            ">
                Informasi
            </h3>

            <div style="display:flex; flex-direction:column; gap:12px;">

                <a href="/tentang-kami" style="
                    color:#ffe5e5;
                    text-decoration:none;
                    font-size:14px;
                ">
                    Tentang Kami
                </a>

                <a href="/kebijakan-privasi" style="
                    color:#ffe5e5;
                    text-decoration:none;
                    font-size:14px;
                ">
                    Kebijakan Privasi
                </a>
            </div>
        </div>

    </div>

    <!-- Bottom -->
    <div style="
        border-top:1px solid rgba(255,255,255,0.15);
        padding:18px;
        text-align:center;
        font-size:13px;
        color:#ffe5e5;
        background:rgba(0,0,0,0.12);
        backdrop-filter:blur(4px);
    ">
        &copy; {{ date('Y') }} Lensa Hoax
    </div>

</footer>
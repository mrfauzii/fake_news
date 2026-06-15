<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <!-- TAMBAHAN WAJIB -->
    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/Lensa-saja.png') }}">

    <!-- GLOBAL CSS -->
    <link rel="stylesheet"
          href="{{ asset('css/admin/global.css') }}">

    <!-- PAGE CSS -->
    @stack('styles')

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- HEADER -->
<div class="header">

    <!-- KIRI -->
    <div class="header-left">

        <img
            src="{{ asset('img/logo-lensa.png') }}"
            class="logo-header"
            alt="Logo"
        >

    </div>

    <!-- KANAN -->
    <div class="header-right">

        @hasSection('header-search')
            @yield('header-search')
        @endif

        <div class="admin-info">

            <div class="admin-text">
                <strong>Admin</strong>
            </div>

            <div class="admin-icon">
                <i class="fa fa-user"></i>
            </div>

        </div>

    </div>

</div>


<div class="admin-layout">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <ul>

            <div class="sidebar-toggle">

                <button
                    id="toggleSidebar"
                    class="toggle-btn"
                >
                    <i class="fa fa-bars"></i>
                </button>

            </div>

            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="/admin/dashboard">
                    <i class="fa fa-home"></i>
                    <span>Beranda</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/pencarian') ? 'active' : '' }}">
                <a href="/admin/pencarian">
                    <i class="fa fa-search"></i>
                    <span>Cek Berita</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/riwayat') ? 'active' : '' }}">
                <a href="/admin/riwayat">
                    <i class="fa fa-globe"></i>
                    <span>Riwayat Global</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/user') ? 'active' : '' }}">
                <a href="/admin/user">
                    <i class="fa fa-users"></i>
                    <span>Data Pengguna</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/umpanbalik') ? 'active' : '' }}">
                <a href="/admin/umpanbalik">
                    <i class="fa fa-comment"></i>
                    <span>Umpan Balik</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/setting') ? 'active' : '' }}">
                <a href="/admin/setting">
                    <i class="fa fa-cog"></i>
                    <span>Setting</span>
                </a>
            </li>

        </ul>


        <!-- LOGOUT -->
        <div class="logout">

            <form
                method="Get"
                action="{{ route('beranda') }}"
            >
                

                <button
                    type="submit"
                    class="logout-btn"
                >

                    <i class="fa fa-sign-out-alt"></i>

                    Keluar Dashboard

                </button>

            </form>

        </div>

    </div>


    <!-- MAIN CONTENT -->
    <div class="main-content">

<div id="adminBannerContainer" class="admin-banner-container"></div>
        @yield('content')

    </div>

</div>


<script>

document.addEventListener(
'DOMContentLoaded',

function(){

    const toggleBtn =
    document.getElementById(
        'toggleSidebar'
    );

    const sidebar =
    document.querySelector(
        '.sidebar'
    );

    toggleBtn.addEventListener(
    'click',

    ()=>{

        sidebar.classList.toggle(
            'collapsed'
        );

        if(
            window.innerWidth <= 768
        ){

            sidebar.classList.toggle(
                'active'
            );

        }

    });

});

</script>

<script>
function showAdminBanner(message, type = 'success') {
    const container = document.getElementById('adminBannerContainer');
    if (!container) return;

    const banner = document.createElement('div');
    banner.className = `admin-banner admin-banner--${type}`;
    banner.innerHTML = `
        <i class="fa ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
        <span>${message}</span>
    `;

    container.appendChild(banner);

    requestAnimationFrame(() => {
        banner.style.opacity = '1';
        banner.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-10px)';
        setTimeout(() => { banner.remove(); }, 300);
    }, 4500);
}
</script>

</body>
</html>
@php
    $isPopularPage = request()->routeIs('pencarian.populer') || request()->is('pencarian/populer*');
    $isRiwayatPage = request()->routeIs('riwayat') || request()->is('riwayat*');
    $isBerandaPage = request()->routeIs('beranda') || request()->is('/');
    $isWhatsappPage = request()->routeIs('whatsapp.page') || request()->is('dapatkan-whatsapp*') || request()->is('whatsapp*');
    $isLoginPage = request()->routeIs('login') || request()->is('login*');
@endphp

<header class="lh-navbar {{ isset($variant) && $variant === 'wa' ? 'wa-navbar' : '' }}">
    <!-- Logo -->
    <a href="{{ route('landing') }}" class="lh-logo {{ isset($variant) && $variant === 'wa' ? 'wa-logo' : '' }}" @if(isset($variant) && $variant === 'wa') aria-label="Kembali ke Pencarian" @endif>
        <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img {{ isset($variant) && $variant === 'wa' ? 'wa-logo__img' : '' }}">
    </a>

    <!-- Mobile Search Bar -->
    <div class="lh-mobile-search">
        <p class="lh-mobile-search__text">Cari, temukan, antisipasi misinformasi</p>
    </div>

    <!-- Nav Icons - order chosen to match mobile layout (popular, history, search, whatsapp, user) -->
    <nav class="lh-nav-icons {{ isset($variant) && $variant === 'wa' ? 'wa-nav-actions' : '' }}" @if(isset($variant) && $variant === 'wa') aria-label="Aksi pengguna" @endif>
        <a href="{{ route('pencarian.populer') }}" class="lh-nav-btn {{ $isPopularPage ? 'lh-nav-btn--active' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Pencarian Terpopuler" @if($isPopularPage) aria-current="page" @endif>
            <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                <span>Pencarian Terpopuler</span>
            </span>
        </a>

        @auth
            <a href="{{ route('riwayat') }}" class="lh-nav-btn {{ $isRiwayatPage || (isset($activeRiwayat) && $activeRiwayat) ? 'lh-nav-btn--active' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Riwayat" @if($isRiwayatPage || (isset($activeRiwayat) && $activeRiwayat)) aria-current="page" @endif>
                <iconify-icon icon="fontisto:history" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="fontisto:history" width="17" height="17"></iconify-icon>
                    <span>Riwayat Pencarian Anda</span>
                </span>
            </a>
        @else
            <a href="{{ route('login') }}" class="lh-nav-btn {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Riwayat (masuk diperlukan)">
                <iconify-icon icon="fontisto:history" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="fontisto:history" width="17" height="17"></iconify-icon>
                    <span>Riwayat Pencarian (Masuk untuk melihat)</span>
                </span>
            </a>
        @endauth

        <a href="{{ route('beranda') }}" class="lh-nav-btn lh-nav-btn--search {{ $isBerandaPage ? 'lh-nav-btn--active' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Telusuri Informasi" @if($isBerandaPage) aria-current="page" @endif>
            <iconify-icon icon="mdi:magnify" width="26" height="26"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="mdi:magnify" width="18" height="18"></iconify-icon>
                <span>Telusuri Informasi</span>
            </span>
        </a>

        <a href="{{ route('whatsapp.page') }}" class="lh-nav-btn {{ ($isWhatsappPage || (isset($variant) && $variant === 'wa')) ? 'lh-nav-btn--active lh-nav-btn--whatsapp-visible' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn wa-nav-btn--whatsapp' : '' }} {{ isset($activeWhatsApp) && $activeWhatsApp ? 'wa-nav-btn--active' : '' }}" aria-label="WhatsApp" @if($isWhatsappPage) aria-current="page" @endif>
            <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip {{ isset($variant) && $variant === 'wa' ? 'lh-nav-tooltip--always-visible' : '' }}" role="tooltip">
                <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                <span>Dapatkan Melalui Whatsapp</span>
            </span>
        </a>

        @auth
            <a href="#" class="lh-nav-btn lh-nav-btn--user js-profile-toggle {{ request()->routeIs('profile') ? 'lh-nav-btn--active' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Profil" aria-controls="user-profile-popup" aria-expanded="false" data-profile-toggle="user-profile-popup">
                <iconify-icon icon="mdi:account-circle" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                    <iconify-icon icon="mdi:account-circle" width="18" height="18"></iconify-icon>
                    <span>Profil Pengguna</span>
                </span>
            </a>
        @else
            <a href="{{ route('login') }}" class="lh-nav-btn lh-nav-btn--user {{ $isLoginPage ? 'lh-nav-btn--active' : '' }} {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Daftar atau Masuk" @if($isLoginPage) aria-current="page" @endif>
                <iconify-icon icon="mdi:user" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                    <iconify-icon icon="mdi:user" width="18" height="18"></iconify-icon>
                    <span>Daftar | Masuk</span>
                </span>
            </a>
        @endauth
    </nav>

    @auth
        @include('user.partials.profile-popup', ['popupId' => 'user-profile-popup'])
    @endauth
</header>

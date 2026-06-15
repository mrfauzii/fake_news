@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/pencarian-terpopuler.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        .lh-popular-card__bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-popular-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
    </style>
@endpush

@section('content')

    <div id="alertContainer"></div>
    @if (session('success'))
        <div id="successAlert"
            style="background-color: #28a745; color: white; padding: 15px 25px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; min-width: 300px; font-weight: 500; transition: 0.5s; opacity: 1;">
            <i class="fa fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <div class="feedback-title">
        <h1>Dashboard Utama</h1>
        <p>Data terakhir diperbarui: {{ $lastDate }} </p>
    </div>

    <!-- STATS -->
    <div class="stats-container">

        <!-- PENGGUNA -->
        <div class="stats-card active">
            <div class="stats-top">
                <span>PENGGUNA TERDAFTAR</span>
                <div class="icon-box red">
                    <i class="fa fa-user"></i>
                </div>
            </div>
            <h2>{{ number_format($dashboardStats['total_pengguna']) }}</h2>
            <p class="positive">Total pengguna aktif di sistem</p>
        </div>

        <!-- BERITA -->
        <div class="stats-card active">
            <div class="stats-top">
                <span>BERITA TERDETEKSI</span>
                <div class="icon-box red">
                    <i class="fa fa-newspaper"></i>
                </div>
            </div>
            <h2>{{ number_format($dashboardStats['total_berita']) }}</h2>
            <p class="negative">Total berita yang telah dianalisis</p>
        </div>

        <!-- UMPAN BALIK -->
        <div class="stats-card active">
            <div class="stats-top">
                <span>UMPAN BALIK</span>
                <div class="icon-box red">
                    <i class="fa fa-comment"></i>
                </div>
            </div>
            <h2>{{ number_format($dashboardStats['total_umpan_balik']) }}</h2>
            <p class="neutral">Total masukan dari pengguna</p>
        </div>

    </div>
    <div class="section-header">
        <h2><i class="fa fa-chart-line"></i> Pencarian Populer</h2>
    </div>
    <div class="dashboard-popular-grid">

        @foreach ($dashboardPopular as $item)
            @php
                $isHoax = str_contains(strtolower($item['badge']), 'hoax');
                $isFakta = str_contains(strtolower($item['badge']), 'fakta');

                $badgeClass = 'lh-popular-card__badge--with-pct';

                if ($isHoax) {
                    $badgeClass .= ' lh-popular-card__badge--hoax';
                } elseif ($isFakta) {
                    $badgeClass .= ' lh-popular-card__badge--fakta';
                }

                $rankClass = $isHoax ? 'rank-hoax' : 'rank-fakta';
            @endphp

            <article class="lh-popular-card">

                <div class="lh-popular-card__rank {{ $rankClass }}">
                    #{{ $item['rank'] }} {{ $item['badge'] }}
                </div>

                <div class="lh-popular-card__excerpt">
                    {{ $item['input'] }}
                </div>

                <div class="lh-popular-card__content">

                    <div class="lh-popular-card__meta">
                        <p class="lh-popular-card__count">
                            <strong>{{ number_format($item['count'], 0, ',', '.') }}</strong>
                            orang mencari informasi serupa
                        </p>
                    </div>

                    <div class="lh-popular-card__bottom">

                        <span class="{{ $badgeClass }}">
                            <span class="lh-popular-card__badge-pct">
                                {{ round($item['confidence'] * 100) }}%
                            </span>

                            <span class="lh-popular-card__badge-label">
                                {{ $item['badge'] }}
                            </span>
                        </span>



                    </div>

                </div>

            </article>
        @endforeach
    </div>
    <script>
        function triggerSettingAlert(message, type = 'success') {
            const container = document.getElementById('alertContainer');

            container.innerHTML = `
                    <div id="successAlert" class="success-alert">
                        <i class="fa ${
                            type === 'success'
                                ? 'fa-circle-check'
                                : 'fa-circle-exclamation'
                        }"></i>
                        ${message}
                    </div>
                `;

            setTimeout(() => {
                const alert = document.getElementById('successAlert');

                if (alert) {
                    alert.style.transition = '0.5s';
                    alert.style.opacity = '0';

                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }
            }, 5000);
        }

        // Jalankan fungsi auto-hide untuk session flash biasa jika ada saat load
        const initialSuccessAlert = document.getElementById('successAlert');
        if (initialSuccessAlert) {
            setTimeout(() => {
                initialSuccessAlert.style.transition = '0.5s';
                initialSuccessAlert.style.opacity = '0';
                setTimeout(() => {
                    initialSuccessAlert.remove();
                }, 500);
            }, 5000);
        }
    </script>
@endsection

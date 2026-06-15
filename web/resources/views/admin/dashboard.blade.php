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

    {{-- Use top banner for session flash --}}
    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', function(){ showAdminBanner("{{ session('success') }}", 'success'); });</script>
    @endif
    @if (session('error'))
        <script>document.addEventListener('DOMContentLoaded', function(){ showAdminBanner("{{ session('error') }}", 'error'); });</script>
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
            showAdminBanner(message, type);
        }
    </script>
@endsection

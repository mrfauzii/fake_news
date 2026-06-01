@extends('layouts.app')

@section('title', 'Pencarian Terpopuler')

@section('content')
    @include('user.partials.navbar')
    <style>
        /* Container utama untuk memposisikan konten di tengah layar */
.lh-verify-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f4f6f8; /* Warna background abu-abu terang yang kalem */
    padding: 1rem;
    font-family: inherit;
}

/* Kotak putih bergaya card minimalis */
.lh-verify-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 2.5rem 2rem;
    text-align: center;
    max-width: 400px;
    width: 100%;
}

/* Teks judul dan deskripsi */
.lh-verify-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 1rem 0 0.5rem 0;
}

.lh-verify-text {
    font-size: 0.95rem;
    color: #6b7280;
    margin: 0 0 1.75rem 0;
    line-height: 1.5;
}

/* Tombol eksekusi yang selaras dengan desain modern */
.lh-verify-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    background-color: #2563eb; /* Warna biru standar primary, sesuaikan jika app kamu pakai warna lain */
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.lh-verify-btn:hover {
    background-color: #1d4ed8; /* Warna biru lebih gelap saat di-hover */
}
    </style>
<div class="lh-verify-wrapper">
    <div class="lh-verify-card">
        
        <iconify-icon icon="mdi:whatsapp" width="56" height="56" style="color: #25D366;"></iconify-icon>

        <h2 class="lh-verify-title">Verifikasi WhatsApp</h2>
        <p class="lh-verify-text">
            Tinggal satu langkah lagi. Silakan klik tombol di bawah ini untuk menyelesaikan proses verifikasi nomor Anda.
        </p>

        <form action="{{ route('wa.verify.action') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <button type="submit" class="lh-verify-btn">
                <iconify-icon icon="mdi:check-bold" width="18" height="18"></iconify-icon>
                <span>Verifikasi Sekarang</span>
            </button>
        </form>
        
    </div>
</div>
 @endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush
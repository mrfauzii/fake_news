@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
<!-- CSS Admin -->
<link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
<!-- CSS Bawaan Pencarian -->
<link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/pencarian.css') }}">
@endpush

@section('content')
<!-- INI YANG SEBELUMNYA HILANG: Tag pembuka wrapper -->
<div class="lh-wrapper">

    <!-- ========== MAIN CONTENT ========== -->
    <main class="lh-main">

        <!-- Heading -->
        <div class="lh-heading">
            <h1 class="lh-heading__title">Pastikan Fakta dengan Mudah</h1>
            <p class="lh-heading__subtitle">Verifikasi berbagai jenis informasi dengan mudah di Lensa Hoax</p>
        </div>

        <!-- Two-panel cards -->
        <div class="lh-panels">

            <!-- Panel Kiri: Input -->
            <div class="lh-card lh-card--input">
                <div class="lh-card__body">
                    <textarea
                        class="lh-textarea"
                        id="inputInformasi"
                        name="query"
                        placeholder="Ketik, Tempel, atau Unggah informasi ....."></textarea>
                    <!-- Image preview area -->
                    <div id="imagePreviewContainer" style="display: none; margin-top: 16px;">
                        <button
                            type="button"
                            id="btnRemoveImage"
                            class="lh-image-preview__remove"
                            aria-label="Hapus gambar yang diunggah"
                            title="Hapus gambar">
                            &times;
                        </button>
                        <img id="imagePreview" src="" alt="Preview gambar" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: block; margin-bottom: 8px;">
                        <p id="imagePreviewText" style="text-align: center; font-size: 12px; color: #666; margin: 0;">
                            ✓ Gambar siap untuk diteliti
                        </p>
                    </div>
                </div>
                <div class="lh-card__footer">
                    <button class="lh-btn lh-btn--upload" id="btnUnggah" type="button">
                        <iconify-icon icon="ic:sharp-upload" width="22" height="22"></iconify-icon>
                        Unggah Gambar
                    </button>
                    <!-- Hidden file input -->
                    <input type="file" id="fileInput" accept="image/*" style="display:none;">

                    <button class="lh-btn lh-btn--search" id="btnTelusuri" type="button">
                        <iconify-icon icon="ic:outline-search" width="22" height="22"></iconify-icon>
                        Telusuri
                    </button>
                </div>
            </div>

            <!-- Panel Kanan: Hasil -->
            <div class="lh-card lh-card--result">
                <div class="lh-card__header">
                    <iconify-icon icon="ic:outline-search" class="lh-result-icon" width="32" height="32"></iconify-icon>
                    <span class="lh-result-title">Hasil Penelusuran</span>
                </div>
                <div class="lh-card__body lh-card__body--result" id="hasilPenelusuran">
                    <!-- Hasil akan muncul di sini -->
                </div>
            </div>

        </div>
    </main>

    <!-- Feedback modal (hidden by default) -->
    <div id="feedbackModal" class="feedback-modal" style="display:none;">
        <div class="feedback-modal__overlay" id="feedbackOverlay"></div>
        <div class="feedback-modal__box" role="dialog" aria-modal="true" aria-labelledby="feedbackTitle">
            <header class="feedback-modal__header">
                <h3 id="feedbackTitle">Umpan Balik Hasil Penelusuran</h3>
            </header>
            <div class="feedback-modal__body">
                <textarea id="feedbackText" placeholder="Berikan umpan balik Anda tentang hasil ini..." maxlength="1000"></textarea>
                <p class="feedback-modal__note">Terima kasih atas kontribusi Anda untuk meningkatkan kualitas verifikasi.</p>
                <div class="feedback-modal__status" id="feedbackStatus" aria-live="polite"></div>
            </div>
            <footer class="feedback-modal__footer">
                <button class="lh-btn lh-btn--upload" id="btnCancelFeedback" type="button">Batal</button>
                <button class="lh-btn lh-btn--search" id="btnSubmitFeedback" type="button">Kirim Umpan Balik</button>
            </footer>
        </div>
    </div>


</div> <!-- Sekarang div penutup ini punya pasangannya -->
<script src="{{ asset('js/pencarian.js') }}"></script>
<script src="{{ asset('js/user/profile-popup.js') }}"></script>

@endsection

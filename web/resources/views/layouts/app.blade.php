<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/Lensa-saja.png') }}">
    <title>Lensa Hoax - @yield('title', 'Pencarian')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Istok+Web:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Iconify (untuk ikon) -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/user/pencarian.css') }}">

    <style>
        .flash-message {
            position: fixed;
            top: 18px;
            left: 50%;
            z-index: 9999;
            min-width: 360px;
            max-width: min(560px, calc(100vw - 32px));
            padding: 18px 20px;
            border-radius: 16px;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.2);
            border: 1px solid transparent;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            line-height: 1.45;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        .flash-message--success {
            background: #ecfdf3;
            color: #166534;
            border-color: #86efac;
        }

        .flash-message--error {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        .flash-message__icon {
            flex: 0 0 auto;
            margin-top: 1px;
            font-size: 18px;
            line-height: 1;
        }

        .flash-message__content {
            flex: 1;
            word-break: break-word;
        }

        .flash-message.is-hiding {
            opacity: 0;
            transform: translateX(-50%) translateY(-8px);
        }

        @media (max-width: 640px) {
            .flash-message {
                top: 16px;
                left: 16px;
                right: 16px;
                min-width: 0;
                max-width: none;
                transform: translateY(0);
            }

            .flash-message.is-hiding {
                transform: translateY(-8px);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @if (session('success') || session('error') || $errors->any())
        <div id="flash-message"
             class="flash-message {{ session('success') ? 'flash-message--success' : 'flash-message--error' }}"
             role="alert"
             aria-live="polite">
            <span class="flash-message__icon">
                @if (session('success'))
                    <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                @else
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                @endif
            </span>
            <div class="flash-message__content">
                @if (session('success'))
                    {{ session('success') }}
                @elseif (session('error'))
                    {{ session('error') }}
                @else
                    {{ $errors->first() }}
                @endif
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
    @include('layouts.footer')

    <script>
        (function () {
            const flashMessage = document.getElementById('flash-message');

            if (!flashMessage) {
                return;
            }

            window.setTimeout(() => {
                flashMessage.classList.add('is-hiding');

                window.setTimeout(() => {
                    flashMessage.remove();
                }, 350);
            }, 5000);
        })();
    </script>
</body>
</html>

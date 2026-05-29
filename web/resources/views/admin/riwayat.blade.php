@extends('layouts.admin')

@section('title', 'Riwayat Global')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/riwayat-style.css') }}">
@endpush

@section('content')
    <!-- HEADER -->
    <div class="page-header">

        <h1>Riwayat Global</h1>

        <div class="page-header-right">

            <div class="search-wrapper">

                <form action="{{ url()->current() }}" method="GET" class="search-wrapper">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="search-input" id="searchInput">
                    <button type="submit" class="search-btn">
                        <i class="fa fa-search"></i>
                    </button>

                </form>

            </div>

            <button class="btn-export" onclick="window.location.href='{{ route('riwayat.unduh_csv') }}'">
                <i class="fa fa-download"></i>
                Export CSV
            </button>

        </div>

    </div>

    <!-- GRID -->
    <div class="riwayat-grid">
        @foreach ($data as $item)
            <div class="riwayat-card searchable-card {{ $item['gambar'] ? 'image-card' : '' }}"
                data-title="{{ strtolower($item['judul']) }}" data-id="{{ $loop->index }}"
                data-request-id="{{ $item['request_id'] }}" data-deleted="{{ $item['is_deleted'] ? 'true' : 'false' }}"
                data-deleted-date="{{ $item['deleted_at'] ?? '' }}">

                {{-- JIKA ADA GAMBAR --}}
                @if ($item['gambar'])
                    <img src="{{ asset($item['gambar']) }}" class="card-img">

                    {{-- JIKA TIDAK ADA GAMBAR --}}
                @else
                    <div class="card-header">

                        <div class="warning-title">

                            <i class="fa fa-exclamation-triangle warning-icon"></i>

                            {{ $item['judul'] }}

                            <i class="fa fa-exclamation-triangle warning-icon"></i>

                        </div>

                        <p class="desc">
                            {{ $item['deskripsi'] }}
                        </p>

                    </div>
                @endif

                <!-- GARIS -->
                <div class="divider"></div>

                <!-- BOTTOM -->
                <div class="card-bottom">

                    <!-- PROGRESS -->
                    <div class="progress-circle">

                        <svg viewBox="0 0 120 60">

                            <!-- background -->
                            <path d="M10 60 A50 50 0 0 1 110 60" class="bg" />

                            <!-- merah -->
                            <path d="M10 60 A50 50 0 0 1 110 60" class="progress-red" />

                            <!-- hijau -->
                            <path d="M10 60 A50 50 0 0 1 110 60" class="progress-green" />

                        </svg>

                        <span>{{ $item['hoax'] }}%</span>

                    </div>

                    <!-- LEGEND -->
                    <div class="legend">

                        <p>
                            <span class="dot red"></span>
                            Data terdeteksi hoax sebesar {{ $item['hoax'] }}%
                        </p>

                        <p>
                            <span class="dot green"></span>
                            Data terdeteksi benar sebesar {{ $item['benar'] }}%
                        </p>

                    </div>

                    <!-- BUTTON -->
                    <button class="btn-detail open-popup" data-judul="{{ $item['judul'] }}"
                        data-deskripsi="{{ $item['deskripsi'] }}" data-penjelasan="{{ $item['penjelasan'] }}"
                        data-hoax="{{ $item['hoax'] }}" data-benar="{{ $item['benar'] }}" data-user="{{ $item['user'] }}"
                        data-date="{{ $item['date'] }}">
                        Selengkapnya
                    </button>

                </div>

            </div>
        @endforeach

    </div>

    <!-- POPUP -->
    <div class="popup-overlay" id="popupOverlay">

        <div class="popup-box">

            <!-- ACTION BUTTON -->
            <div class="popup-actions">

                <!-- DELETE -->
                <button id="deletePopup">
                    <i class="fa fa-trash"></i>
                </button>

                <button id="permanentDeletePopup" style="display:none;">
                    <i class="fa fa-trash-can"></i>
                </button>

                <button id="closePopup">
                    <i class="fa fa-times"></i>
                </button>

            </div>

            <!-- HEADER -->
            <div class="popup-top">

                <p class="popup-user" id="popupUser"></p>

                <p class="popup-date" id="popupDate"></p>

                <div id="popupDeleteStatus" class="popup-delete-status popup-tag">
                </div>

            </div>

            <!-- CONTENT -->
            <div class="popup-content">

                <div class="popup-title" id="popupTitle">
                    Judul
                </div>

                <p class="popup-desc" id="popupDesc">
                    Isi berita
                </p>

            </div>

            <!-- LINE -->
            <div class="popup-divider"></div>

            <!-- BOTTOM -->
            <div class="popup-bottom">

                <!-- LEFT -->
                <div class="popup-legend">

                    <p>
                        <span class="dot red"></span>
                        Data terdeteksi hoax sebesar
                        <strong id="popupHoax">70%</strong>
                    </p>

                    <p>
                        <span class="dot green"></span>
                        Data terdeteksi benar sebesar
                        <strong id="popupBenar">30%</strong>
                    </p>

                </div>

                <!-- RIGHT -->
                <div class="popup-result">

                    <p class="popup-penjelasan" id="popupPenjelasan"></p>

                </div>

            </div>

        </div>

    </div>

    <!-- DELETE POPUP -->
    <div class="permanent-overlay" id="permanentOverlay">

        <div class="permanent-popup">

            <h2>Hapus Riwayat</h2>

            <p>
                Apakah anda yakin ingin menghapus
                riwayat ini?
            </p>

            <div class="permanent-actions">

                <button id="confirmPermanentDelete" class="btn-permanent-delete">
                    Hapus
                </button>

                <button id="cancelPermanentDelete" class="btn-permanent-cancel">
                    Batal
                </button>

            </div>

        </div>

    </div>

    <!-- SEARCH JS -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {

            let keyword = this.value.toLowerCase();

            let cards = document.querySelectorAll('.searchable-card');

            cards.forEach(card => {

                let text = card.innerText.toLowerCase();

                if (text.includes(keyword)) {

                    card.style.display = 'block';

                } else {

                    card.style.display = 'none';

                }

            });

        });
    </script>

    <script>
        const popupOverlay = document.getElementById('popupOverlay');
        const closePopup = document.getElementById('closePopup');

        const deletePopup = document.getElementById('deletePopup');
        const permanentDeletePopup = document.getElementById('permanentDeletePopup');

        const permanentOverlay =
            document.getElementById('permanentOverlay');

        const confirmPermanentDelete =
            document.getElementById('confirmPermanentDelete');

        const cancelPermanentDelete =
            document.getElementById('cancelPermanentDelete');

        let currentCard = null;

        const popupTitle = document.getElementById('popupTitle');
        const popupDesc = document.getElementById('popupDesc');
        const popupHoax = document.getElementById('popupHoax');
        const popupBenar = document.getElementById('popupBenar');
        const popupDeleteStatus =
            document.getElementById('popupDeleteStatus');

        const popupUser =
            document.getElementById('popupUser');

        const popupDate =
            document.getElementById('popupDate');

        const popupPenjelasan =
            document.getElementById('popupPenjelasan');


        // BUKA DETAIL
        document.querySelectorAll('.open-popup')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    function() {

                        currentCard =
                            this.closest('.riwayat-card');

                        popupTitle.innerHTML =
                            `⚠️ ${this.dataset.judul} ⚠️`;

                        popupDesc.innerText =
                            this.dataset.deskripsi;

                        popupPenjelasan.innerText =
                            this.dataset.penjelasan;

                        popupUser.innerText =
                            this.dataset.user;

                        popupDate.innerText =
                            this.dataset.date;

                        popupHoax.innerText =
                            this.dataset.hoax + '%';

                        popupBenar.innerText =
                            this.dataset.benar + '%';


                        // STATUS DELETE
                        if (currentCard.dataset.deleted === "true") {

                            popupDeleteStatus.style.display = 'flex';

                            const deletedDate =
                                new Date(
                                    currentCard.dataset.deletedDate
                                );

                            popupDeleteStatus.textContent =
                                `Dihapus • ${
deletedDate.toLocaleDateString(
'id-ID',
{
day:'numeric',
month:'long',
year:'numeric'
}
)
}`;

                            deletePopup.innerHTML =
                                '<i class="fa fa-undo"></i>';

                            permanentDeletePopup.style.display =
                                'block';

                        } else {

                            popupDeleteStatus.style.display =
                                'none';

                            deletePopup.innerHTML =
                                '<i class="fa fa-trash"></i>';

                            permanentDeletePopup.style.display =
                                'none';

                        }

                        popupOverlay.classList.add(
                            'active'
                        );

                    });

            });


        // TUTUP POPUP
        closePopup.addEventListener(
            'click',
            () => popupOverlay.classList.remove(
                'active'
            )
        );

        popupOverlay.addEventListener(
            'click',
            function(e) {

                if (e.target === popupOverlay) {

                    popupOverlay.classList.remove(
                        'active'
                    );

                }

            });


        // HAPUS / RESTORE
        deletePopup.addEventListener(
            'click',
            function() {

                if (!currentCard) return;

                const requestId =
                    currentCard.dataset.requestId;


                // RESTORE
                if (
                    currentCard.dataset.deleted === "true"
                ) {

                    fetch(
                            `/admin/history-management/restore/${requestId}`, {

                                method: 'POST',

                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]'
                                    ).content
                                }

                            }
                        )
                        .then(r => r.json())
                        .then(() => {

                            location.reload();

                        });

                    return;

                }


                // HAPUS PERTAMA
                permanentOverlay.classList.add(
                    'active'
                );

            });



        // BATAL HAPUS
        cancelPermanentDelete
            .addEventListener(
                'click',
                function() {

                    permanentOverlay.classList.remove(
                        'active'
                    );

                });



        // KONFIRMASI HAPUS
       confirmPermanentDelete.addEventListener('click', function () {

    if (!currentCard) return;

    const requestId = currentCard.dataset.requestId;

    let url = `/admin/history-management/soft-delete/${requestId}`;
    let method = 'POST';

    // JIKA SUDAH DIHAPUS
    if (currentCard.dataset.deleted === "true") {
        url = `/admin/history-management/hard-delete/${requestId}`;
        method = 'DELETE';
    }

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector(
                'meta[name="csrf-token"]'
            ).content,
            'Accept': 'application/json'
        }
    })
    .then(async (response) => {

        console.log('status:', response.status);

        const text = await response.text();
        console.log('response:', text);

        return text ? JSON.parse(text) : {};
    })
    .then((data) => {

        console.log('success:', data);

        location.reload();
    })
    .catch((err) => {
        console.error('fetch error:', err);
    });

});
       
        // BUKA HAPUS PERMANEN
        permanentDeletePopup
            .addEventListener(
                'click',
                function() {

                    permanentOverlay.classList.add(
                        'active'
                    );

                });
    </script>

@endsection

@extends('layouts.admin')

@section('title', 'Data Pengguna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/components.css') }}">
@endpush

@section('content')

<div class="page-header-top">

    <div class="page-title-box">
        <h1>Data Pengguna</h1>
        <p>Kelola dan tinjau profil pengguna yang terdaftar di platform Lensa Hoax.</p>
    </div>

    <form action="{{ url()->current() }}" method="GET" class="search-wrapper">
        <input 
            type="text"
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Search..."
            class="search-input"
            id="searchInput"
        >
        <button type="submit" class="search-btn">
            <i class="fa fa-search"></i>
        </button>
    </form>
</div>

<div class="user-container">

    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th><i class="fa fa-user"></i></th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Whatsapp</th>
                </tr>
            </thead>

            <tbody id="userTableBody">
                @forelse($users as $index => $user)
                <tr>
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td>{{ $user['nama'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['whatsapp'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                        Tidak ada data pengguna yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        @if ($users->lastPage() > 1)

            {{-- Tombol Previous --}}
            @if($users->currentPage() > 1)
                <a href="{{ $users->previousPageUrl() }}">Previous</a>
            @else
                <a class="disabled">Previous</a>
            @endif

            {{-- Nomor Angka Halaman --}}
            @for ($i = 1; $i <= $users->lastPage(); $i++)
                <a href="{{ $users->url($i) }}"
                   class="page-number {{ $users->currentPage() == $i ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor

            {{-- Tombol Next --}}
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}">Next</a>
            @else
                <a class="disabled">Next</a>
            @endif

        @endif
    </div>

</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.querySelector('.search-wrapper');
    
    let debounceTimeout;

    // 1. Fungsi Utama untuk Mengambil Data dari Server via AJAX
    function fetchLiveData(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Mengubah string HTML menjadi objek DOM agar bisa diparsing
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Ambil konten tabel baru dan timpa tabel lama
            const newTableBody = doc.getElementById('userTableBody');
            const currentTableBody = document.getElementById('userTableBody');
            if (newTableBody && currentTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
            }

            // Ambil komponen pagination baru dan timpa pagination lama
            const newPagination = doc.querySelector('.pagination-wrapper');
            const currentPagination = document.querySelector('.pagination-wrapper');
            if (currentPagination) {
                currentPagination.innerHTML = newPagination ? newPagination.innerHTML : '';
            }
        })
        .catch(error => console.error('Terjadi kesalahan saat memuat data:', error));
    }

    // 2. Event Listener saat Pengguna Mengetik (Live Search)
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            const keyword = this.value;

            // Debounce selama 300ms agar server tidak overload menerima request setiap satu ketukan huruf
            debounceTimeout = setTimeout(() => {
                const url = new URL(window.location.origin + window.location.pathname);
                
                if (keyword) {
                    url.searchParams.set('search', keyword);
                }
                // Jika mencari kata baru, set ulang halaman kembali ke page 1
                url.searchParams.delete('page'); 

                // Perbarui URL di browser tanpa reload (bagus untuk bookmarking & user experience)
                window.history.pushState({}, '', url);

                // Jalankan fungsi fetch data
                fetchLiveData(url);
            }, 300);
        });
    }

    // 3. Mencegah Halaman Reload saat Tombol Enter atau Tombol Kaca Pembesar Diklik
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
        });
    }

    // 4. Interseptor Tombol Navigasi Pagination agar Tetap Berjalan secara AJAX (Tanpa Reload)
    document.addEventListener('click', function (e) {
        const targetAnchor = e.target.closest('.pagination-wrapper a');
        
        if (targetAnchor && !targetAnchor.classList.contains('disabled') && !targetAnchor.classList.contains('active')) {
            e.preventDefault(); // Matikan fungsi link bawaan browser
            
            const targetUrl = targetAnchor.getAttribute('href');
            
            // Perbarui URL browser dan ambil data halaman baru
            window.history.pushState({}, '', targetUrl);
            fetchLiveData(targetUrl);
        }
    });
});
</script>
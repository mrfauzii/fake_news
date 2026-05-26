@extends('layouts.admin')

@section('title', 'Data Pengguna')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-style.css') }}">
@endpush

@section('content')

<!-- HEADER -->
<div class="page-header">

    <h1>Data Pengguna</h1>

    <div class="search-wrapper">

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

</div>

<p class="page-subtitle">
    Kelola dan tinjau profil pengguna yang terdaftar di platform Lensa Hoax.
</p>

<!-- TABLE -->
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

                @foreach($users as $index => $user)

                <tr>

                    <td>{{ $users->firstItem() + $index }}</td>

                    <td>{{ $user['nama'] }}</td>

                    <td>{{ $user['email'] }}</td>

                    <td>{{ $user['whatsapp'] }}</td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <div class="pagination-wrapper">

        @if ($users->lastPage() > 1)

            {{-- Previous --}}
            @if($users->currentPage() > 1)

                <a href="{{ $users->previousPageUrl() }}">
                    Previous
                </a>

            @else

                <a class="disabled">
                    Previous
                </a>

            @endif


            {{-- Nomor halaman --}}
            @for ($i = 1; $i <= $users->lastPage(); $i++)

                <a href="{{ $users->url($i) }}"
                class="page-number {{ $users->currentPage() == $i ? 'active' : '' }}">

                    {{ $i }}

                </a>

            @endfor


            {{-- Next --}}
            @if($users->hasMorePages())

                <a href="{{ $users->nextPageUrl() }}">
                    Next
                </a>

            @else

                <a class="disabled">
                    Next
                </a>

            @endif

        @endif

    </div>

</div>

<!-- SEARCH JS -->
<script>

const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('keyup', function () {

    const keyword = searchInput.value.toLowerCase();

    const rows = document.querySelectorAll('#userTableBody tr');

    rows.forEach(function(row) {

        const rowText = row.textContent.toLowerCase();

        if (rowText.includes(keyword)) {

            row.style.display = '';

        } else {

            row.style.display = 'none';

        }

    });

});

</script>

@endsection
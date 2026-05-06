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
        <input type="text" placeholder="Search..." class="search-input">
        <button class="search-btn">
            <i class="fa fa-search"></i>
        </button>
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
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Budi Pratomo</td>
                    <td>budiprtmo34@gmail.com</td>
                    <td>085876542319</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Siti Hartini</td>
                    <td>sitih4rtini@gmail.com</td>
                    <td>085476549315</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Andi Santoso</td>
                    <td>sansand@gmail.com</td>
                    <td>085476549318</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
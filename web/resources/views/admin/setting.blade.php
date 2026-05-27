@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/setting-style.css') }}">
@endpush

@section('content')

<div class="setting-page">

    <!-- HEADER -->
    <div class="setting-header">

        <h1>Setting Dashboard</h1>

        <p>
            Atur jadwal pembaruan knowledge base agar informasi dan data sistem tetap terbaru.
        </p>

    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
    <div
        class="success-alert"
        id="successAlert"
    >
       <i class="fa fa-circle-check"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- CARD -->
    <div class="setting-box">

        <form action="/admin/setting/save" method="POST">
            @csrf

            <div class="input-group-setting">

                <label>Jam Pembaruan Knowledge Base</label>

                <input
                    type="time"
                    name="knowledge_base_update_time"
                    class="time-input"
                    value="{{ session('knowledge_base_update_time') }}"
                >

            </div>

            <button
                type="submit"
                class="save-btn"
            >
                Simpan Jadwal
            </button>

        </form>

    </div>

</div>

@endsection

<script>

setTimeout(() => {

    const alert =
    document.getElementById(
        'successAlert'
    );

    if(alert){

        alert.style.transition =
        '0.5s';

        alert.style.opacity = '0';

        setTimeout(() => {

            alert.remove();

        }, 500);

    }

}, 5000);

</script>
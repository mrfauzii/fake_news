@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/setting-style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

<div class="setting-page">

    <div class="setting-header">
        <h1>Setting Dashboard</h1>
        <p>Atur jadwal pembaruan knowledge base agar informasi dan data sistem tetap terbaru.</p>
    </div>

    {{-- ALERT SUCCESS --}}
   @if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showNotif("{{ session('success') }}", 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showNotif("{{ session('error') }}", 'error');
    });
</script>
@endif
<div id="notif" class="notif hidden"></div>
    <div class="setting-box">
        <form action="{{ route("admin.setting.schedule-scrape") }}" method="POST">
            @csrf

            <div class="input-group-setting">
                <label>Jam Pembaruan Knowledge Base</label>
                <input
                    type="time"
                    name="jam"
                    class="time-input"
                    value="{{ $time }}"
                >
            </div>

            <div class="setting-actions">
                <button type="submit" class="save-btn">
                    Simpan Jadwal
                </button>

                <button type="button" id="btnUpdateNow" class="sync-btn">
                    <i class="fas fa-sync-alt" id="iconSync"></i> Perbarui Sekarang
                </button>
            </div>
        </form> 
    </div>

</div>

@endsection

<script>
// Menghilangkan Alert Sukses setelah 5 detik
setTimeout(() => {
    const alert = document.getElementById('successAlert');
    if(alert){
        alert.style.transition = '0.5s';
        alert.style.opacity = '0';
        setTimeout(() => { alert.remove(); }, 500);
    }
}, 5000);

// Eksekusi Pembaruan Instan via AJAX Fetch
document.addEventListener('DOMContentLoaded', function() {
    const btnUpdateNow = document.getElementById('btnUpdateNow');
    
    if(btnUpdateNow) {
        btnUpdateNow.addEventListener('click', function() {
            const icon = document.getElementById('iconSync');
            
            // Aktifkan status loading tombol secara instan
            btnUpdateNow.disabled = true;
            btnUpdateNow.style.opacity = '0.7';
            icon.classList.add('fa-spin');
            
            fetch('{{ route("admin.setting.scrape.now") }}', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})
.then(async response => {

    const text = await response.text();

    try {
        return JSON.parse(text);
    } catch (e) {
        throw new Error('Server bukan JSON: ' + text);
    }

})
.then(result => {

    if (result.status === 'success') {
        showNotif(result.message, 'success');
    } else {
        showNotif(result.message, 'error');
    }

})
.catch(error => {
    showNotif('Terjadi kesalahan server', 'error');
    console.error(error);
})
.finally(() => {
    btnUpdateNow.disabled = false;
    btnUpdateNow.style.opacity = '1';
    icon.classList.remove('fa-spin');
});
        });
    }
});

function showNotif(message, type = 'success') {
    const notif = document.getElementById('notif');

    notif.className = `notif ${type}`;
    notif.textContent = message;

    notif.classList.remove('hidden');

    setTimeout(() => {
        notif.classList.add('hidden');
    }, 4000);
}
</script>
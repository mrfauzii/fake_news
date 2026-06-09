<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache; // TAMBAHAN: Import Cache untuk simpan data tanpa tabel database
use Carbon\Carbon;
use App\Models\Users;
use App\Models\Requests;
use App\Models\history_view;
use App\Models\ScrapeSchedule;
use App\Models\Feedbacks;

class AdminController extends Controller
{
    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PENCARIAN POPULER
        |--------------------------------------------------------------------------
        */
        $histories = DB::table('user_interactions as ui')
        ->join('requests as r', 'ui.request_id', '=', 'r.id')
        ->select(
            'r.input_text',
            'r.final_label',
            'r.final_confidence',
            DB::raw('COUNT(*) as count')
        )
        ->whereNotNull('r.final_label')
        // Filter bulan & tahun berjalan
        ->whereMonth('ui.created_at', date('n'))
        ->whereYear('ui.created_at', date('Y'))
        ->groupBy('r.input_text', 'r.final_label', 'r.final_confidence')
        ->orderByDesc('count')
        ->limit(3)
        ->get();

    $dashboardPopular = [];

    foreach ($histories as $idx => $row) {
        $category = (strtolower($row->final_label) === 'fake' || strtolower($row->final_label) === 'hoax') 
            ? 'hoax' 
            : 'fakta';
            
        $dashboardPopular[] = [
            'rank'       => $idx + 1,
            'category'   => $category,
            'input'      => $row->input_text,
            'badge'      => strtoupper($category),
            'excerpt'    => $row->input_text,                 // Judul utuh
            'confidence' => $row->final_confidence,
            'count'      => $row->count,
        ];
    }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA STATISTIK DASHBOARD
        |--------------------------------------------------------------------------
        */
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // 1. STATISTIK PENGGUNA
        $total_pengguna = Users::count();
        $pengguna_sekarang = Users::whereMonth('created_at', $now->month)
                                  ->whereYear('created_at', $now->year)->count();
        $pengguna_lalu = Users::whereMonth('created_at', $lastMonth->month)
                              ->whereYear('created_at', $lastMonth->year)->count();

        // 2. STATISTIK BERITA (Diambil dari tabel requests)
        $total_berita = Requests::count();
        $berita_sekarang = Requests::whereMonth('created_at', $now->month)
                                   ->whereYear('created_at', $now->year)->count();
        $berita_lalu = Requests::whereMonth('created_at', $lastMonth->month)
                               ->whereYear('created_at', $lastMonth->year)->count();

        // 3. STATISTIK HOAX (Cari yang labelnya 'hoax' atau 'fake')
        $total_hoax = history_view::whereIn('final_label', ['hoax', 'fake'])->count();
        $hoax_sekarang = history_view::whereIn('final_label', ['hoax', 'fake'])
                                     ->whereMonth('created_at', $now->month)
                                     ->whereYear('created_at', $now->year)->count();
        $hoax_lalu = history_view::whereIn('final_label', ['hoax', 'fake'])
                                 ->whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)->count();

        // 4. STATISTIK UMPAN BALIK
        $total_umpan_balik = Feedbacks::count();
        $umpan_balik_sekarang = Feedbacks::whereMonth('created_at', $now->month)
                                         ->whereYear('created_at', $now->year)->count();
        $umpan_balik_lalu = Feedbacks::whereMonth('created_at', $lastMonth->month)
                                     ->whereYear('created_at', $lastMonth->year)->count();

        // 5. GABUNGKAN KE VARIABLE $dashboardStats
        $dashboardStats = [
            'total_pengguna'     => $total_pengguna,
            'persen_pengguna'    => $this->hitungPersentase($pengguna_sekarang, $pengguna_lalu),
            'total_berita'       => $total_berita,
            'persen_berita'      => $this->hitungPersentase($berita_sekarang, $berita_lalu),
            'total_hoax'         => $total_hoax,
            'persen_hoax'        => $this->hitungPersentase($hoax_sekarang, $hoax_lalu),
            'total_umpan_balik'  => $total_umpan_balik,
            'persen_umpan_balik' => $this->hitungPersentase($umpan_balik_sekarang, $umpan_balik_lalu),
            'last_updated'       => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $lastDate = DB::table('knowledge_base')
            ->max('created_at');

        $lastDate = Carbon::parse($lastDate)
            ->translatedFormat('l, d F Y H:i');
            return view('admin.dashboard', compact('dashboardPopular', 'dashboardStats','lastDate'));
        }

    /**
     * Fungsi Helper untuk menghitung persentase kenaikan/penurunan bulan ini vs bulan lalu
     */
    private function hitungPersentase($bulanIni, $bulanLalu)
    {
        if ($bulanLalu == 0) {
            return $bulanIni > 0 ? 100 : 0;
        }

        $persen = (($bulanIni - $bulanLalu) / $bulanLalu) * 100;
        return round($persen, 1);
    }

    public function pencarian(Request $request)
    {
        return view('admin.pencarian');
    }

    // SETTING
    public function setting()
    {
        $time = ScrapeSchedule::find(1)->scheduled_at ?? '01:00';
        return view('admin.setting', compact('time'));
    }

    // // Menyimpan jadwal pembaruan manual dari tombol "Simpan Jadwal"
    // public function saveSetting(Request $request)
    // {
    //     // Mengubah session menjadi Cache permanen
    //     Cache::forever('knowledge_base_update_time', $request->knowledge_base_update_time);

    //     return back()->with(
    //         'success',
    //         'Jadwal pembaruan knowledge base berhasil diperbarui'
    //     );
    // }

    /* --- FUNGSI UPDATE NOW MENGGUNAKAN CACHE PERMANEN (DIPERBAIKI) --- */
    // public function updateNow()
    // {
    //     try {
    //         $currentRealTime = Carbon::now()->format('H:i');
            
    //         // 1. Simpan jam real-time baru ke Cache secara permanen
    //         Cache::forever('knowledge_base_update_time', $currentRealTime);
            
    //         // 2. Membuat alert hijau muncul setelah halaman di-reload otomatis oleh JS
    //         session()->flash('success', 'Jadwal pembaruan knowledge base berhasil diperbarui secara instan!');

    //         // 3. Kembalikan sinyal sukses ke JavaScript Fetch
    //         return response()->json([
    //             'status' => 'success'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}

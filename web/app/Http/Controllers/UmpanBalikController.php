<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmpanBalikController extends Controller
{
    public function index(Request $request)
    {
        // Set locale Carbon ke Indonesia agar format hari & bulan otomatis berbahasa Indonesia
        Carbon::setLocale('id');

        // 1. Ambil kata kunci pencarian dari URL (?search=...)
        $search = $request->input('search');

        // 2. Hitung total feedback keseluruhan di database untuk mengisi komponen Stats Card
        $totalFeedback = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            ->count();

        // 3. Siapkan query dasar untuk mengambil list data umpan balik
        $query = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            ->leftJoin('requests', 'feedbacks.request_id', '=', 'requests.id')
            ->select(
                'feedbacks.id',
                'users.name as username',
                'feedbacks.feedback',
                'feedbacks.created_at',
                'requests.input_text as link',
                'requests.final_label'
            );

        // 4. LOGIKA PENCARIAN GLOBAL: Jika kolom search diisi, saring database di semua halaman
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('feedbacks.feedback', 'like', "%{$search}%");
            });
        }

        // 5. Batasi 10 data per halaman & kunci parameter pencarian di URL saat klik pindah page
        $feedbacks = $query->orderBy('feedbacks.created_at', 'desc')
            ->paginate(1)
            
            ->withQueryString();

        // 6. Kirim seluruh variabel ke view admin.umpanbalik
        return view('admin.umpanbalik', compact('totalFeedback', 'feedbacks'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UmpanBalikController extends Controller
{
    public function index(Request $request)
{
    Carbon::setLocale('id');
    $search = $request->input('search');

    // Query builder dasar
    $query = DB::table('feedbacks')
        ->join('users', 'feedbacks.user_id', '=', 'users.id')
        ->leftJoin('requests', 'feedbacks.request_id', '=', 'requests.id')
        ->leftJoin('images', 'requests.image_id', '=', 'images.id')
        ->select(
            'feedbacks.id',
            'users.name as username',
            'feedbacks.feedback',
            'feedbacks.created_at',
            'requests.input_text as input_text',
            'requests.final_label',
            'images.file_path as images'
        );

    // LOGIKA PENCARIAN (Cari di nama user atau isi feedback)
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('users.name', 'like', '%' . $search . '%')
              ->orWhere('feedbacks.feedback', 'like', '%' . $search . '%');
        });
    }

    // Paginate hasil query
    $feedbacks = $query->orderBy('feedbacks.created_at', 'desc')
                       ->paginate(1) 
                       ->withQueryString();

    // Hitung total untuk keperluan statistik
    $totalFeedback = DB::table('feedbacks')->count();

    // Jika request adalah AJAX, kembalikan view partial atau full view tergantung kebutuhan
    // Di sini kita return view biasa karena AJAX Anda mengambil innerHTML dari view yang sama
    return view('admin.umpanbalik', compact('totalFeedback', 'feedbacks'));
}
}
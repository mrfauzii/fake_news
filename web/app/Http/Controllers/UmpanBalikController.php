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
            
             // TAMBAHAN
            ->leftJoin(
                'requests',
                'feedbacks.request_id',
                '=',
                'requests.id'
            )
            ->leftJoin(
                'images',
                'requests.image_id',
                '=',
                'images.id'
            )

            ->select(
                'feedbacks.id',
                'users.name as username',
                'feedbacks.feedback',
                'feedbacks.created_at',
                'requests.input_text as input_text',
                'requests.final_label',
                'images.file_path as images'
            )
            ->orderBy('feedbacks.created_at', 'desc')
            ->get()
            ->map(function($item){

                return [
                    'id' => $item->id,
                    'username' => $item->username,
                    'feedback' => $item->feedback,
                    'input_text' => $item->input_text,
                    'result' => ucfirst($item->final_label ?? '-'),
                    'images' => $item->images,
                    'date' => Carbon::parse(
                        $item->created_at
                    )->translatedFormat(
                        'l, j F Y'
                    ),

                ];
            });
        

        // 5. Batasi 10 data per halaman & kunci parameter pencarian di URL saat klik pindah page
        $feedbacks = DB::table('feedbacks')
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
    )
    ->orderBy('feedbacks.created_at', 'desc')
    ->paginate(25)
    ->withQueryString();

        // 6. Kirim seluruh variabel ke view admin.umpanbalik
        return view('admin.umpanbalik', compact('totalFeedback', 'feedbacks'));
    }
}
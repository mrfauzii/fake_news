<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmpanBalikController extends Controller
{
    public function index()
    {
        // ambil total sesuai data yang benar-benar tampil
        $totalFeedback = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            ->count();

        /*
        // sementara dinonaktifkan
        $belumDibaca = $totalFeedback;
        */

        return view(
            'admin.umpanbalik',
            compact(
                'totalFeedback'
            )
        );
    }

    public function getFeedbackData()
    {
        Carbon::setLocale('id');

        $feedbacks = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            
             // TAMBAHAN
            ->leftJoin(
                'requests',
                'feedbacks.request_id',
                '=',
                'requests.id'
            )

            ->select(
                'feedbacks.id',
                'users.name as username',
                'feedbacks.feedback',
                'feedbacks.created_at',
                'requests.input_text as link',
                'requests.final_label'
            )
            ->orderBy('feedbacks.created_at', 'desc')
            ->get()
            ->map(function($item){

                return [
                    'id' => $item->id,
                    'username' => $item->username,
                    'feedback' => $item->feedback,
                    'link' => $item->link ?? '-',
                    'result' => ucfirst($item->final_label ?? '-'),
                    'date' => Carbon::parse(
                        $item->created_at
                    )->translatedFormat(
                        'l, j F Y'
                    ),

                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Data umpan balik berhasil dimuat.',
            'data' => $feedbacks
        ]);
    }
}
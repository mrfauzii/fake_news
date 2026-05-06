<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UmpanBalikController extends Controller
{
     public function index()
    {
        return view('admin.umpanbalik');
    }

    public function getFeedbackData()
    {
        $feedbacks = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            ->select(
                'feedbacks.id', 
                'users.name as username', 
                'feedbacks.request_id', 
                'feedbacks.feedback', 
                'feedbacks.created_at'
            )
            ->orderBy('feedbacks.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data umpan balik berhasil dimuat.',
            'data' => $feedbacks
        ]);
    }
}

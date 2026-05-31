<?php

namespace App\Http\Controllers;

use App\Models\ScrapeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function detectHoax(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Endpoint detect-hoax siap digunakan.',
            'input' => $request->all(),
        ]);
    }

    public function setScrapeSchedule(Request $request)
    {
        // Validasi inputan form format jam
        $request->validate([
            'jam' => 'required|date_format:H:i' 
        ]);

        \App\Models\ScrapeSchedule::updateOrCreate(
            ['id' => 1],
            ['scheduled_at' => $request->jam]
        );

        return redirect()->back()->with('success', 'Jadwal berhasil disimpan');
    }
}

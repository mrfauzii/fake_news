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
    // Validasi format waktu (contoh: "2026-05-20 14:30:00")
    $request->validate([
        'scheduled_time' => 'required|date'
    ]);

    // Simpan ke database
    ScrapeSchedule::create([
        'scheduled_at' => $request->scheduled_time,
        'status' => 'pending'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Jadwal scrape berhasil disimpan.'
    ]);
}
}

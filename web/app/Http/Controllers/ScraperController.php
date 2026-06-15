<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScraperController extends Controller
{
public function triggerScraper()
{
    $lock = Cache::lock('global-scraper-lock', 3600);

    if (!$lock->get()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Pembaruan knowledge base masih berjalan'
        ], 429);
    }

    $token = Str::random(40);
    Cache::put("scraper_token_$token", true, now()->addDay());

    try {
    Http::timeout(5)->post(env('AI_API_URL') . '/scrape', [
            'token' => $token,
        ]);

        Log::info('Pembaruan knowledge base berhasil ditrigger');

    } catch (\Exception $e) {
        Log::warning('Trigger scraper gagal tapi diabaikan: ' . $e->getMessage());
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Pembaruan knowledge base dimulai'
    ]);
}

public function markDone($token)
{
    

    // 1. validasi token
    if (!$token || !Cache::has("scraper_token_$token")) {
        return response()->json(['error' => 'unauthorized'], 403);
    }

    // 2. hapus token biar sekali pakai (opsional tapi bagus)
    Cache::forget("scraper_token_$token");

    // 3. release lock
    Cache::lock('global-scraper-lock', 7200)->forceRelease();
    Log::info('Released global-scraper-lock scraping sukses');

    return response()->json([
        'status' => 'success'
    ]);
}
}
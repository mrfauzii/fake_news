<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScraperController extends Controller
{
    public function triggerScraper()
    {
        try {
            // Kasih timeout 60 detik biar nggak RTO
            $response = Http::timeout(60)->get('http://127.0.0.1:8004/scrape');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Scraper berhasil dieksekusi manual!',
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Scraper gagal merespon.',
                'details' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Koneksi ke server scraper terputus: ' . $e->getMessage()
            ], 500);
        }
    }
}
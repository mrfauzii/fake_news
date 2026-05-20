<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapeSchedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RunScraper extends Command
{
    // nama command
    protected $signature = 'scrape:run';
    protected $description = 'Menjalankan HTTP GET ke URL scrape sesuai jadwal';

    public function handle()
    {
        // 1. Cari data yang statusnya 'pending' dan waktunya udah lewat atau pas saat ini
        $schedules = ScrapeSchedule::where('status', 'pending')
                        ->where('scheduled_at', '<=', now())
                        ->get();

        foreach ($schedules as $schedule) {
            try {
                // 2. Tembak URL-nya mek!
                $response = Http::timeout(60)->get('http://127.0.0.1:8004/scrape');

                if ($response->successful()) {
                    // 3. Kalau sukses, ubah status jadi success
                    $schedule->update(['status' => 'success']);
                    Log::info("Scrape sukses untuk ID: " . $schedule->id);
                } else {
                    $schedule->update(['status' => 'failed']);
                    Log::error("Scrape gagal (Bukan 200 OK) untuk ID: " . $schedule->id);
                }

            } catch (\Exception $e) {
                // Kalau URL-nya mati atau error
                $schedule->update(['status' => 'failed']);
                Log::error("Scrape error untuk ID: " . $schedule->id . " - " . $e->getMessage());
            }
        }
    }
}
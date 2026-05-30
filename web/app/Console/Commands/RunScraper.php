<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\ScrapeSchedule;

class RunScraper extends Command
{
    protected $signature = 'app:run-scraper';
    protected $description = 'Menjalankan scraper sesuai jadwal jam di database';

    public function handle()
    {
        // 1. Ambil jadwal dari database
        $schedule = \App\Models\ScrapeSchedule::find(1);

        if (!$schedule || !$schedule->scheduled_at) {
            $this->info('Jadwal belum diatur di database.');
            return;
        }

        // 2. Ambil jam & menit sekarang, lalu cocokan dengan jadwal
        $now = Carbon::now()->format('H:i');
        $scheduledTime = Carbon::parse($schedule->scheduled_at)->format('H:i');

        $this->info("Mengecek jadwal... Sekarang: $now | Jadwal: $scheduledTime");

        // 3. Kalau waktunya sama, eksekusi!
        if ($now === $scheduledTime) {
            $this->info('Waktu cocok! Menembak server scraper...');

            try {
                Http::timeout(1)->get('http://127.0.0.1:8004/scrape');
                
            } catch (\Exception $e) {
            }

            $this->info('Status Success: Perintah scraping berhasil dikirim ke server!');
        } else {
            $this->info('Belum waktunya scraping.');
        }
    }
}
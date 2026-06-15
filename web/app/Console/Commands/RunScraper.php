<?php

namespace App\Console\Commands;

use App\Models\ScrapeSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RunScraper extends Command
{
    protected $signature = 'app:run-scraper';
    protected $description = 'Menjalankan scraper sesuai jadwal jam di database';

    public function handle()
    {
        $schedule = ScrapeSchedule::find(1);

        if (!$schedule || !$schedule->scheduled_at) {
            $this->info('Jadwal belum diatur');
            return 0;
        }

        $now = Carbon::now()->format('H:i');
        $scheduledTime = Carbon::parse($schedule->scheduled_at)->format('H:i');

        $this->info("Sekarang: $now | Jadwal: $scheduledTime");

        if ($now !== $scheduledTime) {
            $this->info('Belum waktunya scraping');
            return 0;
        }

        // LOCK supaya tidak double run
        $lock = Cache::lock('global-scraper-lock', 7200);

        if (!$lock->get()) {
            $this->info('Scraper masih berjalan, skip');
            return 0;
        }

        try {
            $this->info('Trigger scraper (NON-BLOCKING)...');

            // 🔥 FIRE AND FORGET (tidak nunggu hasil)
            $token = Str::random(40);
            Cache::put("scraper_token_$token", true, now()->addDay());

            $response = Http::timeout(5)->post(env('AI_API_URL') . '/scrape', [
                'token' => $token,
            ]);

            $this->info('Scraper berhasil dipicu');

            return 0;
        } catch (\Throwable $e) {
            $this->error('Error trigger: ' . $e->getMessage());
            return 1;
        } finally {
            // tetap release lock biar tidak nyangkut
            $lock->release();
        }
    }
}

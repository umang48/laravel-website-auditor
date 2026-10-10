<?php

namespace App\Jobs;

use App\Models\Audit;
use App\Models\Website;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PerformWebsiteAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Website $website,
        public Audit $audit
    ) {}

    public function handle(): void
    {
        // 1. Mark audit as processing
        $this->audit->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            // 2. Fetch the website using Laravel's HTTP client
            $start = microtime(true);
            $response = Http::timeout(10)->get($this->website->url);
            $responseTime = round((microtime(true) - $start) * 1000); // in milliseconds

            // 3. Create the initial Audit Page record
            $page = $this->audit->pages()->create([
                'url' => $this->website->url,
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'page_size' => strlen($response->body()),
            ]);

            // 4. (We will add the DOM parsing for SEO issues here later)

            // 5. Mark as completed
            $this->audit->update([
                'status' => 'completed',
                'score' => 100, // Dummy score for now
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Handle timeouts or DNS failures cleanly
            $this->audit->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
        }
    }
}

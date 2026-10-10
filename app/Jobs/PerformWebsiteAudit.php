<?php

namespace App\Jobs;

use App\Models\Audit;
use App\Models\Website;
use App\Services\AuditScoringService;
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

    // 2. Inject the service into the handle method
    public function handle(AuditScoringService $scoringService): void
    {
        $this->audit->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $start = microtime(true);
            $response = Http::timeout(10)->get($this->website->url);
            $responseTime = round((microtime(true) - $start) * 1000);

            $page = $this->audit->pages()->create([
                'url' => $this->website->url,
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'page_size' => strlen($response->body()),
            ]);

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($response->body() ?: '<html></html>');
            $xpath = new \DOMXPath($dom);
            libxml_clear_errors();

            $titleNode = $dom->getElementsByTagName('title')->item(0);
            $title = $titleNode ? trim($titleNode->nodeValue) : null;

            $metaDescNode = $xpath->query('//meta[translate(@name, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="description"]/@content')->item(0);
            $metaDescription = $metaDescNode ? trim($metaDescNode->nodeValue) : null;

            $h1Node = $dom->getElementsByTagName('h1')->item(0);
            $h1 = $h1Node ? trim($h1Node->nodeValue) : null;

            $page->update([
                'title' => $title ? substr($title, 0, 255) : null,
                'meta_description' => $metaDescription,
            ]);

            $issues = [];

            if (!$title) {
                $issues[] = [
                    'audit_id' => $this->audit->id,
                    'type' => 'seo',
                    'severity' => 'error',
                    'message' => 'Missing Title Tag',
                    'recommendation' => 'Add a descriptive <title> tag to the head of your document.',
                ];
            }

            if (!$metaDescription) {
                $issues[] = [
                    'audit_id' => $this->audit->id,
                    'type' => 'seo',
                    'severity' => 'warning',
                    'message' => 'Missing Meta Description',
                    'recommendation' => 'Add a meta description to improve click-through rates from search engines.',
                ];
            }

            if (!$h1) {
                $issues[] = [
                    'audit_id' => $this->audit->id,
                    'type' => 'seo',
                    'severity' => 'warning',
                    'message' => 'Missing H1 Tag',
                    'recommendation' => 'Ensure the page has exactly one H1 tag summarizing the content.',
                ];
            }

            if (!empty($issues)) {
                $page->issues()->createMany($issues);
            }

            // 3. Use the injected service to calculate the score
            $finalScore = $scoringService->calculateScore($issues);

            $this->audit->update([
                'status' => 'completed',
                'score' => $finalScore,
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->audit->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
        }
    }
}

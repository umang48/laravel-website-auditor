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

            // 1. Suppress warnings for malformed HTML (very common on real websites)
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            // Load the HTML body, falling back to an empty string if null
            $dom->loadHTML($response->body() ?: '<html></html>');
            $xpath = new \DOMXPath($dom);
            libxml_clear_errors();

            // 2. Extract SEO elements
            $titleNode = $dom->getElementsByTagName('title')->item(0);
            $title = $titleNode ? trim($titleNode->nodeValue) : null;

            // XPath checks for 'description' regardless of uppercase/lowercase attributes
            $metaDescNode = $xpath->query('//meta[translate(@name, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="description"]/@content')->item(0);
            $metaDescription = $metaDescNode ? trim($metaDescNode->nodeValue) : null;

            $h1Node = $dom->getElementsByTagName('h1')->item(0);
            $h1 = $h1Node ? trim($h1Node->nodeValue) : null;

            // 3. Update the page record with extracted data
            $page->update([
                'title' => $title ? substr($title, 0, 255) : null,
                'meta_description' => $metaDescription,
            ]);

            // 4. Analyze and compile issues
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

            // 5. Save all issues to the database using Eloquent's createMany
            if (!empty($issues)) {
                $page->issues()->createMany($issues);
            }

            $this->audit->update([
                'status' => 'completed',
                'score' => empty($issues) ? 100 : (100 - (count($issues) * 10)), // Basic dynamic scoring
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

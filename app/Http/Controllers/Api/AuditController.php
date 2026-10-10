<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Jobs\PerformWebsiteAudit;
use App\Http\Resources\AuditResource;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function store(Website $website)
    {
        // 1. Create a pending audit in the database
        $audit = $website->audits()->create([
            'status' => 'pending',
        ]);

        // 2. Dispatch the job to the queue
        PerformWebsiteAudit::dispatch($website, $audit);

        // 3. Return the pending audit instantly so the frontend knows it started
        return response()->json([
            'message' => 'Audit queued successfully',
            'audit' => new AuditResource($audit)
        ], 202); // 202 Accepted is standard for background tasks
    }
}

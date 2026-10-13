<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Jobs\PerformWebsiteAudit;
use App\Http\Resources\AuditResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditController extends Controller
{
    public function store(Website $website)
    {
        // 2. Authorize using the WebsitePolicy's 'view' method
        Gate::authorize('view', $website);

        $audit = $website->audits()->create([
            'status' => 'pending',
        ]);

        PerformWebsiteAudit::dispatch($website, $audit);

        return response()->json([
            'message' => 'Audit queued successfully',
            'audit' => new AuditResource($audit)
        ], 202);
    }
}

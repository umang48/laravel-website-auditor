<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\User; // Add this
use App\Http\Requests\StoreWebsiteRequest; // Add this
use App\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only fetch websites belonging to the logged-in user
        $websites = $request->user()->websites()->latest()->paginate(10);
        
        return WebsiteResource::collection($websites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWebsiteRequest $request)
    {
       

        $validated = $request->validated();

        // Automatically assign the website to the authenticated user
        $website = $request->user()->websites()->create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'status' => 'active',
        ]);

        return new WebsiteResource($website);
    }

    /**
     * Display the specified resource.
     */
   public function show(Website $website)
    {
        // 2. Authorize the action before doing anything else
        Gate::authorize('view', $website);

        $website->load('audits');
        
        return new WebsiteResource($website);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

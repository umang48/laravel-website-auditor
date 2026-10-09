<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\User; // Add this
use App\Http\Requests\StoreWebsiteRequest; // Add this
use App\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $websites = Website::latest()->paginate(10);
        
        return WebsiteResource::collection($websites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWebsiteRequest $request)
    {
        // 1. Get only the validated data ('name' and 'url')
        $validated = $request->validated();

        // 2. Temporarily assign to the first user until we set up Sanctum Auth
        $user = User::first(); 
        
        // 3. Create the website through the relationship
        // This automatically sets the user_id on the website!
        $website = $user->websites()->create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'status' => 'active',
        ]);

        // 4. Return the new resource (Laravel automatically sets a 201 Created HTTP status)
        return new WebsiteResource($website);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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

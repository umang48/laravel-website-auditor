<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Website;

Route::get('/', function () {
    // For now, fetch all websites to prove it works.
    // Later we will attach this to a web Auth Controller.
    $websites = Website::latest()->get();

    return Inertia::render('Dashboard', [
        'websites' => $websites
    ]);
});
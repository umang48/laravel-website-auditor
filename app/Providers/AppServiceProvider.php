<?php

namespace App\Providers;



use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

use App\Events\AuditCompleted; // 2. Add this
use App\Listeners\SendAuditCompletedEmail; // 3. Add this

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
 
        Event::listen(
            AuditCompleted::class,
            SendAuditCompletedEmail::class,
        );
    }
}

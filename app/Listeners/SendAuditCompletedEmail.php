<?php

namespace App\Listeners;

use App\Events\AuditCompleted;
use App\Notifications\AuditCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAuditCompletedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AuditCompleted $event): void
    {
        // Navigate through Eloquent relationships to find the owner of the website
        $user = $event->audit->website->user;

        // Trigger the email notification on the user
        $user->notify(new AuditCompletedNotification($event->audit));
    }
}

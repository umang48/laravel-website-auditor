<?php

namespace App\Notifications;

use App\Models\Audit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuditCompletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $websiteName = $this->audit->website->name;
        $score = $this->audit->score;

        return (new MailMessage)
            ->subject("Audit Completed: {$websiteName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The SEO audit for {$websiteName} has finished.")
            ->line("Final Score: {$score} / 100")
            ->action('View Full Report', url("/api/websites/{$this->audit->website_id}")) // Placeholder URL
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

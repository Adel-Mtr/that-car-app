<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reminder $reminder) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (($notifiable->preferences['email_reminders'] ?? true) === true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->reminder->title.' — '.$this->reminder->vehicle->registration)
            ->greeting('A quick heads-up, '.$notifiable->name)
            ->line($this->reminder->title.' for your '.$this->reminder->vehicle->displayName().' is due '.$this->reminder->due_at->diffForHumans().'.')
            ->line($this->reminder->description ?: 'Open your vehicle timeline to review the details and next step.')
            ->action('Review vehicle', route('vehicles.show', $this->reminder->vehicle))
            ->line('Keeping the record up to date helps your car-health plan stay accurate.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reminder_id' => $this->reminder->id,
            'vehicle_id' => $this->reminder->vehicle_id,
            'title' => $this->reminder->title,
            'due_at' => $this->reminder->due_at->toIso8601String(),
            'url' => route('vehicles.show', $this->reminder->vehicle),
        ];
    }
}

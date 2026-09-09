<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleAccessGrantedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Vehicle $vehicle, public User $inviter) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A vehicle has been shared with you on '.config('app.name'))
            ->greeting('Hello '.$notifiable->name)
            ->line($this->inviter->name.' has shared their '.$this->vehicle->displayName().' with you.')
            ->line('You can now help keep its maintenance plan, reminders and history up to date.')
            ->action('Open shared vehicle', route('vehicles.show', $this->vehicle));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'title' => $this->vehicle->displayName().' was shared with you',
            'inviter' => $this->inviter->name,
            'url' => route('vehicles.show', $this->vehicle),
        ];
    }
}

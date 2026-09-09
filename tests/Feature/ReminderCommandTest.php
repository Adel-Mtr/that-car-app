<?php

namespace Tests\Feature;

use App\Models\Reminder;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_reminders_are_sent_once_when_their_lead_window_opens(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();
        $reminder = Reminder::factory()->for($vehicle)->for($user)->create([
            'due_at' => now()->addDays(7),
            'lead_days' => 14,
        ]);

        $this->artisan('reminders:send-due')->assertSuccessful()->expectsOutput('1 reminder sent.');

        Notification::assertSentTo($user, MaintenanceDueNotification::class);
        $this->assertNotNull($reminder->fresh()->last_sent_at);

        $this->artisan('reminders:send-due')->assertSuccessful()->expectsOutput('0 reminders sent.');
        Notification::assertSentToTimes($user, MaintenanceDueNotification::class, 1);
    }

    public function test_reminders_outside_their_lead_window_are_not_sent(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();
        Reminder::factory()->for($vehicle)->for($user)->create([
            'due_at' => now()->addDays(45),
            'lead_days' => 14,
        ]);

        $this->artisan('reminders:send-due')->assertSuccessful()->expectsOutput('0 reminders sent.');
        Notification::assertNothingSent();
    }
}

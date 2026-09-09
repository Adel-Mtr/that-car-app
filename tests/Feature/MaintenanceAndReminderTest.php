<?php

namespace Tests\Feature;

use App\Models\MaintenanceRecord;
use App\Models\Reminder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceAndReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_owner_can_record_completed_maintenance_with_a_precise_cost(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();

        $this->actingAs($user)->post(route('vehicles.maintenance-records.store', $vehicle), [
            'title' => 'Oil and filter service',
            'type' => 'service',
            'status' => 'completed',
            'urgency' => 'routine',
            'completed_at' => today()->toDateString(),
            'mileage' => 42650,
            'cost' => '189.95',
            'provider_name' => 'Town Garage',
        ])->assertRedirect();

        $this->assertDatabaseHas('maintenance_records', [
            'vehicle_id' => $vehicle->id,
            'created_by' => $user->id,
            'title' => 'Oil and filter service',
            'cost_pence' => 18995,
            'status' => 'completed',
        ]);
    }

    public function test_completing_work_updates_the_vehicle_mileage(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create(['current_mileage' => 40000]);
        $record = MaintenanceRecord::factory()->for($vehicle)->create([
            'created_by' => $user->id,
            'mileage' => 44500,
        ]);

        $this->actingAs($user)
            ->post(route('vehicles.maintenance-records.completion.store', [$vehicle, $record]))
            ->assertRedirect();

        $this->assertDatabaseHas('maintenance_records', ['id' => $record->id, 'status' => 'completed']);
        $this->assertSame(44500, $vehicle->fresh()->current_mileage);
    }

    public function test_an_owner_can_schedule_and_complete_a_reminder(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();

        $this->actingAs($user)->post(route('vehicles.reminders.store', $vehicle), [
            'title' => 'Renew breakdown cover',
            'category' => 'breakdown',
            'due_at' => today()->addMonth()->toDateString(),
            'lead_days' => 14,
            'recurrence' => 'yearly',
        ])->assertRedirect();

        $reminder = Reminder::query()->sole();

        $this->actingAs($user)
            ->post(route('vehicles.reminders.completion.store', [$vehicle, $reminder]))
            ->assertRedirect();

        $this->assertNotNull($reminder->fresh()->completed_at);

        $nextReminder = Reminder::query()->whereKeyNot($reminder->id)->sole();
        $this->assertSame(
            $reminder->due_at->copy()->addYearNoOverflow()->toDateString(),
            $nextReminder->due_at->toDateString(),
        );
        $this->assertSame('yearly', $nextReminder->recurrence);
        $this->assertNull($nextReminder->completed_at);
        $this->assertNull($nextReminder->last_sent_at);
    }

    public function test_a_member_cannot_add_work_to_someone_elses_vehicle(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();

        $this->actingAs($stranger)->post(route('vehicles.maintenance-records.store', $vehicle), [
            'title' => 'Unauthorised change',
            'type' => 'repair',
            'status' => 'planned',
            'urgency' => 'routine',
        ])->assertForbidden();

        $this->assertDatabaseMissing('maintenance_records', ['title' => 'Unauthorised change']);
    }
}

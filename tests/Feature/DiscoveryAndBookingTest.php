<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryAndBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_member_can_save_an_event_with_one_of_their_vehicles(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();
        $event = Event::factory()->create();

        $this->actingAs($user)->post(route('events.attendance.store', $event), [
            'status' => 'going',
            'vehicle_id' => $vehicle->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'going',
        ]);
    }

    public function test_a_member_cannot_attach_someone_elses_vehicle_to_an_event(): void
    {
        $member = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $event = Event::factory()->create();

        $this->actingAs($member)->post(route('events.attendance.store', $event), [
            'status' => 'going',
            'vehicle_id' => $vehicle->id,
        ])->assertForbidden();

        $this->assertDatabaseMissing('event_user', ['event_id' => $event->id, 'user_id' => $member->id]);
    }

    public function test_only_vehicle_managers_can_attach_a_shared_vehicle_to_an_event(): void
    {
        $owner = User::factory()->create();
        $manager = User::factory()->create();
        $viewer = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();
        $vehicle->members()->attach($manager, ['role' => 'manager', 'accepted_at' => now()]);
        $vehicle->members()->attach($viewer, ['role' => 'viewer', 'accepted_at' => now()]);
        $event = Event::factory()->create();

        $this->actingAs($manager)->post(route('events.attendance.store', $event), [
            'status' => 'going',
            'vehicle_id' => $vehicle->id,
        ])->assertRedirect();

        $this->actingAs($viewer)->post(route('events.attendance.store', $event), [
            'status' => 'going',
            'vehicle_id' => $vehicle->id,
        ])->assertForbidden();

        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $manager->id,
            'vehicle_id' => $vehicle->id,
        ]);
        $this->assertDatabaseMissing('event_user', [
            'event_id' => $event->id,
            'user_id' => $viewer->id,
        ]);
    }

    public function test_an_owner_can_request_and_cancel_a_specialist_booking(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();
        $specialist = Specialist::factory()->create();

        $this->actingAs($user)->post(route('bookings.store'), [
            'vehicle_id' => $vehicle->id,
            'specialist_id' => $specialist->id,
            'service' => 'Annual service and inspection',
            'description' => 'Please check the front brakes too.',
            'requested_start_at' => now()->addWeek()->toDateTimeString(),
        ])->assertRedirect(route('bookings.index'));

        $booking = Booking::query()->sole();
        $this->assertNotEmpty($booking->confirmation_code);
        $this->assertSame('requested', $booking->status);

        $this->actingAs($user)->delete(route('bookings.destroy', $booking))->assertRedirect();
        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_a_post_can_only_be_linked_to_a_vehicle_the_author_manages(): void
    {
        $author = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'body' => 'This update should never be published.',
            'vehicle_id' => $otherVehicle->id,
            'category' => 'update',
            'visibility' => 'public',
        ])->assertForbidden();

        $this->assertDatabaseCount('posts', 0);

        $ownVehicle = Vehicle::factory()->for($author, 'owner')->create();
        $this->actingAs($author)->post(route('posts.store'), [
            'body' => 'Fresh tyres fitted for the summer.',
            'vehicle_id' => $ownVehicle->id,
            'category' => 'update',
            'visibility' => 'public',
        ])->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'user_id' => $author->id,
            'vehicle_id' => $ownVehicle->id,
            'body' => 'Fresh tyres fitted for the summer.',
        ]);
    }
}

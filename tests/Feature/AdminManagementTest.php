<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_events(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.events.store'), [
            'title' => 'Sunday Heritage Run',
            'summary' => 'A relaxed morning drive for modern classics and enthusiast cars.',
            'description' => 'Meet at the venue, enjoy the route and return for coffee.',
            'category' => 'drive',
            'venue' => 'Heritage Yard',
            'city' => 'London',
            'postcode' => 'W1A 1AA',
            'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addWeek()->addHours(3)->format('Y-m-d H:i:s'),
            'capacity' => 80,
            'price_pounds' => '12.50',
            'vehicle_tags' => 'Classic, German, Classic',
            'is_published' => '1',
        ])->assertRedirect(route('admin.events.index'));

        $event = Event::query()->where('title', 'Sunday Heritage Run')->firstOrFail();
        $this->assertSame(1250, $event->price_pence);
        $this->assertSame(['Classic', 'German'], $event->vehicle_tags);
        $this->assertTrue($event->is_published);

        $this->actingAs($admin)->put(route('admin.events.update', $event), [
            'title' => 'Sunday Heritage Run Updated',
            'summary' => $event->summary,
            'description' => $event->description,
            'category' => 'meet',
            'venue' => $event->venue,
            'city' => $event->city,
            'starts_at' => $event->starts_at->format('Y-m-d H:i:s'),
            'price_pounds' => '0',
            'is_featured' => '1',
        ])->assertRedirect(route('admin.events.index'));

        $event->refresh();
        $this->assertSame('Sunday Heritage Run Updated', $event->title);
        $this->assertTrue($event->is_featured);
        $this->assertNotSame('sunday-heritage-run', $event->slug);

        $this->actingAs($admin)->delete(route('admin.events.destroy', $event))
            ->assertRedirect(route('admin.events.index'));
        $this->assertModelMissing($event);
    }

    public function test_admin_can_manage_specialists_and_booking_statuses(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.specialists.store'), [
            'name' => 'West London Motor Works',
            'tagline' => 'Independent servicing for enthusiast cars.',
            'description' => 'Service, diagnostics and preventative maintenance.',
            'categories' => 'servicing, diagnostics, servicing',
            'brands' => 'Audi, Volkswagen',
            'address' => '1 Workshop Lane',
            'city' => 'London',
            'postcode' => 'TW8 0AA',
            'price_level' => '££',
            'email' => 'workshop@example.test',
            'is_verified' => '1',
        ])->assertRedirect(route('admin.specialists.index'));

        $specialist = Specialist::query()->where('name', 'West London Motor Works')->firstOrFail();
        $this->assertSame(['servicing', 'diagnostics'], $specialist->categories);
        $this->assertTrue($specialist->is_verified);

        $booking = Booking::factory()->for($specialist)->create(['status' => 'requested', 'quote_pence' => null]);

        $this->actingAs($admin)->patch(route('admin.bookings.update', $booking), [
            'status' => 'quoted',
            'quote_pounds' => '349.99',
        ])->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame('quoted', $booking->status);
        $this->assertSame(34999, $booking->quote_pence);

        $this->actingAs($admin)->delete(route('admin.specialists.destroy', $specialist))
            ->assertSessionHasErrors('specialist');
        $this->assertModelExists($specialist);
    }

    public function test_regular_members_cannot_access_admin_management_routes(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $specialist = Specialist::factory()->create();
        $booking = Booking::factory()->create();

        foreach ([
            ['get', route('admin.events.index')],
            ['get', route('admin.events.edit', $event)],
            ['get', route('admin.specialists.index')],
            ['get', route('admin.specialists.edit', $specialist)],
            ['get', route('admin.bookings.index')],
            ['patch', route('admin.bookings.update', $booking)],
        ] as [$method, $uri]) {
            $response = $method === 'patch'
                ? $this->actingAs($user)->patch($uri, ['status' => 'confirmed'])
                : $this->actingAs($user)->get($uri);

            $response->assertForbidden();
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_main_product_experience_renders_for_a_member(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create();
        $event = Event::factory()->create();
        $specialist = Specialist::factory()->create();

        $routes = [
            route('dashboard'),
            route('vehicles.index'),
            route('vehicles.show', $vehicle),
            route('events.index'),
            route('events.show', $event),
            route('specialists.index'),
            route('specialists.show', $specialist),
            route('community'),
            route('bookings.index'),
            route('profile.edit'),
        ];

        foreach ($routes as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }

    public function test_only_administrators_can_open_the_operations_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}

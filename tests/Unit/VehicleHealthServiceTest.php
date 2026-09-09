<?php

namespace Tests\Unit;

use App\Models\MaintenanceRecord;
use App\Models\MotDefect;
use App\Models\MotTest;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleHealthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_vehicle_with_no_open_issues_is_healthy(): void
    {
        $vehicle = Vehicle::factory()->create([
            'mot_due_at' => today()->addMonths(8),
            'tax_due_at' => today()->addMonths(7),
            'insurance_due_at' => today()->addMonths(6),
        ]);

        $health = app(VehicleHealthService::class)->assess($vehicle);

        $this->assertSame(100, $health['score']);
        $this->assertSame('healthy', $health['status']);
    }

    public function test_expired_legal_items_and_overdue_work_lower_the_score(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user, 'owner')->create([
            'mot_due_at' => today()->subDay(),
            'mot_status' => 'expired',
            'tax_due_at' => today()->subDay(),
            'tax_status' => 'untaxed',
            'insurance_due_at' => today()->subDay(),
        ]);
        MaintenanceRecord::factory()->for($vehicle)->create([
            'created_by' => $user->id,
            'due_at' => today()->subWeek(),
        ]);

        $health = app(VehicleHealthService::class)->assess($vehicle);

        $this->assertSame(0, $health['score']);
        $this->assertSame('action', $health['status']);
        $this->assertCount(4, $health['reasons']);
    }

    public function test_dangerous_mot_defects_are_treated_as_critical(): void
    {
        $vehicle = Vehicle::factory()->create([
            'mot_due_at' => today()->addMonths(8),
            'tax_due_at' => today()->addMonths(7),
            'insurance_due_at' => today()->addMonths(6),
        ]);
        $test = MotTest::factory()->for($vehicle)->create();
        MotDefect::factory()->for($test, 'motTest')->create([
            'type' => 'dangerous',
            'dangerous' => true,
        ]);

        $health = app(VehicleHealthService::class)->assess($vehicle);

        $this->assertSame(65, $health['score']);
        $this->assertSame('attention', $health['status']);
        $this->assertSame('Dangerous MOT defect recorded', $health['reasons'][0]['title']);
    }
}

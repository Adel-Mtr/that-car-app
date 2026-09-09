<?php

namespace Tests\Feature;

use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Notifications\VehicleAccessGrantedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_owner_can_add_a_vehicle_from_its_registration(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'registration' => 'ab 12-cde',
            'current_mileage' => 48120,
            'insurance_due_at' => today()->addMonths(5)->toDateString(),
            'visibility' => 'private',
        ]);

        $vehicle = Vehicle::query()->sole();

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $this->assertSame('AB12CDE', $vehicle->registration);
        $this->assertSame($user->id, $vehicle->owner_id);
        $this->assertSame(2, $vehicle->motTests()->count());
        $this->assertSame(1, $vehicle->maintenanceRecords()->where('source', 'mot')->count());
        $this->assertSame(3, $vehicle->reminders()->count());
    }

    public function test_the_same_registration_cannot_be_added_twice_to_one_garage(): void
    {
        $user = User::factory()->create();
        Vehicle::factory()->for($user, 'owner')->create(['registration' => 'AB12CDE']);

        $this->actingAs($user)
            ->from(route('vehicles.create'))
            ->post(route('vehicles.store'), ['registration' => 'ab 12 cde'])
            ->assertRedirect(route('vehicles.create'))
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseCount('vehicles', 1);
    }

    public function test_private_vehicle_data_is_isolated_from_other_members(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();

        $this->actingAs($stranger)->get(route('vehicles.show', $vehicle))->assertForbidden();
        $this->actingAs($stranger)->patch(route('vehicles.update', $vehicle), [
            'current_mileage' => 99999,
            'visibility' => 'private',
        ])->assertForbidden();

        $this->assertNotSame(99999, $vehicle->fresh()->current_mileage);
    }

    public function test_an_accepted_manager_can_update_but_not_delete_a_shared_vehicle(): void
    {
        $owner = User::factory()->create();
        $manager = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();
        $vehicle->members()->attach($manager, ['role' => 'manager', 'accepted_at' => now()]);

        $this->actingAs($manager)->patch(route('vehicles.update', $vehicle), [
            'current_mileage' => 52000,
            'visibility' => 'shared',
        ])->assertRedirect();

        $this->actingAs($manager)->delete(route('vehicles.destroy', $vehicle))->assertForbidden();
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'current_mileage' => 52000]);
    }

    public function test_an_owner_can_share_a_vehicle_with_an_existing_member(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('vehicles.members.store', $vehicle), [
            'email' => $member->email,
            'role' => 'manager',
        ])->assertRedirect();

        $this->assertDatabaseHas('vehicle_user', [
            'vehicle_id' => $vehicle->id,
            'user_id' => $member->id,
            'role' => 'manager',
        ]);
        Notification::assertSentTo($member, VehicleAccessGrantedNotification::class);
        $this->actingAs($member)->get(route('vehicles.show', $vehicle))->assertOk();

        $this->actingAs($owner)
            ->delete(route('vehicles.members.destroy', [$vehicle, $member]))
            ->assertRedirect();
        $this->assertDatabaseMissing('vehicle_user', ['vehicle_id' => $vehicle->id, 'user_id' => $member->id]);
    }

    public function test_documents_are_private_and_stored_outside_the_public_disk(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $vehicle = Vehicle::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->post(route('vehicles.documents.store', $vehicle), [
            'title' => 'Annual service invoice',
            'type' => 'service_invoice',
            'document_date' => today()->toDateString(),
            'document' => UploadedFile::fake()->create('invoice.pdf', 120, 'application/pdf'),
        ])->assertRedirect();

        $document = VehicleDocument::query()->sole();
        Storage::disk('local')->assertExists($document->path);
        $this->assertStringStartsWith('vehicle-documents/'.$vehicle->public_id, $document->path);

        $this->actingAs($stranger)
            ->get(route('vehicles.documents.show', [$vehicle, $document]))
            ->assertForbidden();
    }

    public function test_public_passports_show_curated_history_without_private_identifiers(): void
    {
        $owner = User::factory()->create(['name' => 'Jamie Owner', 'username' => 'jamie']);
        $vehicle = Vehicle::factory()->publiclyVisible()->for($owner, 'owner')->create([
            'registration' => 'PR11VTE',
        ]);
        MaintenanceRecord::factory()->completed()->for($vehicle)->create([
            'created_by' => $owner->id,
            'title' => 'Full annual service',
        ]);
        VehicleDocument::factory()->for($vehicle)->create([
            'uploaded_by' => $owner->id,
            'original_filename' => 'private-insurance.pdf',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('vehicles.show', $vehicle))
            ->assertForbidden();

        $this->get(route('garage.public', $vehicle->public_id))
            ->assertOk()
            ->assertSee('Full annual service')
            ->assertSee('Jamie Owner')
            ->assertDontSee('PR11VTE')
            ->assertDontSee('private-insurance.pdf');
    }
}

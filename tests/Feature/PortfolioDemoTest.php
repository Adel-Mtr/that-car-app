<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PortfolioDemoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['demo.enabled' => true]);
    }

    public function test_sample_member_can_browse_and_sign_out(): void
    {
        $user = User::factory()->create(['email' => 'demo@thatcarapp.test']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/app');
        $this->assertAuthenticatedAs($user);
        $this->get('/app')->assertOk()->assertSee('Portfolio demo');
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_credentials_and_admin_pages_are_blocked(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@thatcarapp.test']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertForbidden();
        $this->assertGuest();
        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    public function test_account_creation_and_profile_changes_are_blocked(): void
    {
        $this->get('/register')->assertRedirect('/login');
        $this->post('/register', ['email' => 'new@example.test'])->assertForbidden();
        $this->post('/forgot-password', ['email' => 'demo@thatcarapp.test'])->assertForbidden();
        $user = User::factory()->create();
        $this->actingAs($user)->patch('/settings', ['name' => 'Changed'])->assertForbidden();
        $this->assertSame($user->name, $user->fresh()->name);
    }

    public function test_vehicle_changes_and_document_uploads_are_blocked(): void
    {
        $user = User::factory()->create();
        $vehicle = \App\Models\Vehicle::factory()->create(['owner_id' => $user->id]);
        $this->actingAs($user)->delete('/vehicles/'.$vehicle->id)->assertForbidden();
        $this->post('/vehicles/'.$vehicle->id.'/documents', [
            'document' => UploadedFile::fake()->create('private.pdf', 10),
        ])->assertForbidden();
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id]);
        $this->assertDatabaseCount('vehicle_documents', 0);
    }
}

<?php

namespace Tests\Unit;

use App\Exceptions\VehicleLookupException;
use App\Services\GovernmentVehicleDataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GovernmentVehicleDataProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.vehicle_data.dvla', [
            'url' => 'https://dvla.example/vehicle-enquiry/v1/vehicles',
            'key' => 'dvla-key',
        ]);
        config()->set('services.vehicle_data.dvsa', [
            'url' => 'https://mot.example',
            'key' => 'mot-key',
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'token_url' => 'https://login.example/token',
            'scope' => 'https://tapi.dvsa.gov.uk/.default',
        ]);
        Cache::forget('vehicle-data:dvsa-access-token');
    }

    public function test_it_combines_dvla_vehicle_data_with_dvsa_mot_history(): void
    {
        Http::fake([
            'https://login.example/token' => Http::response(['access_token' => 'issued-token']),
            'https://dvla.example/*' => Http::response([
                'registrationNumber' => 'AB12CDE',
                'taxStatus' => 'Taxed',
                'taxDueDate' => '2027-03-01',
                'motStatus' => 'Valid',
                'motExpiryDate' => '2027-02-14',
                'make' => 'VOLKSWAGEN',
                'yearOfManufacture' => 2020,
                'engineCapacity' => 1984,
                'fuelType' => 'PETROL',
                'colour' => 'BLUE',
            ]),
            'https://mot.example/*' => Http::response([
                'registration' => 'AB12CDE',
                'model' => 'GOLF',
                'motTests' => [[
                    'completedDate' => '2026-02-10 10:30:00',
                    'motTestNumber' => '1234567890',
                    'expiryDate' => '2027-02-14',
                    'testResult' => 'PASSED',
                    'odometerValue' => '42000',
                    'odometerUnit' => 'MI',
                    'dataSource' => 'DVSA',
                    'defects' => [[
                        'text' => 'Front tyre worn close to the legal limit',
                        'type' => 'ADVISORY',
                        'dangerous' => false,
                    ]],
                ]],
            ]),
        ]);

        $result = app(GovernmentVehicleDataProvider::class)->lookup('ab 12 cde');

        $this->assertSame('AB12CDE', $result['registration']);
        $this->assertSame('Volkswagen', $result['make']);
        $this->assertSame('Golf', $result['model']);
        $this->assertSame('valid', $result['mot_status']);
        $this->assertSame('passed', $result['mot_tests'][0]['result']);
        $this->assertSame('advisory', $result['mot_tests'][0]['defects'][0]['type']);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://dvla.example/vehicle-enquiry/v1/vehicles'
            && $request->hasHeader('x-api-key', 'dvla-key'));
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/registration/AB12CDE')
            && $request->hasHeader('Authorization', 'Bearer issued-token'));
    }

    public function test_missing_mot_test_numbers_remain_nullable(): void
    {
        Http::fake([
            'https://login.example/token' => Http::response(['access_token' => 'issued-token']),
            'https://dvla.example/*' => Http::response([
                'registrationNumber' => 'AB12CDE',
                'make' => 'VOLKSWAGEN',
                'yearOfManufacture' => 2020,
            ]),
            'https://mot.example/*' => Http::response([
                'registration' => 'AB12CDE',
                'model' => 'GOLF',
                'motTests' => [
                    ['completedDate' => '2026-02-10 10:30:00', 'testResult' => 'PASSED'],
                    ['completedDate' => '2025-02-10 10:30:00', 'testResult' => 'PASSED'],
                ],
            ]),
        ]);

        $result = app(GovernmentVehicleDataProvider::class)->lookup('AB12CDE');

        $this->assertNull($result['mot_tests'][0]['test_number']);
        $this->assertNull($result['mot_tests'][1]['test_number']);
    }

    public function test_it_fails_safely_when_vehicle_data_authentication_is_unavailable(): void
    {
        Http::fake([
            'https://login.example/token' => Http::response([], 503),
            'https://dvla.example/*' => Http::response([
                'registrationNumber' => 'AB12CDE',
                'make' => 'VOLKSWAGEN',
                'yearOfManufacture' => 2020,
            ]),
        ]);

        $this->expectException(VehicleLookupException::class);
        $this->expectExceptionMessage('Vehicle data authentication is temporarily unavailable. Please try again shortly.');

        app(GovernmentVehicleDataProvider::class)->lookup('AB12CDE');
    }

    public function test_it_fails_safely_when_the_token_response_is_invalid(): void
    {
        Http::fake([
            'https://login.example/token' => Http::response(['token_type' => 'Bearer']),
            'https://dvla.example/*' => Http::response([
                'registrationNumber' => 'AB12CDE',
                'make' => 'VOLKSWAGEN',
                'yearOfManufacture' => 2020,
            ]),
        ]);

        $this->expectException(VehicleLookupException::class);
        $this->expectExceptionMessage('Vehicle data authentication returned an invalid access token.');

        app(GovernmentVehicleDataProvider::class)->lookup('AB12CDE');
    }

    public function test_it_fails_safely_when_live_credentials_are_missing(): void
    {
        config()->set('services.vehicle_data.dvla.key');

        $this->expectException(VehicleLookupException::class);
        $this->expectExceptionMessage('Live vehicle lookup has not been configured yet.');

        app(GovernmentVehicleDataProvider::class)->lookup('AB12CDE');
    }
}

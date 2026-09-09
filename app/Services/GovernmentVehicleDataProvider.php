<?php

namespace App\Services;

use App\Contracts\VehicleDataProvider;
use App\Exceptions\VehicleLookupException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GovernmentVehicleDataProvider implements VehicleDataProvider
{
    /**
     * @return array<string, mixed>
     */
    public function lookup(string $registration): array
    {
        $registration = Str::upper((string) preg_replace('/[^A-Z0-9]/i', '', $registration));

        $this->ensureConfigured();

        try {
            $vehicle = $this->vehicleRequest()
                ->post(config('services.vehicle_data.dvla.url'), [
                    'registrationNumber' => $registration,
                ])
                ->throw()
                ->json();

            $history = $this->motHistory($registration);
        } catch (RequestException $exception) {
            $status = $exception->response->status();

            throw new VehicleLookupException(match ($status) {
                400, 404 => 'We could not find that registration. Check it and try again.',
                429 => 'The vehicle data service is busy. Please wait a moment and try again.',
                default => 'Vehicle data is temporarily unavailable. Please try again shortly.',
            }, previous: $exception);
        } catch (ConnectionException $exception) {
            throw new VehicleLookupException(
                'Vehicle data is temporarily unavailable. Please try again shortly.',
                previous: $exception,
            );
        }

        $tests = collect($history['motTests'] ?? [])
            ->map(fn (array $test): array => [
                'test_number' => filled($test['motTestNumber'] ?? null) ? (string) $test['motTestNumber'] : null,
                'completed_at' => $test['completedDate'] ?? null,
                'expiry_at' => $test['expiryDate'] ?? null,
                'result' => Str::lower($test['testResult'] ?? 'unknown'),
                'odometer_value' => is_numeric($test['odometerValue'] ?? null) ? (int) $test['odometerValue'] : null,
                'odometer_unit' => Str::lower($test['odometerUnit'] ?? 'mi'),
                'data_source' => Str::lower($test['dataSource'] ?? 'dvsa'),
                'defects' => collect($test['defects'] ?? [])->map(fn (array $defect): array => [
                    'text' => $defect['text'] ?? 'MOT defect',
                    'type' => Str::lower($defect['type'] ?? 'advisory'),
                    'dangerous' => (bool) ($defect['dangerous'] ?? false),
                ])->all(),
            ])
            ->filter(fn (array $test): bool => $test['completed_at'] !== null)
            ->values()
            ->all();

        return [
            'registration' => $registration,
            'make' => Str::title($vehicle['make'] ?? $history['make'] ?? 'Unknown'),
            'model' => Str::title($history['model'] ?? 'Model not supplied'),
            'variant' => null,
            'year' => (int) ($vehicle['yearOfManufacture'] ?? Str::substr($history['manufactureDate'] ?? $history['firstUsedDate'] ?? now()->year, 0, 4)),
            'colour' => Str::title($vehicle['colour'] ?? $history['primaryColour'] ?? 'Unknown'),
            'fuel_type' => Str::title($vehicle['fuelType'] ?? $history['fuelType'] ?? 'Unknown'),
            'transmission' => 'Not supplied',
            'engine_size_cc' => Arr::get($vehicle, 'engineCapacity') ?? (is_numeric($history['engineSize'] ?? null) ? (int) $history['engineSize'] : null),
            'mot_status' => $this->normaliseMotStatus($vehicle['motStatus'] ?? null),
            'mot_due_at' => $vehicle['motExpiryDate'] ?? Arr::get($tests, '0.expiry_at'),
            'tax_status' => Str::lower($vehicle['taxStatus'] ?? 'unknown'),
            'tax_due_at' => $vehicle['taxDueDate'] ?? null,
            'valuation_pence' => null,
            'mot_tests' => $tests,
            'provider' => 'dvla-dvsa',
        ];
    }

    private function vehicleRequest(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withHeaders(['x-api-key' => config('services.vehicle_data.dvla.key')])
            ->timeout(8)
            ->retry(2, 250, throw: false);
    }

    /** @return array<string, mixed> */
    private function motHistory(string $registration): array
    {
        try {
            $response = Http::acceptJson()
                ->withToken($this->motAccessToken())
                ->withHeaders(['X-API-Key' => config('services.vehicle_data.dvsa.key')])
                ->timeout(10)
                ->retry(2, 250, throw: false)
                ->get(rtrim(config('services.vehicle_data.dvsa.url'), '/').'/v1/trade/vehicles/registration/'.rawurlencode($registration))
                ->throw();
        } catch (RequestException $exception) {
            if ($exception->response->status() === 404) {
                return [];
            }

            throw $exception;
        }

        $payload = $response->json();

        return array_is_list($payload) ? ($payload[0] ?? []) : $payload;
    }

    private function motAccessToken(): string
    {
        return Cache::remember('vehicle-data:dvsa-access-token', now()->addMinutes(50), function (): string {
            try {
                $response = Http::asForm()
                    ->timeout(8)
                    ->retry(2, 250, throw: false)
                    ->post(config('services.vehicle_data.dvsa.token_url'), [
                        'grant_type' => 'client_credentials',
                        'client_id' => config('services.vehicle_data.dvsa.client_id'),
                        'client_secret' => config('services.vehicle_data.dvsa.client_secret'),
                        'scope' => config('services.vehicle_data.dvsa.scope'),
                    ])
                    ->throw();
            } catch (RequestException|ConnectionException $exception) {
                throw new VehicleLookupException(
                    'Vehicle data authentication is temporarily unavailable. Please try again shortly.',
                    previous: $exception,
                );
            }

            $token = $response->json('access_token');

            if (! is_string($token) || blank($token)) {
                throw new VehicleLookupException('Vehicle data authentication returned an invalid access token.');
            }

            return $token;
        });
    }

    private function normaliseMotStatus(?string $status): string
    {
        return match (Str::lower($status ?? '')) {
            'valid' => 'valid',
            'not valid' => 'expired',
            default => 'unknown',
        };
    }

    private function ensureConfigured(): void
    {
        $required = [
            config('services.vehicle_data.dvla.key'),
            config('services.vehicle_data.dvsa.key'),
            config('services.vehicle_data.dvsa.client_id'),
            config('services.vehicle_data.dvsa.client_secret'),
            config('services.vehicle_data.dvsa.token_url'),
        ];

        if (collect($required)->contains(fn (mixed $value): bool => blank($value))) {
            throw new VehicleLookupException('Live vehicle lookup has not been configured yet.');
        }
    }
}

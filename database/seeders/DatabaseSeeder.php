<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\MaintenanceRecord;
use App\Models\Post;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demoUser = User::factory()->create([
            'name' => 'Jordan Ellis',
            'username' => 'jordan',
            'email' => 'demo@thatcarapp.test',
            'password' => 'password',
            'bio' => 'Daily driver, weekend tinkerer and believer in keeping every receipt.',
            'postcode' => 'OX26 5HA',
            'preferences' => ['primary_goal' => 'project', 'email_reminders' => true],
        ]);
        $admin = User::factory()->admin()->create([
            'name' => 'That Car App Admin',
            'username' => 'admin',
            'email' => 'admin@thatcarapp.test',
            'password' => 'password',
        ]);
        $communityMembers = collect([
            ['name' => 'Amelia Ward', 'username' => 'ameliaw', 'email' => 'amelia@example.test', 'bio' => 'Classic Mini owner and Sunday-drive regular.'],
            ['name' => 'Marcus Reed', 'username' => 'marcusbuilds', 'email' => 'marcus@example.test', 'bio' => 'Building a road-focused Golf GTI one sensible upgrade at a time.'],
            ['name' => 'Priya Shah', 'username' => 'priyadrives', 'email' => 'priya@example.test', 'bio' => 'EV driver, detailer and road-trip planner.'],
        ])->map(fn (array $member): User => User::factory()->create($member));

        $bmw = Vehicle::factory()->publiclyVisible()->create([
            'owner_id' => $demoUser->id,
            'registration' => 'RJ21MOT',
            'make' => 'BMW',
            'model' => '3 Series',
            'variant' => 'M340i xDrive',
            'year' => 2021,
            'colour' => 'Portimao blue',
            'fuel_type' => 'Petrol',
            'transmission' => 'Automatic',
            'engine_size_cc' => 2998,
            'current_mileage' => 38420,
            'annual_mileage' => 8500,
            'mot_due_at' => today()->addDays(54),
            'tax_due_at' => today()->addDays(111),
            'insurance_due_at' => today()->addDays(78),
            'health_score' => 82,
            'valuation_pence' => 3395000,
        ]);
        $ford = Vehicle::factory()->create([
            'owner_id' => $demoUser->id,
            'registration' => 'EX68DAY',
            'make' => 'Ford',
            'model' => 'Fiesta',
            'variant' => 'Titanium',
            'year' => 2018,
            'colour' => 'Magnetic grey',
            'fuel_type' => 'Petrol',
            'transmission' => 'Manual',
            'engine_size_cc' => 998,
            'current_mileage' => 61740,
            'annual_mileage' => 11000,
            'mot_due_at' => today()->addDays(214),
            'tax_due_at' => today()->addDays(242),
            'insurance_due_at' => today()->addDays(32),
            'health_score' => 94,
            'valuation_pence' => 735000,
        ]);

        $service = MaintenanceRecord::factory()->completed()->create([
            'vehicle_id' => $bmw->id,
            'created_by' => $demoUser->id,
            'type' => 'service',
            'title' => 'Oil service and inspection',
            'description' => 'Oil, microfilter and full vehicle inspection completed.',
            'completed_at' => today()->subDays(96),
            'mileage' => 36120,
            'cost_pence' => 38900,
            'provider_name' => 'North Oxford Motor Works',
        ]);
        MaintenanceRecord::factory()->completed()->create([
            'vehicle_id' => $bmw->id,
            'created_by' => $demoUser->id,
            'type' => 'upgrade',
            'title' => 'M Performance exhaust fitted',
            'description' => 'Cat-back system fitted with original parts retained.',
            'completed_at' => today()->subDays(187),
            'mileage' => 33440,
            'cost_pence' => 124500,
            'provider_name' => 'Cotswold Performance',
        ]);
        $tyreTask = MaintenanceRecord::factory()->create([
            'vehicle_id' => $bmw->id,
            'created_by' => $demoUser->id,
            'type' => 'mot_advisory',
            'title' => 'Inspect nearside front tyre',
            'description' => 'Tyre worn close to the legal limit. Measure tread and replace as a pair if required.',
            'source' => 'mot',
            'urgency' => 'attention',
            'due_at' => today()->addDays(16),
            'verification_status' => 'government_data',
            'mileage' => 37780,
        ]);
        MaintenanceRecord::factory()->create([
            'vehicle_id' => $bmw->id,
            'created_by' => $demoUser->id,
            'type' => 'service',
            'title' => 'Brake fluid service',
            'description' => 'Two-year brake fluid interval approaching.',
            'due_at' => today()->addDays(47),
            'urgency' => 'routine',
        ]);
        MaintenanceRecord::factory()->completed()->create([
            'vehicle_id' => $ford->id,
            'created_by' => $demoUser->id,
            'type' => 'service',
            'title' => 'Annual service',
            'completed_at' => today()->subDays(41),
            'mileage' => 60895,
            'cost_pence' => 21900,
            'provider_name' => 'Summertown Service Centre',
        ]);

        $motTest = $bmw->motTests()->create([
            'test_number' => '8472150391',
            'completed_at' => now()->subYear()->addDays(54),
            'expiry_at' => $bmw->mot_due_at,
            'result' => 'passed',
            'odometer_value' => 37780,
            'odometer_unit' => 'mi',
        ]);
        $motTest->defects()->create([
            'maintenance_record_id' => $tyreTask->id,
            'text' => 'Nearside front tyre worn close to legal limit/worn on edge',
            'type' => 'advisory',
            'dangerous' => false,
        ]);
        $bmw->motTests()->create([
            'test_number' => '7601439284',
            'completed_at' => now()->subYears(2)->addDays(48),
            'expiry_at' => today()->subYear()->addDays(54),
            'result' => 'passed',
            'odometer_value' => 29104,
            'odometer_unit' => 'mi',
        ]);

        foreach ([
            [$bmw, 'mot', 'MOT renewal', $bmw->mot_due_at, 30],
            [$bmw, 'tax', 'Vehicle tax renewal', $bmw->tax_due_at, 30],
            [$bmw, 'insurance', 'Insurance renewal', $bmw->insurance_due_at, 30],
            [$ford, 'insurance', 'Fiesta insurance renewal', $ford->insurance_due_at, 30],
        ] as [$vehicle, $category, $title, $dueAt, $leadDays]) {
            $vehicle->reminders()->create([
                'user_id' => $demoUser->id,
                'category' => $category,
                'title' => $title,
                'due_at' => $dueAt,
                'lead_days' => $leadDays,
            ]);
        }

        Storage::disk('local')->put('demo/bmw-service-record.txt', "That Car App demo document\nOil service and inspection\nMileage: 36,120\nTotal: £389.00\n");
        $bmw->documents()->create([
            'uploaded_by' => $demoUser->id,
            'title' => 'Oil service invoice',
            'type' => 'service_invoice',
            'disk' => 'local',
            'path' => 'demo/bmw-service-record.txt',
            'original_filename' => 'bmw-service-record.txt',
            'mime_type' => 'text/plain',
            'size_bytes' => 87,
            'document_date' => $service->completed_at,
            'verification_status' => 'document_supported',
            'extracted_data' => ['provider' => 'North Oxford Motor Works', 'total_pence' => 38900, 'mileage' => 36120],
        ]);

        $specialistData = [
            ['North Oxford Motor Works', 'Independent care for German performance cars.', ['servicing', 'performance'], ['BMW', 'Porsche', 'Volkswagen'], 'Oxford', '4.9', 186, '££'],
            ['Cotswold Classic Engineering', 'Restoration, preservation and sympathetic upgrades.', ['classic', 'bodywork'], ['Jaguar', 'MG', 'Triumph'], 'Bicester', '4.8', 92, '£££'],
            ['Volt & Vector EV', 'EV servicing, diagnostics and battery-health reports.', ['electric', 'servicing'], ['Tesla', 'Polestar', 'Kia'], 'London', '4.9', 134, '££'],
            ['The Alignment Room', 'Geometry, tyres and chassis setup with measured results.', ['tyres', 'performance'], ['All marques'], 'Birmingham', '4.7', 241, '££'],
            ['Studio Forty Detailing', 'Protection and presentation without the hard sell.', ['detailing'], ['All marques'], 'Bristol', '4.8', 318, '££'],
            ['Everyday Auto Care', 'Straightforward servicing and MOTs for daily drivers.', ['servicing', 'tyres'], ['All marques'], 'Manchester', '4.6', 407, '£'],
        ];
        $specialists = collect($specialistData)->map(function (array $data, int $index): Specialist {
            [$name, $tagline, $categories, $brands, $city, $rating, $reviews, $price] = $data;

            return Specialist::factory()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'tagline' => $tagline,
                'description' => $tagline.' Verified work can be added directly to your vehicle passport after completion.',
                'categories' => $categories,
                'brands' => $brands,
                'city' => $city,
                'rating' => $rating,
                'review_count' => $reviews,
                'price_level' => $price,
                'is_featured' => $index < 3,
            ]);
        });

        $eventData = [
            ['Bicester Sunday Scramble', 'bicester-sunday-scramble', 'meet', 'Bicester Heritage', 'Bicester', 18, true, 1850],
            ['Cars & Coffee Oxford', 'cars-coffee-oxford', 'meet', 'The Motor Shed', 'Oxford', 31, false, 0],
            ['Modern Classics Gathering', 'modern-classics-gathering', 'show', 'Caffeine & Machine', 'Stratford-upon-Avon', 46, true, 1200],
            ['Cotswolds Golden Hour Drive', 'cotswolds-golden-hour-drive', 'drive', 'Burford Car Park', 'Burford', 58, false, 0],
            ['Open Pitlane Evening', 'open-pitlane-evening', 'track', 'Donington Park', 'Derby', 74, false, 14900],
            ['British Icons on the Lawn', 'british-icons-on-the-lawn', 'classic', 'Harewood House', 'Leeds', 96, false, 2400],
        ];
        $events = collect($eventData)->map(function (array $data) use ($admin): Event {
            [$title, $slug, $category, $venue, $city, $days, $featured, $price] = $data;
            $startsAt = now()->addDays($days)->setTime($category === 'drive' ? 17 : 9, 30);

            return Event::factory()->create([
                'organizer_id' => $admin->id,
                'title' => $title,
                'slug' => $slug,
                'summary' => 'A thoughtfully curated '.$category.' for people who enjoy cars, good company and a well-planned day out.',
                'description' => 'Bring the car you love and meet people who care about the details. Entry information, arrival guidance and live updates are kept together in That Car App.',
                'category' => $category,
                'venue' => $venue,
                'city' => $city,
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->copy()->addHours($category === 'track' ? 5 : 3),
                'price_pence' => $price,
                'is_featured' => $featured,
            ]);
        });
        $events->first()->attendees()->attach($demoUser->id, ['vehicle_id' => $bmw->id, 'status' => 'going']);

        $memberVehicles = collect([
            [$communityMembers[0], 'LJ72MIN', 'MINI', 'Hatch', 'Cooper S', 2022],
            [$communityMembers[1], 'GT19BLD', 'Volkswagen', 'Golf', 'GTI Performance', 2019],
            [$communityMembers[2], 'EV23JOY', 'Polestar', '2', 'Long Range', 2023],
        ])->map(fn (array $data): Vehicle => Vehicle::factory()->publiclyVisible()->create([
            'owner_id' => $data[0]->id,
            'registration' => $data[1],
            'make' => $data[2],
            'model' => $data[3],
            'variant' => $data[4],
            'year' => $data[5],
        ]));

        collect([
            [$communityMembers[1], $memberVehicles[1], 'Fresh set of road-biased coilovers fitted and aligned. The biggest improvement is actually the calmer ride on broken B-roads.', 'build', 84],
            [$communityMembers[0], $memberVehicles[0], 'First early start of the year. Quiet roads, warm coffee and 120 miles without a destination.', 'drive', 51],
            [$communityMembers[2], $memberVehicles[2], 'Battery health report came back at 96%. I have added the certificate to the car history so the next owner will have the evidence.', 'update', 109],
            [$demoUser, $bmw, 'The MOT advisory is now a maintenance task. Booking a tyre inspection before it becomes next year’s failure.', 'update', 27],
        ])->each(fn (array $data): Post => Post::factory()->create([
            'user_id' => $data[0]->id,
            'vehicle_id' => $data[1]->id,
            'body' => $data[2],
            'category' => $data[3],
            'likes_count' => $data[4],
        ]));

        Booking::factory()->create([
            'user_id' => $demoUser->id,
            'vehicle_id' => $bmw->id,
            'specialist_id' => $specialists->first()->id,
            'service' => 'Tyre inspection and alignment check',
            'description' => 'Please inspect the nearside front tyre advisory and quote for a matched pair if necessary.',
            'requested_start_at' => now()->addDays(9)->setTime(10, 30),
            'status' => 'confirmed',
            'quote_pence' => 8500,
        ]);
    }
}

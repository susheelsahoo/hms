<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Hotel\Models\Hotel;
use Modules\Organization\Models\Organization;
use Modules\Role\Models\Role;
use Modules\Room\Models\Room;
use Modules\Room\Models\RoomType;
use Modules\User\Models\User;

class DummyDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with dummy data.
     */
    public function run(): void
    {
        // Create organizations
        $organizations = $this->createOrganizations();

        // Create users for each organization
        foreach ($organizations as $organization) {
            $this->createUsersForOrganization($organization);
        }

        // Create hotels with rooms for each organization
        foreach ($organizations as $organization) {
            $this->createHotelsForOrganization($organization);
        }
    }

    /**
     * Create dummy organizations.
     */
    private function createOrganizations()
    {
        $organizations = [];

        $organizationData = [
            [
                'name' => 'Grand Hotel Enterprises',
                'email' => 'info@grandhote.com',
                'phone' => '+1-800-GRAND-01',
                'country' => 'US',
                'timezone' => 'America/New_York',
                'currency' => 'USD',
            ],
            [
                'name' => 'Luxury Resorts International',
                'email' => 'contact@luxuryresorts.com',
                'phone' => '+44-20-LUXURY-01',
                'country' => 'UK',
                'timezone' => 'Europe/London',
                'currency' => 'GBP',
            ],
            [
                'name' => 'Pacific Beach Hotels',
                'email' => 'reservations@pacificbeach.com',
                'phone' => '+61-2-PACIFIC-01',
                'country' => 'AU',
                'timezone' => 'Australia/Sydney',
                'currency' => 'AUD',
            ],
        ];

        foreach ($organizationData as $data) {
            $organization = Organization::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'slug' => \Illuminate\Support\Str::slug($data['name']),
                    'phone' => $data['phone'],
                    'country' => $data['country'],
                    'timezone' => $data['timezone'],
                    'currency' => $data['currency'],
                    'status' => 'active',
                    'metadata' => [
                        'founded_year' => now()->year - rand(5, 50),
                        'employee_count' => rand(50, 500),
                        'website' => 'https://example.com',
                    ],
                ]
            );
            $organizations[] = $organization;
        }

        return collect($organizations);
    }

    /**
     * Create users for organization.
     */
    private function createUsersForOrganization(Organization $organization): void
    {
        $roles = Role::all()->keyBy('slug');

        $usersData = [
            [
                'first_name' => 'John',
                'last_name' => 'Owner',
                'email' => "owner.{$organization->id}@hms.test",
                'role_slug' => Role::ORGANIZATION_OWNER,
                'phone' => '+1-555-0001',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Hotel',
                'email' => "admin.{$organization->id}@hms.test",
                'role_slug' => Role::HOTEL_ADMIN,
                'phone' => '+1-555-0002',
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'Operations',
                'email' => "manager.{$organization->id}@hms.test",
                'role_slug' => Role::HOTEL_MANAGER,
                'phone' => '+1-555-0003',
            ],
            [
                'first_name' => 'Front',
                'last_name' => 'Desk',
                'email' => "receptionist.{$organization->id}@hms.test",
                'role_slug' => Role::RECEPTIONIST,
                'phone' => '+1-555-0004',
            ],
            [
                'first_name' => 'Staff',
                'last_name' => 'Member',
                'email' => "staff.{$organization->id}@hms.test",
                'role_slug' => Role::STAFF,
                'phone' => '+1-555-0005',
            ],
            [
                'first_name' => 'Finance',
                'last_name' => 'Accountant',
                'email' => "accountant.{$organization->id}@hms.test",
                'role_slug' => Role::ACCOUNTANT,
                'phone' => '+1-555-0006',
            ],
        ];

        foreach ($usersData as $data) {
            User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'organization_id' => $organization->id,
                    'role_id' => $roles[$data['role_slug']]->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'metadata' => [
                        'department' => match ($data['role_slug']) {
                            Role::ORGANIZATION_OWNER => 'Executive',
                            Role::HOTEL_ADMIN => 'Management',
                            Role::HOTEL_MANAGER => 'Operations',
                            Role::RECEPTIONIST => 'Front Desk',
                            Role::ACCOUNTANT => 'Finance',
                            default => 'Operations',
                        },
                    ],
                ]
            );
        }
    }

    /**
     * Create hotels and rooms for organization.
     */
    private function createHotelsForOrganization(Organization $organization): void
    {
        $hotelsData = [
            [
                'name' => "{$organization->name} Downtown",
                'city' => 'New York',
                'state' => 'NY',
                'country' => $organization->country,
                'zip_code' => '10001',
                'address' => '123 Main Street',
                'phone' => '+1-212-555-0100',
                'email' => "downtown@{$organization->slug}.com",
                'rooms_count' => 150,
            ],
            [
                'name' => "{$organization->name} Airport",
                'city' => 'Newark',
                'state' => 'NJ',
                'country' => $organization->country,
                'zip_code' => '07114',
                'address' => '456 Airport Blvd',
                'phone' => '+1-973-555-0101',
                'email' => "airport@{$organization->slug}.com",
                'rooms_count' => 200,
            ],
            [
                'name' => "{$organization->name} Beach Resort",
                'city' => 'Miami',
                'state' => 'FL',
                'country' => $organization->country,
                'zip_code' => '33139',
                'address' => '789 Beach Avenue',
                'phone' => '+1-305-555-0102',
                'email' => "beach@{$organization->slug}.com",
                'rooms_count' => 300,
            ],
        ];

        foreach ($hotelsData as $data) {
            $hotel = Hotel::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'organization_id' => $organization->id,
                    'name' => $data['name'],
                    'slug' => \Illuminate\Support\Str::slug($data['name']),
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'country' => $data['country'],
                    'zip_code' => $data['zip_code'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'status' => 'active',
                    'checkin_time' => '15:00',
                    'checkout_time' => '11:00',
                    'metadata' => [
                        'rating' => rand(3, 5),
                        'amenities' => ['WiFi', 'Pool', 'Gym', 'Restaurant', 'Bar'],
                        'languages_spoken' => ['English', 'Spanish', 'French'],
                    ],
                ]
            );

            // Create rooms for hotel
            $this->createRoomsForHotel($hotel, $data['rooms_count']);
        }
    }

    /**
     * Create rooms for hotel.
     */
    private function createRoomsForHotel(Hotel $hotel, int $count): void
    {
        $roomTypeNames = ['Standard', 'Deluxe', 'Suite', 'Penthouse'];
        $roomFeatures = [
            'Air Conditioning',
            'WiFi',
            'TV',
            'Minibar',
            'Safe',
            'Bathrobe',
            'Hairdryer',
            'Work Desk',
        ];

        $roomsPerType = intval(ceil($count / count($roomTypeNames)));
        $roomTypeModels = [];

        // First create room types
        foreach ($roomTypeNames as $typeIndex => $typeName) {
            $basePrice = match ($typeName) {
                'Standard' => 100,
                'Deluxe' => 150,
                'Suite' => 250,
                'Penthouse' => 500,
                default => 100,
            };

            $roomType = \Modules\Room\Models\RoomType::query()->firstOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'slug' => \Illuminate\Support\Str::slug($typeName),
                ],
                [
                    'organization_id' => $hotel->organization_id,
                    'name' => $typeName,
                    'max_adults' => match ($typeName) {
                        'Standard' => 2,
                        'Deluxe' => 2,
                        'Suite' => 4,
                        'Penthouse' => 6,
                        default => 2,
                    },
                    'max_children' => 1,
                    'base_price' => $basePrice,
                    'bed_type' => ['Single', 'Double', 'Queen', 'King'][array_rand(['Single', 'Double', 'Queen', 'King'])],
                ]
            );
            $roomTypeModels[$typeName] = $roomType;
        }

        // Then create rooms for each type
        foreach ($roomTypeNames as $typeIndex => $typeName) {
            $roomType = $roomTypeModels[$typeName];
            $basePrice = $roomType->base_price;

            for ($i = 1; $i <= $roomsPerType; $i++) {
                $roomNumber = ($typeIndex * 100) + $i;
                $floor = intval(floor($roomNumber / 10));

                \Modules\Room\Models\Room::query()->firstOrCreate(
                    [
                        'hotel_id' => $hotel->id,
                        'room_number' => (string) $roomNumber,
                    ],
                    [
                        'organization_id' => $hotel->organization_id,
                        'room_type_id' => $roomType->id,
                        'floor_number' => (string) $floor,
                        'capacity' => $roomType->max_adults,
                        'price' => $basePrice + rand(-10, 50),
                        'status' => 'available',
                        'metadata' => [
                            'features' => array_slice($roomFeatures, 0, rand(4, 8)),
                            'square_feet' => rand(300, 800),
                        ],
                    ]
                );
            }
        }
    }
}

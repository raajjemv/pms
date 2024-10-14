<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessSource;

class BusinessSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Data to insert
        $businessSources = [
            [
                'id' => 1,
                'tenant_id' => null,
                'name' => 'Agoda',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:11',
                'updated_at' => '2024-09-17 14:05:11',
            ],
            [
                'id' => 2,
                'tenant_id' => null,
                'name' => 'Booking.com',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:25',
                'updated_at' => '2024-09-17 14:05:25',
            ],
            [
                'id' => 3,
                'tenant_id' => null,
                'name' => 'Trip.com',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 4,
                'tenant_id' => null,
                'name' => 'Expedia',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:20',
                'updated_at' => '2024-09-19 10:28:20',
            ],
            [
                'id' => 5,
                'tenant_id' => null,
                'name' => 'Airbnb',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:20',
                'updated_at' => '2024-09-19 10:28:20',
            ],
            [
                'id' => 6,
                'tenant_id' => null,
                'name' => 'Tripadvisor',
                'business_registration' => null,
                'type' => 'ota',
                'locked' => 1,
                'user_id' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:20',
                'updated_at' => '2024-09-19 10:28:20',
            ],
        ];

        // Insert records
        BusinessSource::insert($businessSources);
    }
}

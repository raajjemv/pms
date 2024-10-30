<?php

namespace Database\Seeders;

use App\Models\VoidReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoidReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessSources = [
            [
                'id' => 1,
                'tenant_id' => null,
                'reason' => 'Duplicate Reservation',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:11',
                'updated_at' => '2024-09-17 14:05:11',
            ],
            [
                'id' => 2,
                'tenant_id' => null,
                'name' => 'Guest Cancellation',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:25',
                'updated_at' => '2024-09-17 14:05:25',
            ],
            [
                'id' => 3,
                'tenant_id' => null,
                'name' => 'Guest No-Show',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
        ];

        // Insert records
        VoidReason::withoutGlobalScopes()->insert($businessSources);
    }
}

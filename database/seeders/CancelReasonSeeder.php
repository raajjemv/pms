<?php

namespace Database\Seeders;

use App\Models\CancelReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CancelReasonSeeder extends Seeder
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
                'reason' => 'Guest Cancelled',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:11',
                'updated_at' => '2024-09-17 14:05:11',
            ],
            [
                'id' => 2,
                'tenant_id' => null,
                'name' => 'No-Show',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-17 14:05:25',
                'updated_at' => '2024-09-17 14:05:25',
            ],
            [
                'id' => 3,
                'tenant_id' => null,
                'name' => 'Overbooked',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 4,
                'tenant_id' => null,
                'name' => 'Property Closed',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 5,
                'tenant_id' => null,
                'name' => 'Force Majeure',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 6,
                'tenant_id' => null,
                'name' => 'Hotel Policy Violation',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 7,
                'tenant_id' => null,
                'name' => 'Distruptive Behavior',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
            [
                'id' => 8,
                'tenant_id' => null,
                'name' => 'Payment Issue',
                'user_id' => 1,
                'locked' => 1,
                'deleted_at' => null,
                'created_at' => '2024-09-19 10:28:11',
                'updated_at' => '2024-09-19 10:28:11',
            ],
        ];

        // Insert records
        CancelReason::withoutGlobalScopes()->insert($businessSources);
    }
}

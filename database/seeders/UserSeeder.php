<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Samaah',
            'email' => 'imsamaah@gmail.com',
            'password' => Hash::make('Samaah@123'),
        ]);

//         $tenant = Tenant::create([
//             'name' => 'Maldives Stay',
//             'email' => 'imsamaah@gmail.com',
//             'phone_number' => '9607779967',
//             'owner_id' => $user->id,
//             'user_id' => $user->id
//         ]);
// 
//         $user->tenants()->attach($tenant);
// 
//         setPermissionsTeamId($tenant->id);

        $user->assignRole('admin');
    }
}

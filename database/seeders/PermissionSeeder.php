<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin_role = Role::create(['name' => 'admin']);
        $admin_role = Role::create(['name' => 'tenant_owner']);
        $admin_role = Role::create(['name' => 'front_desk']);
        $admin_role = Role::create(['name' => 'housekeeping']);
        $admin_role = Role::create(['name' => 'revenue_manager']);
        $admin_role = Role::create(['name' => 'sales_manager']);
        
    }
}

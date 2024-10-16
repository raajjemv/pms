<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TenantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return true;
        return $user->allRoles()->whereName('admin')->exists() || $user->hasRole('tenant_owner|hotel_manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        // return true;

        return $user->allRoles()->whereName('admin')->exists() || $user->hasRole('tenant_owner|hotel_manager');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->allRoles()->whereName('admin')->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tenant $tenant): bool
    {

        return $user->allRoles()->whereName('admin')->exists() || $user->hasRole('tenant_owner|hotel_manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): bool
    {

        return $user->allRoles()->whereName('admin')->exists() ;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $tenant): bool
    {

        return $user->allRoles()->whereName('admin')->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $tenant): bool
    {
        //
    }
}

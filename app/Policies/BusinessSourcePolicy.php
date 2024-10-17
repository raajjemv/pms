<?php

namespace App\Policies;

use App\Models\BusinessSource;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessSourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BusinessSource $businessSource): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BusinessSource $businessSource): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BusinessSource $businessSource): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BusinessSource $businessSource): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BusinessSource $businessSource): bool
    {
        //
    }
}

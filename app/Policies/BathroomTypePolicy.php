<?php

namespace App\Policies;

use App\Models\BathroomType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BathroomTypePolicy
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
    public function view(User $user, BathroomType $bathroomType): bool
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
    public function update(User $user, BathroomType $bathroomType): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BathroomType $bathroomType): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BathroomType $bathroomType): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BathroomType $bathroomType): bool
    {
        //
    }
}

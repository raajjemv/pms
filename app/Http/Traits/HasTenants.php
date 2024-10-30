<?php

namespace App\Http\Traits;

use App\Models\Tenant;

trait HasTenants
{
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot(['tenant_id'])
            ->withTimestamps();
    }

    public function ownedTenants()
    {
        return $this->hasMany(Tenant::class, 'owner_id', 'id');
    }

    public function defaultTenant()
    {
        return $this->tenants->first();
    }

    public function currentTenant()
    {
        if (is_null($this->current_tenant_id) && $this->id) {
            $this->switchTenant($this->defaultTenant());
        }
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function switchTenant($tenant)
    {

        $this->forceFill([
            'current_tenant_id' => $tenant->id,
        ])->save();

        $this->setRelation('currentTenant', $tenant);

        return true;
    }
}

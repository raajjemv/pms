<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantPivot extends Pivot
{
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

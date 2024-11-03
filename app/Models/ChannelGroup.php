<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([TenantScope::class])]
class ChannelGroup extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->withPivot(['tenant_id','token']);
    }
}

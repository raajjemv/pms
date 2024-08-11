<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([TenantScope::class])]
class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function rates()
    {
        return $this->hasMany(RoomTypeRate::class);
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function ratePlans()
    {
        return $this->belongsToMany(RatePlan::class);
    }
}

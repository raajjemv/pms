<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\HasCurrentTenantLabel;

class Tenant extends Model implements HasCurrentTenantLabel
{
    use HasFactory, SoftDeletes;

    protected $guarded  = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
   
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function getCurrentTenantLabel(): string
    {
        return 'Current Tenant';
    }
}

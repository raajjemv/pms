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
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }
    public function channelGroups()
    {
        return $this->hasMany(ChannelGroup::class);
    }
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }
    public function roomViews()
    {
        return $this->hasMany(RoomView::class);
    }
    public function roomStatuses()
    {
        return $this->hasMany(RoomStatus::class);
    }
    public function roomClasses()
    {
        return $this->hasMany(RoomClass::class);
    }
    public function bedTypes()
    {
        return $this->hasMany(BedType::class);
    }
    public function bathroomTypes()
    {
        return $this->hasMany(BathroomType::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function ratePlans()
    {
        return $this->hasMany(RatePlan::class);
    }
    public function roomTypeRates()
    {
        return $this->hasMany(RoomTypeRate::class);
    }
    public function getCurrentTenantLabel(): string
    {
        return 'Current Tenant';
    }
}

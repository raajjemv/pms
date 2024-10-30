<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model implements HasCurrentTenantLabel
{
    use HasFactory, SoftDeletes;

    protected $guarded  = [];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['tenant_id'])
            ->withTimestamps();
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
    public function folioOperationCharges()
    {
        return $this->hasMany(FolioOperationCharge::class);
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
    public function businessSources()
    {
        return $this->hasMany(BusinessSource::class);
    }
    public function voidReasons()
    {
        return $this->hasMany(VoidReason::class);
    }
    public function getCurrentTenantLabel(): string
    {
        return 'Current Tenant';
    }

    protected function casts(): array
    {
        return [
            'currencies' => 'array',
            'check_in_time' => TimeCast::class,
            'check_out_time' => TimeCast::class,
            'late_check_out_time' => TimeCast::class
        ];
    }
}

<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([TenantScope::class])]
class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function roomClass()
    {
        return $this->belongsTo(RoomClass::class);
    }

    public function bedType()
    {
        return $this->belongsTo(BedType::class);
    }

    public function bathroomType()
    {
        return $this->belongsTo(BathroomType::class);
    }

    public function roomView()
    {
        return $this->belongsTo(RoomView::class);
    }

    public function status()
    {
        return $this->belongsTo(RoomStatus::class, 'room_status_id', 'id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bookingReservations()
    {
        return $this->hasMany(BookingReservation::class);
    }


    protected function casts(): array
    {
        return [
            'smoking' => 'boolean',
            'amenities' => 'array'
        ];
    }
}

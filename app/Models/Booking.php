<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)
            ->withDefault(function (Customer $customer, Booking $booking) {
                $customer->name = $booking->booking_customer;
                $customer->country = '-';
            });
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bookingNights()
    {
        return $this->hasMany(BookingNight::class);
    }

    public function averageRate()
    {
        return $this->bookingNights->avg('rate');
    }

    protected function casts(): array
    {
        return [
            'from' => 'date',
            'to' => 'date'
        ];
    }
}

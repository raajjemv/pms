<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\BookingType;
use App\Enums\PaymentStatus;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TenantScope::class])]
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

    public function customers()
    {
        return $this->belongsToMany(Customer::class)
            ->withTimestamps()
            ->withPivot('booking_reservation_id', 'master');
    }



    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


    public function bookingTransactions()
    {
        return $this->hasMany(BookingTransaction::class);
    }

    public function bookingReservations()
    {
        return $this->hasMany(BookingReservation::class);
    }



    protected function casts(): array
    {
        return [
            'from' => 'date',
            'to' => 'date',
            'booking_type' => BookingType::class,

        ];
    }
    protected $casts = [];
}

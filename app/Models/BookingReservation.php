<?php

namespace App\Models;

use App\Enums\Status;
use App\Casts\TimeCast;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingReservation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class)
            ->withDefault(function (Customer $customer, BookingReservation $reservation) {
                $customer->name = $reservation->booking_customer;
                $customer->country = '-';
            });
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class)
            ->withTimestamps()
            ->withPivot('booking_reservation_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }


    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingTransactions()
    {
        return $this->hasMany(BookingTransaction::class);
    }

    public function averageRate()
    {
        return $this->bookingTransactions->where('transaction_type', 'room_charge')->avg('rate');
    }

    public function totalCharges()
    {
        return $this->bookingTransactions->whereNotIn('transaction_type', PaymentType::getAllValues())->sum('rate');
    }

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function totalPax()
    {
        return $this->adults + $this->children;
    }


    protected function casts(): array
    {
        return [
            'check_in' => TimeCast::class,
            'check_out' => TimeCast::class,
            'from' => 'date',
            'to' => 'date',
            'status' => Status::class,
            'payment_status' => PaymentStatus::class

        ];
    }
}

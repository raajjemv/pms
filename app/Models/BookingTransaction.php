<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function businessSource()
    {
        return $this->belongsTo(BusinessSource::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d'
        ];
    }
}

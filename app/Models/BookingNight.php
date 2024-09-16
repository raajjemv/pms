<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingNight extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d'
        ];
    }
}

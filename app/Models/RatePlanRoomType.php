<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RatePlanRoomType extends Pivot
{
    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}

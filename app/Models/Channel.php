<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $guarded = [];

    
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function channelGroups()
    {
        return $this->belongsToMany(ChannelGroup::class);
    }
}

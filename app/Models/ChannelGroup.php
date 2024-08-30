<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelGroup extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->withPivot(['tenant_id','token']);
    }
}

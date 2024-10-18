<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([TenantScope::class])]
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->belongsTo(Booking::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function searchableLabel(): Attribute
    {
        return Attribute::get(fn() => "{$this->name} - {$this->document_number}");
    }
}

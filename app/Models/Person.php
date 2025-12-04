<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = [
        'name',
    ];

    public function ownedSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'owner_id');
    }

    public function subscriptionMemberships(): HasMany
    {
        return $this->hasMany(SubscriptionMember::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MemberPayment::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'service_name',
        'billing_period',
        'default_amount_eur',
        'owner_id',
        'started_on',
        'ended_on',
        'notes',
    ];

    protected $casts = [
        'default_amount_eur' => 'decimal:2',
        'started_on' => 'date',
        'ended_on'   => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(SubscriptionMember::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(SubscriptionCharge::class);
    }
}

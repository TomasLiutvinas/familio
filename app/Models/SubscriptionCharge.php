<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionCharge extends Model
{
    protected $fillable = [
        'subscription_id',
        'period_year',
        'charge_date',
        'amount_eur',
        'notes',
    ];

    protected $casts = [
        'charge_date' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MemberPayment::class, 'charge_id');
    }
}

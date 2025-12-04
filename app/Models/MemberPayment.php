<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPayment extends Model
{
    protected $fillable = [
        'charge_id',
        'person_id',
        'amount_eur',
        'paid_on',
        'notes',
    ];

    protected $casts = [
        'paid_on' => 'date',
    ];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(SubscriptionCharge::class, 'charge_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

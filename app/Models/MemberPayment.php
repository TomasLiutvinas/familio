<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

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

    protected static function booted(): void
    {
        static::saving(function (self $payment): void {
            if (SubscriptionCharge::find($payment->charge_id)?->is_planned) {
                throw ValidationException::withMessages(['charge_id' => 'Activate this planned charge before recording a payment.']);
            }
        });
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(SubscriptionCharge::class, 'charge_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

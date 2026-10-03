<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionCharge extends Model
{
    protected $fillable = [
        'subscription_id',
        'period_year',
        'charge_date',
        'covered_from',
        'covered_until',
        'amount_eur',
        'notes',
    ];

    protected $casts = [
        'charge_date' => 'date',
        'covered_from' => 'date',
        'covered_until' => 'date',
    ];

    public function coverageStart(): CarbonImmutable
    {
        return $this->covered_from
            ? CarbonImmutable::instance($this->covered_from)
            : CarbonImmutable::create($this->period_year, $this->subscription->started_on->month, 1);
    }

    public function coverageEnd(): CarbonImmutable
    {
        return $this->covered_until
            ? CarbonImmutable::instance($this->covered_until)
            : $this->coverageStart()->addMonths(11)->endOfMonth()->startOfDay();
    }

    public function coverageLabel(): string
    {
        return $this->coverageStart()->format('M Y').' – '.$this->coverageEnd()->format('M Y');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MemberPayment::class, 'charge_id');
    }
}

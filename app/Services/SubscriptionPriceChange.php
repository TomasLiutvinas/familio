<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscriptionPriceChange
{
    /** Annual collections retain already paid months, using the subscription anniversary. */
    public function preview(Subscription $subscription, string $effectiveFrom, float $monthlyPrice): array
    {
        if ($subscription->billing_period !== 'yearly' || $monthlyPrice <= 0 || ! is_finite($monthlyPrice)) {
            throw ValidationException::withMessages(['monthly_price' => 'Choose an annual subscription and a positive monthly price.']);
        }
        $effective = CarbonImmutable::parse($effectiveFrom)->startOfDay();
        if ($effective->day !== 1) {
            throw ValidationException::withMessages(['effective_from' => 'Use the first day of the month the new price starts.']);
        }
        if ($effective->lt(CarbonImmutable::parse($subscription->started_on)->startOfMonth())) {
            throw ValidationException::withMessages(['effective_from' => 'The price change cannot precede the subscription.']);
        }
        $newMonthlyCents = (int) round($monthlyPrice * 100);
        $oldAnnualCents = (int) round((float) $subscription->default_amount_eur * 100);
        $oldMonthlyCents = (int) round($oldAnnualCents / 12);
        $changes = [];
        foreach ($subscription->charges as $charge) {
            $start = CarbonImmutable::create($charge->period_year, $subscription->started_on->month, 1);
            $months = 0;
            for ($month = 0; $month < 12; $month++) {
                if ($start->addMonths($month)->gte($effective)) {
                    $months++;
                }
            }
            $before = (int) round((float) $charge->amount_eur * 100);
            $after = $before + $months * ($newMonthlyCents - $oldMonthlyCents);
            if ($after < 0) {
                throw ValidationException::withMessages(['monthly_price' => 'This adjustment would make an annual charge negative. Edit that charge directly instead.']);
            }
            if ($before !== $after) {
                $changes[] = ['id' => $charge->id, 'year' => $charge->period_year, 'months' => $months,
                    'before_cents' => $before, 'after_cents' => $after];
            }
        }

        return ['monthly_cents' => $newMonthlyCents, 'annual_cents' => $newMonthlyCents * 12,
            'effective_from' => $effective->toDateString(), 'changes' => $changes];
    }

    public function apply(Subscription $subscription, string $effectiveFrom, float $monthlyPrice): array
    {
        return DB::transaction(function () use ($subscription, $effectiveFrom, $monthlyPrice) {
            $subscription->refresh()->load('charges');
            $preview = $this->preview($subscription, $effectiveFrom, $monthlyPrice);
            foreach ($preview['changes'] as $change) {
                $charge = $subscription->charges->firstWhere('id', $change['id']);
                $note = sprintf('Price change from %s: €%.2f/month; %d affected months; annual collection €%.2f → €%.2f.',
                    $preview['effective_from'], $preview['monthly_cents'] / 100, $change['months'],
                    $change['before_cents'] / 100, $change['after_cents'] / 100);
                $charge->update(['amount_eur' => $change['after_cents'] / 100,
                    'notes' => trim(($charge->notes ?? '')."\n".$note)]);
            }
            $subscription->update(['default_amount_eur' => $preview['annual_cents'] / 100]);

            return $preview;
        });
    }
}

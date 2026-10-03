<?php

namespace App\Services;

use App\Models\SubscriptionCharge;
use Illuminate\Support\Collection;

class ChargeBalances
{
    /** Exact cents: keep partial payments outstanding and allocate remainder cents once. */
    public function forCharge(SubscriptionCharge $charge): Collection
    {
        $charge->loadMissing(['subscription.members.person', 'payments']);
        $members = $charge->subscription?->members->sortBy('person_id')->values() ?? collect();
        if ($members->isEmpty()) {
            return collect();
        }

        $total = (int) round((float) $charge->amount_eur * 100);
        $share = intdiv($total, $members->count());
        $remainder = $total % $members->count();
        $payments = $charge->payments->groupBy('person_id');

        return $members->mapWithKeys(function ($member, $index) use ($charge, $share, $remainder, $payments) {
            $due = $share + ($index < $remainder ? 1 : 0);
            $paid = $payments->get($member->person_id, collect())->sum(
                fn ($payment) => (int) round((float) $payment->amount_eur * 100)
            );
            $isOwner = $member->person_id === $charge->subscription->owner_id;

            return [$member->person_id => [
                'person' => $member->person,
                'due_cents' => $due,
                'paid_cents' => $isOwner ? max($due, $paid) : $paid,
                'outstanding_cents' => $isOwner ? 0 : max(0, $due - $paid),
            ]];
        });
    }

    public function pendingByPerson(): Collection
    {
        $pending = collect();
        $charges = SubscriptionCharge::with(['subscription.members.person', 'payments'])->get();
        foreach ($charges as $charge) {
            foreach ($this->forCharge($charge) as $personId => $balance) {
                if ($balance['outstanding_cents'] === 0) {
                    continue;
                }
                $person = $pending->get($personId, ['total_cents' => 0, 'charges' => []]);
                $person['total_cents'] += $balance['outstanding_cents'];
                $person['charges'][] = [
                    'charge_id' => $charge->id,
                    'label' => $charge->subscription->service_name.' · '.$charge->coverageLabel(),
                    'outstanding_cents' => $balance['outstanding_cents'],
                ];
                $pending->put($personId, $person);
            }
        }

        return $pending;
    }
}

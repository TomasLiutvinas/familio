<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\SubscriptionCharge;
use App\Models\SubscriptionMember;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ActiveSubscriptionsCharts extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = "full";

    protected function getColumns(): int
    {
        $count = count($this->getStats());
        return min($count, 4); // Max 4 columns, adapts to fewer subscriptions
    }

    protected function getStats(): array
    {
        $stats = [];

        // Get all active subscriptions
        $subscriptions = Subscription::query()
            ->where(function ($query) {
                $query->whereNull("ended_on")->orWhere("ended_on", ">=", now());
            })
            ->orderBy("service_name")
            ->get();

        if ($subscriptions->isEmpty()) {
            return $stats;
        }

        $subscriptionIds = $subscriptions->pluck("id");

        // Get member counts for all subscriptions in one query
        $memberCounts = SubscriptionMember::query()
            ->whereIn("subscription_id", $subscriptionIds)
            ->select("subscription_id", DB::raw("COUNT(*) as member_count"))
            ->groupBy("subscription_id")
            ->pluck("member_count", "subscription_id");

        // Get most recent year with charge data for each subscription
        $mostRecentCharges = SubscriptionCharge::query()
            ->whereIn("subscription_id", $subscriptionIds)
            ->select(
                "subscription_id",
                DB::raw("MAX(period_year) as recent_year"),
            )
            ->groupBy("subscription_id")
            ->pluck("recent_year", "subscription_id");

        // Get all charge data for current and previous years in one query
        $relevantYears = $mostRecentCharges
            ->flatMap(fn($year) => [$year, $year - 1])
            ->unique()
            ->values();

        $charges = SubscriptionCharge::query()
            ->whereIn("subscription_id", $subscriptionIds)
            ->whereIn("period_year", $relevantYears)
            ->select(
                "subscription_id",
                "period_year",
                DB::raw("SUM(amount_eur) as total_cost"),
            )
            ->groupBy("subscription_id", "period_year")
            ->get()
            ->groupBy("subscription_id");

        // Build stats for each subscription
        foreach ($subscriptions as $subscription) {
            $recentYear = $mostRecentCharges->get($subscription->id);

            if (!$recentYear) {
                continue;
            }

            // Get charge data for this subscription
            $subscriptionCharges = $charges->get($subscription->id, collect());

            $recentYearCost =
                $subscriptionCharges->where("period_year", $recentYear)->first()
                    ->total_cost ?? 0;

            if ($recentYearCost <= 0) {
                continue;
            }

            $memberCount = $memberCounts->get($subscription->id, 0);
            $costPerPersonPerMonth =
                $memberCount > 0 ? $recentYearCost / 12 / $memberCount : 0;

            // Get previous year data for comparison
            $previousYear = $recentYear - 1;
            $previousYearCost =
                $subscriptionCharges
                    ->where("period_year", $previousYear)
                    ->first()->total_cost ?? 0;
            $previousCostPerPersonPerMonth =
                $memberCount > 0 ? $previousYearCost / 12 / $memberCount : 0;

            // Calculate change
            if ($previousCostPerPersonPerMonth > 0) {
                $costChange =
                    (($costPerPersonPerMonth - $previousCostPerPersonPerMonth) /
                        $previousCostPerPersonPerMonth) *
                    100;
                $description = sprintf(
                    "%s%.1f%% vs %d",
                    $costChange >= 0 ? "+" : "",
                    $costChange,
                    $previousYear,
                );
                $descriptionIcon =
                    $costChange >= 0
                        ? "heroicon-m-arrow-trending-up"
                        : "heroicon-m-arrow-trending-down";
                $color = $costChange >= 0 ? "warning" : "success";
            } else {
                $description = "No previous data";
                $descriptionIcon = null;
                $color = "gray";
            }

            $stat = Stat::make(
                $subscription->service_name . " (per month)",
                "€" . number_format($costPerPersonPerMonth, 2),
            )
                ->description($description)
                ->color($color);

            if ($descriptionIcon) {
                $stat->descriptionIcon($descriptionIcon);
            }

            $stats[] = $stat;
        }

        return $stats;
    }
}

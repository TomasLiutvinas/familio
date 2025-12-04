<?php

namespace App\Filament\Widgets;

use App\Models\MemberPayment;
use App\Models\Person;
use App\Models\Subscription;
use App\Models\SubscriptionCharge;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FamilioStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        // Active subscriptions
        $activeSubscriptions = Subscription::query()
            ->where(function ($query) {
                $query->whereNull("ended_on")->orWhere("ended_on", ">=", now());
            })
            ->count();

        // Total unpaid balance (charges - payments)
        $totalCharges = SubscriptionCharge::sum("amount_eur");
        $totalPayments = MemberPayment::sum("amount_eur");
        $unpaidBalance = $totalCharges - $totalPayments;

        // Annual costs this year vs last year
        $costsThisYear = SubscriptionCharge::where(
            "period_year",
            $currentYear,
        )->sum("amount_eur");
        $costsLastYear = SubscriptionCharge::where(
            "period_year",
            $lastYear,
        )->sum("amount_eur");

        $costChange =
            $costsLastYear > 0
                ? (($costsThisYear - $costsLastYear) / $costsLastYear) * 100
                : 0;

        // Total family members
        $totalPeople = Person::count();

        return [
            Stat::make("Active Subscriptions", $activeSubscriptions)
                ->description("Currently running")
                ->descriptionIcon("heroicon-m-play-circle")
                ->color("success"),

            Stat::make("Unpaid Balance", "€" . number_format($unpaidBalance, 2))
                ->description(
                    $unpaidBalance > 0
                        ? "Still needs to be collected"
                        : "All settled up!",
                )
                ->descriptionIcon(
                    $unpaidBalance > 0
                        ? "heroicon-m-exclamation-triangle"
                        : "heroicon-m-check-circle",
                )
                ->color($unpaidBalance > 0 ? "danger" : "success"),

            Stat::make(
                "Annual Costs {$currentYear}",
                "€" . number_format($costsThisYear, 2),
            )
                ->description(
                    sprintf(
                        "%s%.1f%% vs {$lastYear}",
                        $costChange >= 0 ? "+" : "",
                        abs($costChange),
                    ),
                )
                ->descriptionIcon(
                    $costChange >= 0
                        ? "heroicon-m-arrow-trending-up"
                        : "heroicon-m-arrow-trending-down",
                )
                ->color("warning"),

            Stat::make("Family Members", $totalPeople)
                ->description("Total people 🦎")
                ->color("success"),
        ];
    }
}

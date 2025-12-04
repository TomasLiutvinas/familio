<?php

namespace App\Filament\Resources\SubscriptionCharges\Tables;

use App\Models\SubscriptionCharge;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionChargesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscription.service_name')
                    ->label('Subscription')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('period_year')
                    ->label('Year')
                    ->sortable(),

                // total charge
                TextColumn::make('amount_eur')
                    ->label('Price (€)')
                    ->numeric(2)
                    ->sortable(),

                // per-person amount
                TextColumn::make('amount_per_person_eur')
                    ->label('Per person (€)')
                    ->getStateUsing(function (SubscriptionCharge $record): string {
                        $subscription = $record->subscription;

                        if (! $subscription) {
                            return '–';
                        }

                        $membersCount = $subscription->members?->count() ?? 0;
                        if ($membersCount <= 0) {
                            return '–';
                        }

                        $perPerson = (float) $record->amount_eur / $membersCount;

                        return number_format($perPerson, 2, '.', '');
                    })
                    ->sortable(),

                TextColumn::make('members_paid')
                    ->label('Members paid')
                    ->getStateUsing(function (SubscriptionCharge $record): string {
                        $subscription = $record->subscription;
                        if (! $subscription) {
                            return '0 / 0';
                        }

                        $members = $subscription->members ?? collect();
                        $memberIds = $members->pluck('person_id')->filter()->unique();
                        $totalMembers = $memberIds->count();

                        if ($totalMembers === 0) {
                            return '0 / 0';
                        }

                        // start with people who have payments
                        $paidIds = $record->payments
                            ->pluck('person_id')
                            ->filter();

                        // owner is always counted as paid if they are in the members list
                        if ($subscription->owner_id) {
                            $paidIds->push($subscription->owner_id);
                        }

                        $paidIds = $paidIds->unique();

                        $paidCount = $memberIds->intersect($paidIds)->count();

                        return sprintf('%d / %d', $paidCount, $totalMembers);
                    }),

                TextColumn::make('unpaid_members')
                    ->label('Unpaid members')
                    ->getStateUsing(function (SubscriptionCharge $record): string {
                        $subscription = $record->subscription;
                        if (! $subscription) {
                            return '–';
                        }

                        $members = $subscription->members ?? collect();
                        $memberIds = $members->pluck('person_id')->filter()->unique();

                        if ($memberIds->isEmpty()) {
                            return '–';
                        }

                        // start with people who have payments
                        $paidIds = $record->payments
                            ->pluck('person_id')
                            ->filter();

                        // owner is always counted as paid if they are in the members list
                        if ($subscription->owner_id) {
                            $paidIds->push($subscription->owner_id);
                        }

                        $paidIds = $paidIds->unique();

                        $unpaidIds = $memberIds->diff($paidIds);

                        if ($unpaidIds->isEmpty()) {
                            return '–';
                        }

                        $unpaidNames = $members
                            ->filter(fn($m) => $unpaidIds->contains($m->person_id))
                            ->map(fn($m) => $m->person?->name ?? '???')
                            ->filter()
                            ->values()
                            ->all();

                        if (empty($unpaidNames)) {
                            return '–';
                        }

                        return implode(', ', $unpaidNames);
                    }),

                TextColumn::make('charge_date')
                    ->label('Charge date')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

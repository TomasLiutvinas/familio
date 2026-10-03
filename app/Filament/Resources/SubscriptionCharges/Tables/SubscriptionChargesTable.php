<?php

namespace App\Filament\Resources\SubscriptionCharges\Tables;

use App\Models\SubscriptionCharge;
use App\Services\ChargeBalances;
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
            ->modifyQueryUsing(fn ($query) => $query->with(['subscription.members.person', 'payments']))
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
                        $balances = app(ChargeBalances::class)->forCharge($record);

                        return sprintf('%d / %d', $balances->where('outstanding_cents', 0)->count(), $balances->count());
                    }),

                TextColumn::make('unpaid_members')
                    ->label('Unpaid members')
                    ->getStateUsing(function (SubscriptionCharge $record): string {
                        $names = app(ChargeBalances::class)->forCharge($record)
                            ->filter(fn ($balance) => $balance['outstanding_cents'] > 0)
                            ->map(fn ($balance) => sprintf('%s (€%.2f)', $balance['person']?->name ?? 'Unknown', $balance['outstanding_cents'] / 100));

                        return $names->isEmpty() ? '–' : $names->implode(', ');
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

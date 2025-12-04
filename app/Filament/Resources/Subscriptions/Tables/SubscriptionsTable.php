<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Subscription;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // avoid N+1 and weird recursion by eager-loading what we need
            ->modifyQueryUsing(
                fn ($query) => $query->with(['owner', 'members.person'])
            )
            ->columns([
                TextColumn::make('service_name')
                    ->label('Subscription')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('billing_period')
                    ->label('Period')
                    ->sortable(),

                TextColumn::make('default_amount_eur')
                    ->label('Price (€)')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable(),

                // count of members
                TextColumn::make('members_count')
                    ->label('Members')
                    ->getStateUsing(function (Subscription $record): string {
                        return (string) $record->members->count();
                    }),

                // names of members
                TextColumn::make('members_names')
                    ->label('People')
                    ->getStateUsing(function (Subscription $record): string {
                        $names = $record->members
                            ->map(fn ($m) => $m->person?->name ?? null)
                            ->filter()
                            ->values()
                            ->all();

                        if (empty($names)) {
                            return '–';
                        }

                        return implode(', ', $names);
                    })
                    ->wrap(),

                TextColumn::make('started_on')
                    ->date()
                    ->sortable(),

                TextColumn::make('ended_on')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

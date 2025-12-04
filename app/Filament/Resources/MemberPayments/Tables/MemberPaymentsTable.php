<?php

namespace App\Filament\Resources\MemberPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('charge.subscription.service_name')
                    ->label('Subscription')
                    ->sortable()
                    ->searchable(),

                // year of the charge
                TextColumn::make('charge.period_year')
                    ->label('Year')
                    ->sortable(),

                TextColumn::make('person.name')
                    ->label('Person')
                    ->searchable(),

                TextColumn::make('amount_eur')
                    ->label('Price (€)')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('paid_on')
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

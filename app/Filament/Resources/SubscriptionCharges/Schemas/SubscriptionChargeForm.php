<?php

namespace App\Filament\Resources\SubscriptionCharges\Schemas;

use App\Models\Subscription;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionChargeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription_id')
                    ->relationship('subscription', 'service_name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        $subscription = $state ? Subscription::find($state) : null;
                        $set('amount_eur', $subscription?->default_amount_eur);
                    }),
                TextInput::make('period_year')
                    ->default(now()->year)
                    ->helperText('Year this collection starts; annual periods use the subscription’s anniversary month.')
                    ->required()
                    ->numeric(),
                DatePicker::make('charge_date')
                    ->label('Charge date:')
                    ->default(fn () => now())
                    ->required(),
                TextInput::make('amount_eur')
                    ->label('Price (€)')
                    ->numeric()
                    ->step('0.01')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}

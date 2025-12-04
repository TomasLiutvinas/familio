<?php

namespace App\Filament\Resources\SubscriptionCharges\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubscriptionChargeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription_id')
                    ->relationship('subscription', 'service_name')
                    ->required(),
                TextInput::make('period_year')
                    ->required()
                    ->numeric(),
                DatePicker::make('charge_date')
                    ->label('Charge date:')
                    ->default(fn() => now())
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

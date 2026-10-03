<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('service_name')
                    ->required(),
                Select::make('billing_period')
                    ->label('Billing period')
                    ->options([
                        'monthly' => 'Monthly',
                        'montly' => 'Monthly (legacy)',
                        'yearly' => 'Yearly',
                    ])
                    ->default(fn () => 'yearly')
                    ->required(),
                TextInput::make('default_amount_eur')
                    ->label('Default price per billing period (€)')
                    ->helperText('For annual subscriptions, use Change monthly price to adjust an existing year.')
                    ->numeric()
                    ->step('0.01')
                    ->required(),
                Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->default(fn () => 1)
                    ->required(),
                DatePicker::make('started_on')
                    ->label('Started on')
                    ->default(fn () => now())
                    ->required(),
                DatePicker::make('ended_on'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}

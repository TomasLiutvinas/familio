<?php

namespace App\Filament\Resources\SubscriptionCharges\Schemas;

use App\Models\Subscription;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                Toggle::make('is_planned')->label('Planned — excluded from calculations')
                    ->default(false)
                    ->helperText('Keep future charges visible without collecting them yet. Activate when you want them included.'),
                TextInput::make('period_year')
                    ->default(now()->year)
                    ->helperText('The year the covered period starts.')
                    ->required()
                    ->numeric(),
                DatePicker::make('covered_from')
                    ->label('Covered from')->nullable()->requiredWith('covered_until')
                    ->helperText('Optional for old annual charges; set both dates for a shorter or calendar-year collection.')
                    ->rules([fn () => function (string $attribute, $value, $fail): void {
                        if ($value && CarbonImmutable::parse($value)->day !== 1) {
                            $fail('Coverage must start on the first day of a month.');
                        }
                    }]),
                DatePicker::make('covered_until')
                    ->label('Covered through')->nullable()->requiredWith('covered_from')
                    ->afterOrEqual('covered_from')
                    ->rules([fn () => function (string $attribute, $value, $fail): void {
                        if ($value && ! CarbonImmutable::parse($value)->isLastOfMonth()) {
                            $fail('Coverage must end on the last day of a month.');
                        }
                    }]),
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

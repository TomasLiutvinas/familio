<?php

namespace App\Filament\Resources\MemberPayments\Schemas;

use App\Models\SubscriptionCharge;
use App\Services\ChargeBalances;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MemberPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('charge_id')
                    ->relationship(
                        name: 'charge',
                        titleAttribute: 'charge_date',
                        modifyQueryUsing: fn ($query) => $query
                            ->with('subscription')
                            ->orderByDesc('period_year'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (SubscriptionCharge $record) => sprintf(
                            '%d – %s (€%s)',
                            $record->period_year,
                            $record->subscription?->service_name ?? '???',
                            number_format($record->amount_eur, 2, '.', '')
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->label('Charge')
                    ->live()
                    ->required()
                    ->afterStateUpdated(fn (Get $get, callable $set) => self::suggestRemaining($get, $set)),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, callable $set) => self::suggestRemaining($get, $set)),

                TextInput::make('amount_eur')
                    ->label('Payment (€)')
                    ->helperText('Suggested amount is this person’s remaining share; you can record a partial payment.')
                    ->numeric()
                    ->step('0.01')
                    ->required(),

                DatePicker::make('paid_on')
                    ->default(fn () => now())
                    ->required(),

                Textarea::make('notes')
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    private static function suggestRemaining(Get $get, callable $set): void
    {
        $charge = $get('charge_id') ? SubscriptionCharge::find($get('charge_id')) : null;
        $personId = $get('person_id');
        if (! $charge || ! $personId) {
            $set('amount_eur', null);

            return;
        }
        $balance = app(ChargeBalances::class)->forCharge($charge)->get($personId);
        $set('amount_eur', $balance ? number_format($balance['outstanding_cents'] / 100, 2, '.', '') : null);
    }
}

<?php

namespace App\Filament\Resources\MemberPayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\SubscriptionCharge;

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
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) {
                            $set('amount_eur', null);
                            return;
                        }

                        $charge = SubscriptionCharge::with('subscription.members')
                            ->find($state);

                        if (!$charge || !$charge->subscription) {
                            return;
                        }

                        $membersCount = $charge->subscription->members->count();
                        if ($membersCount <= 0) {
                            return;
                        }

                        // float division for euros
                        $perMember = $charge->amount_eur / $membersCount;

                        // format nicely
                        $set('amount_eur', number_format($perMember, 2, '.', ''));
                    }),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->required(),

                TextInput::make('amount_eur')
                    ->label('Price (€)')
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
}

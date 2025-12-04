<?php

namespace App\Filament\Resources\SubscriptionMembers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubscriptionMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription_id')
                    ->relationship('subscription', 'service_name')
                    ->searchable()
                    ->preload()
                    ->label('Subscription'),

                Select::make('person_id')
                    ->relationship('person', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Person'),

                Textarea::make('notes')
                    ->rows(2)
                    ->nullable(),
            ]);
    }
}

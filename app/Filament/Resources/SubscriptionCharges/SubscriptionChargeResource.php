<?php

namespace App\Filament\Resources\SubscriptionCharges;

use App\Filament\Resources\SubscriptionCharges\Pages\CreateSubscriptionCharge;
use App\Filament\Resources\SubscriptionCharges\Pages\EditSubscriptionCharge;
use App\Filament\Resources\SubscriptionCharges\Pages\ListSubscriptionCharges;
use App\Filament\Resources\SubscriptionCharges\Schemas\SubscriptionChargeForm;
use App\Filament\Resources\SubscriptionCharges\Tables\SubscriptionChargesTable;
use App\Models\SubscriptionCharge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionChargeResource extends Resource
{
    protected static ?string $model = SubscriptionCharge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'charge_date';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionChargeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionChargesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionCharges::route('/'),
            'create' => CreateSubscriptionCharge::route('/create'),
            'edit' => EditSubscriptionCharge::route('/{record}/edit'),
        ];
    }
}

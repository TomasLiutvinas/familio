<?php

namespace App\Filament\Resources\MemberPayments;

use App\Filament\Resources\MemberPayments\Pages\CreateMemberPayment;
use App\Filament\Resources\MemberPayments\Pages\EditMemberPayment;
use App\Filament\Resources\MemberPayments\Pages\ListMemberPayments;
use App\Filament\Resources\MemberPayments\Schemas\MemberPaymentForm;
use App\Filament\Resources\MemberPayments\Tables\MemberPaymentsTable;
use App\Models\MemberPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MemberPaymentResource extends Resource
{
    protected static ?string $model = MemberPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'paid_on';

    public static function form(Schema $schema): Schema
    {
        return MemberPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberPaymentsTable::configure($table);
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
            'index' => ListMemberPayments::route('/'),
            'create' => CreateMemberPayment::route('/create'),
            'edit' => EditMemberPayment::route('/{record}/edit'),
        ];
    }
}

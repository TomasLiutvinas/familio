<?php

namespace App\Filament\Resources\SubscriptionMembers;

use App\Filament\Resources\SubscriptionMembers\Pages\CreateSubscriptionMember;
use App\Filament\Resources\SubscriptionMembers\Pages\EditSubscriptionMember;
use App\Filament\Resources\SubscriptionMembers\Pages\ListSubscriptionMembers;
use App\Filament\Resources\SubscriptionMembers\Schemas\SubscriptionMemberForm;
use App\Filament\Resources\SubscriptionMembers\Tables\SubscriptionMembersTable;
use App\Models\SubscriptionMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionMemberResource extends Resource
{
    protected static ?string $model = SubscriptionMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionMembersTable::configure($table);
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
            'index' => ListSubscriptionMembers::route('/'),
            'create' => CreateSubscriptionMember::route('/create'),
            'edit' => EditSubscriptionMember::route('/{record}/edit'),
        ];
    }
}

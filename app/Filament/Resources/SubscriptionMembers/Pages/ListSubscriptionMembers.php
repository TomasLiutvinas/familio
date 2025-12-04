<?php

namespace App\Filament\Resources\SubscriptionMembers\Pages;

use App\Filament\Resources\SubscriptionMembers\SubscriptionMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionMembers extends ListRecords
{
    protected static string $resource = SubscriptionMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

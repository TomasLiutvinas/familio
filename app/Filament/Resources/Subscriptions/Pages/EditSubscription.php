<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Actions\ChangeSubscriptionPrice;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected function getHeaderActions(): array
    {
        return [ChangeSubscriptionPrice::make(), DeleteAction::make()->color('primary')];
    }

    protected static string $resource = SubscriptionResource::class;
}

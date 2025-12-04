<?php

namespace App\Filament\Resources\SubscriptionCharges\Pages;

use App\Filament\Resources\SubscriptionCharges\SubscriptionChargeResource;
use App\Filament\Traits\HasPrimaryDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionCharge extends EditRecord
{
    use HasPrimaryDeleteAction;

    protected static string $resource = SubscriptionChargeResource::class;
}

<?php

namespace App\Filament\Resources\SubscriptionMembers\Pages;

use App\Filament\Resources\SubscriptionMembers\SubscriptionMemberResource;
use App\Filament\Traits\HasPrimaryDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionMember extends EditRecord
{
    use HasPrimaryDeleteAction;

    protected static string $resource = SubscriptionMemberResource::class;
}

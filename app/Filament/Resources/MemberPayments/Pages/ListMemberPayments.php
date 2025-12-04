<?php

namespace App\Filament\Resources\MemberPayments\Pages;

use App\Filament\Resources\MemberPayments\MemberPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMemberPayments extends ListRecords
{
    protected static string $resource = MemberPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

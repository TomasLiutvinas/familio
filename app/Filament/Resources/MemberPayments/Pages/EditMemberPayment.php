<?php

namespace App\Filament\Resources\MemberPayments\Pages;

use App\Filament\Resources\MemberPayments\MemberPaymentResource;
use App\Filament\Traits\HasPrimaryDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMemberPayment extends EditRecord
{
    use HasPrimaryDeleteAction;

    protected static string $resource = MemberPaymentResource::class;
}

<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\People\PersonResource;
use App\Filament\Traits\HasPrimaryDeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    use HasPrimaryDeleteAction;

    protected static string $resource = PersonResource::class;
}

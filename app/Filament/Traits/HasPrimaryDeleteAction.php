<?php

namespace App\Filament\Traits;

use Filament\Actions\DeleteAction;

trait HasPrimaryDeleteAction
{
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->color('primary'),
        ];
    }
}

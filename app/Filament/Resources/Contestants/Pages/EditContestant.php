<?php

namespace App\Filament\Resources\Contestants\Pages;

use App\Filament\Resources\Contestants\ContestantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContestant extends EditRecord
{
    protected static string $resource = ContestantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Contestants\Pages;

use App\Filament\Resources\Contestants\ContestantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContestants extends ListRecords
{
    protected static string $resource = ContestantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

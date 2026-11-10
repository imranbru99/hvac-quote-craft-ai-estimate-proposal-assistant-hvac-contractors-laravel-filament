<?php

namespace App\Filament\Resources\JobTicketResource\Pages;

use App\Filament\Resources\JobTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobTickets extends ListRecords
{
    protected static string $resource = JobTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

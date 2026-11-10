<?php

namespace App\Filament\Resources\ProposalFollowupResource\Pages;

use App\Filament\Resources\ProposalFollowupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProposalFollowups extends ListRecords
{
    protected static string $resource = ProposalFollowupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

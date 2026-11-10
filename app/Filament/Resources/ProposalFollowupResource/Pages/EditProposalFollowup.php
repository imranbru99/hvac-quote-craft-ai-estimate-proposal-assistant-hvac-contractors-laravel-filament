<?php

namespace App\Filament\Resources\ProposalFollowupResource\Pages;

use App\Filament\Resources\ProposalFollowupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposalFollowup extends EditRecord
{
    protected static string $resource = ProposalFollowupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\PaymentReminderResource\Pages;

use App\Filament\Resources\PaymentReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentReminder extends EditRecord
{
    protected static string $resource = PaymentReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

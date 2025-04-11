<?php

namespace App\Filament\Resources\InterventionDeliveryResource\Pages;

use App\Filament\Resources\InterventionDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterventionDelivery extends EditRecord
{
    protected static string $resource = InterventionDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

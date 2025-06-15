<?php

namespace App\Filament\Resources\InterventionResource\Pages;

use App\Filament\Resources\InterventionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntervention extends EditRecord
{
    protected static string $resource = InterventionResource::class;

    protected static ?string $title = 'Modifier une intervention';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // static::updated(function (Intervention $intervention) {
       

        protected function mutateFormDataBeforeSave(array $data): array
    {
           if($data['type_activite'] == 1)
            {
                $data['generator_name'] = null;
                $data['generator_reference'] = null;
                $ada['power'] = null;
                $data['serial_number'] = null;
                $data['customer_id'] = null;
            }else {
                $data['contract_id'] = null;
                $data['generator_id'] = null;
            }
    
        return $data;

    }
}

<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected static ?string $title = 'Nouveau contrat';

        protected function afterCreate()
{
    $data = $this->form->getState();
    foreach ($data['generators'] as $generatorData) {
        $this->record->generators()->attach(
            $generatorData['generator_id'],
            [
                'site' => $generatorData['site'] ?? null,
                'code_site' => $generatorData['code_site'] ?? null,
                'contact_name' => $generatorData['contact_name'] ?? null,
                'contact_phone' => $generatorData['contact_phone'] ?? null,
                'contact_email' => $generatorData['contact_email'] ?? null,
                'user_id' => auth()->id(),
            ]
        );
    }
}
}

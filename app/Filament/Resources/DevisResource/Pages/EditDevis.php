<?php

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDevis extends EditRecord
{
    protected static string $resource = DevisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {

        $record->update($data);
        foreach ($data['generators'] as $generatorData) {
            $this->record->generators()->syncWithoutDetaching([
                $generatorData['generator_id'] => [
                    'site' => $generatorData['site'],
                    'code_site' => $generatorData['code_site'],
                    'contact_name' => $generatorData['contact_name'],
                    'contact_phone' => $generatorData['contact_phone'],
                    'contact_email' => $generatorData['contact_email'],
                    'status' => $record->is_active,
                    'user_id' => auth()->id(),
                ]
            ]);
        }
        return $record;
    }
}

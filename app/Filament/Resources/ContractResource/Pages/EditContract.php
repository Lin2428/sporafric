<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Generator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

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
                    'forfait' => $generatorData['forfait'],
                    'site' => $generatorData['site'],
                    'code_site' => $generatorData['code_site'],
                    'contact_name' => $generatorData['contact_name'],
                    'contact_phone' => $generatorData['contact_phone'],
                    'contact_email' => $generatorData['contact_email'],
                    'user_id' => auth()->id(),
                ]
            ]);

            Generator::where('id', $generatorData['generator_id'])->update(['status' => GeneratorStatus::EN_LOCATION->value]);
        }
        return $record;
    }
}

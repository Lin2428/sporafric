<?php

namespace App\Filament\Resources\TechnicalVisitsResource\Pages;

use App\Filament\Resources\TechnicalVisitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTechnicalVisits extends EditRecord
{
    protected static string $resource = TechnicalVisitsResource::class;

    protected static ?string $title = 'Modifier la visite technique';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    $data['control_1'] = in_array('control_1', $data['checklist_1']);
    $data['control_2'] = in_array('control_2', $data['checklist_1']);
    $data['control_3'] = in_array('control_3', $data['checklist_1']);
    $data['control_4'] = in_array('control_4', $data['checklist_2']);
    $data['control_5'] = in_array('control_5', $data['checklist_2']);
    $data['control_6'] = in_array('control_6', $data['checklist_2']);
    $data['control_7'] = in_array('control_7', $data['checklist_2']);
    $data['control_8'] = in_array('control_8', $data['checklist_2']);
    $data['control_9'] = in_array('control_9', $data['checklist_2']);
    $data['control_10'] = in_array('control_10', $data['checklist_2']);
    $data['control_11'] = in_array('control_11', $data['checklist_2']);
    $data['control_12'] = in_array('control_12', $data['checklist_3']);
    $data['control_13'] = in_array('control_13', $data['checklist_3']);
    $data['control_14'] = in_array('control_14', $data['checklist_3']);

    unset($data['checklist_1']);
    unset($data['checklist_2']);
    unset($data['checklist_3']);
    
    return $data;
}
}

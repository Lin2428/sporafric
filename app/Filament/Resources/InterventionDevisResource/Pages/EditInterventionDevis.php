<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\InterventionDevisResource;
use App\Models\DevisGenerator;
use App\Models\Generator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterventionDevis extends EditRecord
{
    protected static string $resource = InterventionDevisResource::class;
    protected static ?string $title = 'Modifier une intervention';
    
    protected function getHeaderActions(): array
    {
        return [
            //   Actions\Action::make('print')
            // ->label("Imprimer")
            // ->icon("heroicon-o-printer")
            // ->color('primary')
            // ->url(url('/admin/intervention-devis/'.$this->record->id)),

            Actions\DeleteAction::make()
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        $submittedPieces = collect($data['pieces'])->pluck('piece_id')->toArray();

        $this->record->pieces()
            ->whereNotIn('piece_id', $submittedPieces)
            ->delete();

        foreach ($data['pieces'] as $piece) {
            $this->record->pieces()->syncWithoutDetaching([
                $piece['piece_id'] => [
                    'qty'          => $piece['qty'],
                    'price'        => $piece['price'] ?? 0,
                    'generator_id' => $data['generator_id'] ?? null,
                ],
            ]);
        }


        if($data['type'] == InterventionType::RONDE->value || $data['type'] == InterventionType::VIDANGE->value){
           $houres = $data['houres'];
             $nexTvidange = $data['prochain_visite'] -  $houres;
            $vidange =  $nexTvidange > 30;

            Generator::where('id', $data['generator_id'])
            ->update([
                'houres' => $data['houres'],
                'next_vidange' => $data['next_vidange'],
                'prochain_visite' => $data['prochain_visite'],
                'vidange' => $vidange
            ]);
        }

        if($data['type'] == InterventionType::REMPLACEMENT->value){
            if($this->record->new_generator_id != $data['new_generator_id']){
                Generator::where('id', $data['generator_id'])
            ->update([
                'status' => GeneratorStatus::EN_REVU->value
            ]);

            Generator::where('id', $data['new_generator_id'])
            ->update([
                'status' => GeneratorStatus::EN_LOCATION->value
            ]);

            DevisGenerator::where('devis_id', $data['devis_id'])
            ->where('generator_id', $data['generator_id'])
            ->update([
                'generator_id' => $data['new_generator_id'],
                'old_generator_id' => $data['generator_id'],
            ]);
            }
        }

        return $data;

    }
}

<?php
namespace App\Filament\Resources\InterventionResource\Pages;

use App\Enum\InterventionType;
use App\Filament\Resources\InterventionResource;
use App\Models\Generator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntervention extends EditRecord
{
    protected static string $resource = InterventionResource::class;

    protected static ?string $title = 'Modifier une intervention';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
            ->label("Imprimer")
            ->icon("heroicon-o-printer")
            ->color('primary')
            ->url(url('/admin/interventions/'.$this->record->id)),

            Actions\DeleteAction::make()
        ];
    }

    // static::updated(function (Intervention $intervention) {

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

        if ($data['type_activite'] == 1) {
            $data['generator_name']      = null;
            $data['generator_reference'] = null;
            $ada['power']                = null;
            $data['serial_number']       = null;
            $data['customer_id']         = null;
        } else {
            $data['contract_id']  = null;
            $data['generator_id'] = null;
        }

        if($data['type_activite'] == '1' && ($data['type'] == InterventionType::RONDE->value || $data['type'] == InterventionType::VIDANGE->value)){
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

        return $data;

    }
}

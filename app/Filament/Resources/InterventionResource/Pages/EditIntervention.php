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

        return $data;

    }
}

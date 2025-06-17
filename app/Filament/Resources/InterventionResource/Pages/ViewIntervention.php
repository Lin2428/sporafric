<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\InterventionStatus;
use App\Filament\Resources\InterventionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewIntervention extends ViewRecord
{
    protected static string $resource = InterventionResource::class;
    public $showForm = false;

    public function getTitle(): string | Htmlable
    {

        $name = $this->record->contract != null ? $this->record->contract?->customer->name . "  " . $this->record->contract?->customer?->contact_c_phone
            : $this->record->customer?->name . " - " . $this->record->customer?->contact_c_phone;

        $title = '
        <div class="flex items-center space-x-4">
            <strong class="text-primary text-3xl">' . e($name) . '</strong>
        </div>
    ';
        return new HtmlString($title);
    }



    protected function getHeaderActions(): array
    {
        return [
            /*Actions\Action::make('facture')
                ->label('Generer la facture')
                ->icon('heroicon-o-ticket')
                ->color('success')
                ->iconPosition('after'),*/
            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier l\'intervention')
                    ->icon('heroicon-o-pencil'),

                 Actions\Action::make('en_cours')
                    ->label('Marquer en cours')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->action(function ($record) {
                        $record->status = InterventionStatus::EN_COURS->value;
                        $record->save();
                    })
                    ->visible(fn($record)=>$record->status == InterventionStatus::PLANIFIEE->value ? true : false),

                Actions\Action::make('finish')
                    ->label('Marquer comme terminé')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($record) {
                        $record->status = InterventionStatus::TERMINEE->value;
                        $record->save();
                    })
                    ->visible(fn($record)=> $record->status == InterventionStatus::EN_COURS->value ? true : false)
              
            ]),
        ];
    }
}

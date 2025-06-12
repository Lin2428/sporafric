<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\InterventionDevisResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewInterventionDevis extends ViewRecord
{
    protected static string $resource = InterventionDevisResource::class;
    public $showForm = false;

    public function getTitle(): string | Htmlable
    {

        $img = $this->record->contract?->customer?->logo ?? $this->record->devis?->customer->logo;

        $name = $this->record->contract != null ? $this->record->contract?->customer?->name . " - " . $this->record->contract?->site
            : $this->record->devis?->customer?->name . " - " . $this->record->devis?->site;

        $title = '
        <div class="flex items-center space-x-4">
            <img src="' . asset("/storage/$img") . '" class="w-[100px] h-auto object-contain rounded" alt="Logo">
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

                Actions\DeleteAction::make()
                    ->label('Annulé l\'intervention')
                    ->icon('heroicon-o-trash'),
            ]),
        ];
    }
}

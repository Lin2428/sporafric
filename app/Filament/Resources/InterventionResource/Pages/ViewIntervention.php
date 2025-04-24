<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\InterventionResource;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewIntervention extends ViewRecord
{
    protected static string $resource = InterventionResource::class;

    public function getTitle(): string | Htmlable
    {
        $title = "<strong class='text-primary'> {$this->record->contract->customer->name} - {$this->record->contract->site}</strong>";
        return new HtmlString($title);
    }



    protected function getHeaderActions(): array
    {
        return [

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

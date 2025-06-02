<?php

namespace App\Filament\Resources\DevisResource\Pages;


use App\Filament\Resources\DevisResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewDevis extends ViewRecord
{
    protected static string $resource = DevisResource::class;

    public function getTitle(): string | Htmlable
    {
        $title =  "<strong class='text-primary'> {$this->record->customer->name} - {$this->record->site}</strong>";
        return new HtmlString($title);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier le contrat')
                    ->icon('heroicon-o-pencil'),
                    
                Actions\DeleteAction::make()
                    ->label('Supprimer le contrat')
                    ->icon('heroicon-o-trash'),
            ]),
        ];
    }
}
?>
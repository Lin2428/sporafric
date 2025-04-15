<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewGenerator extends ViewRecord
{
    protected static string $resource = GeneratorResource::class;

    public function getTitle(): string | Htmlable
    {
        $title =  "<strong class='text-primary'> {$this->record->name} - {$this->record->site} - {$this->record->modele}</strong>";
        return new HtmlString($title);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('history')
                ->label('Historique')
                ->icon('heroicon-o-clock')
                //->url($this->record->generator ? route('filament.resources.contracts.history', $this->record) : route('filament.resources.contracts.history', $this->record))
                ->openUrlInNewTab(),
            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier le groupe electrogene')
                    ->icon('heroicon-o-pencil'),
                Actions\DeleteAction::make()
                    ->label('Supprimer le groupe electrogene')
                    ->icon('heroicon-o-trash'),
            ]),
        ];
    }
}
?>
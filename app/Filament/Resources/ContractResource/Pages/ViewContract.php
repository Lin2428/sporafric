<?php

namespace App\Filament\Resources\ContractResource\Pages;


use App\Filament\Resources\ContractResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewContract extends ViewRecord
{
    protected static string $resource = ContractResource::class;

    public function getTitle(): string | Htmlable
    {
        $title =  "<strong class='text-primary'> {$this->record->customer->name} - {$this->record->site} - {$this->record->customerAdress->city->name}</strong>";
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
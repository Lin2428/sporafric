<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\ContractGeneratorResource;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewContractGenerator extends ViewRecord
{
    protected static string $resource = ContractGeneratorResource::class;

    public function getTitle(): string | Htmlable
    {
        $title = "<strong class='text-primary'> {$this->record->name} - {$this->record->power}kVA</strong>";
        return new HtmlString($title);
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('previous')
                ->hiddenLabel()
                ->icon('heroicon-o-chevron-left')
                ->color('primary')
                ->action(fn() => $this->redirect(url("/admin/contract-generators/{$this->record->getPreviousRecordMaintenance()?->id}")))
                ->visible(fn() => $this->record->getPreviousRecordMaintenance()?->id !== null)
                ->tooltip('Précédent'),

            Actions\Action::make('next')
                ->hiddenLabel()
                ->icon('heroicon-o-chevron-right')
                ->color('primary')
                ->action(fn() => $this->redirect(url("/admin/contract-generators/{$this->record->getNextRecordMaintenance()->id}")))
                ->visible(fn() => $this->record->getNextRecordMaintenance()?->id !== null)
                ->tooltip('Suivant'),

            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier le GE')
                    ->icon('heroicon-o-pencil'),
                Actions\Action::make('edit_contract')
                    ->label('Modifier la location')
                    ->icon('heroicon-o-pencil')
                    ->url(fn() => url("/admin/contracts/{$this->record->ContractGenerator?->contract_id}/edit"))
                    ->visible(fn() => $this->record->ContractGenerator !== null),
            ]),
        ];
    }
}

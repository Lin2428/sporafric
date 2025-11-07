<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Filament\Resources\GeneratorResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewGenerator extends ViewRecord
{
    protected static string $resource = GeneratorResource::class;

    public function getTitle(): string | Htmlable
    {
        $title = "<strong class='text-primary'> {$this->record->name} - {$this->record->reference} - {$this->record->power}kVA</strong>";
        return new HtmlString($title);
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('previous')
                ->hiddenLabel()
                ->icon('heroicon-o-chevron-left')
                ->color('primary')
                ->action(fn() => $this->redirect(url("/admin/generators/{$this->record->getPreviousRecordLocation()?->id}")))
                ->visible(fn() => $this->record->getPreviousRecordLocation()?->id !== null)
                ->tooltip('Précédent'),

            Actions\Action::make('next')
                ->hiddenLabel()
                ->icon('heroicon-o-chevron-right')
                ->color('primary')
                ->action(fn() => $this->redirect(url("/admin/generators/{$this->record->getNextRecordLocation()->id}")))
                ->visible(fn() => $this->record->getNextRecordLocation()?->id !== null)
                ->tooltip('Suivant'),

            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier le GE')
                    ->icon('heroicon-o-pencil'),
                Actions\Action::make('edit_devis')
                    ->label('Modifier la location')
                    ->icon('heroicon-o-pencil')
                    ->url(fn() => url("/admin/devis/{$this->record->devisGenerator?->devis_id}/edit"))
                    ->visible(fn() => $this->record->devisGenerator !== null),
            ]),

        ];
    }
}

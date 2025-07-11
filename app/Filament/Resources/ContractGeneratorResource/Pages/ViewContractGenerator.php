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

            Actions\ActionGroup::make([
                Actions\EditAction::make()
                    ->label('Modifier le groupe electrogene')
                    ->icon('heroicon-o-pencil')
            ]),
        ];
    }
}

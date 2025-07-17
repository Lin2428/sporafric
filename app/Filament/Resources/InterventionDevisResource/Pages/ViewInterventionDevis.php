<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\InterventionDevisResource;
use App\Models\Generator;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use ArielMejiaDev\FilamentPrintable\Actions\PrintBulkAction;
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


        return " ";
    }



    protected function getHeaderActions(): array
    {
        return [
            PrintAction::make('print')
            ->label('Imprimer'),
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

                        if($record->type == InterventionType::RETRAIT->value){
                            Generator::where('id', $record->generator_id)
                            ->update(['status' => GeneratorStatus::EN_REVU->value]);
                        }
                    })
                    ->visible(fn($record)=> $record->status == InterventionStatus::EN_COURS->value ? true : false)
            ]),
        ];
    }
}

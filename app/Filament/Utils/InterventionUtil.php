<?php

namespace App\Filament\Utils;

use App\Enum\InterventionStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

class InterventionUtil
{
    public static function infoInterne()
    {
        $section = Section::make('Infos internes')
            ->columns(1)
            ->schema([
                DatePicker::make('start_date')
                    ->label('Date de début')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Date limite')
                    ->required(),

                Select::make('status')
                    ->label('Statut')
                    ->options(collect(InterventionStatus::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                    ->required(),
                Select::make('interventionTechniciens.technicien_id')
                    ->relationship('interventionTechniciens', 'name')
                    ->label('Techniciens assignés')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder('Sélectionner un technicien'),
            ]);
        return $section;
    }
}

<?php

namespace App\Filament\Utils;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Models\Intervention;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

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

    public static function customerColumn(Intervention $record): HtmlString
    {
        // $district = $record->customer?->customerAdresses->isNotEmpty() ? $record->customer?->customerAdresses->first()->district->name : "";
        // $city= $record->customer?->customerAdresses->isNotEmpty() ? $record->customer?->customerAdresses->first()->city->name : "";
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='text-[13px]'>{$record->contract?->customer->contact_c_phone}{$record->customer?->contact_c_phone}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function generatorColumn(Intervention $record): HtmlString
    {
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='font-normal'>{$record->contract?->generator->modele}{$record->generator}</span>
                    <span class='font-normal'>{$record->contract?->generator->serial_number}{$record->serial_number}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function table(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Créé le')
                ->dateTime("d/m/Y à H:i")
                ->sortable(),

            TextColumn::make('identifiant')
                ->label('Numéro')
                ->searchable()
                ->sortable()
                ->limit(50),

            TextColumn::make('type_location')
                ->label('Type')
                ->getStateUsing(fn($record) => $record->type_location == 1 ? "Maintenance" : "Location")
                ->extraAttributes(['class' => 'font-bold']),

            TextColumn::make('status')
                ->label('Statut')
                ->badge()
                ->getStateUsing(fn($record) => InterventionStatus::from($record->status)->label())
                ->searchable()
                ->colors([
                    'warning' => "En cours",
                    'info' => "Non commencée",
                    'danger' => "Annulée",
                    'success' => "Terminée",
                ]),

            TextColumn::make('client') // Nom arbitraire, car on utilise getStateUsing
                ->label('Client')
                ->searchable(true, function($search) {
                    return fn($query, $search) => $query
                        ->whereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('contract.customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                })
                ->getStateUsing(function (Intervention $record) {
                    return $record->contract
                        ? optional($record->contract->customer)->name
                        : optional($record->customer)->name;
                })
                ->description(fn(Intervention $record) => static::customerColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(50),

            TextColumn::make('groupe')
                ->label('Groupe Électrogène')
                ->searchable(false, function($search) {
                    return fn($query, $search) => $query
                        ->whereHas('generator', function ($query) use ($search) {
                            $query->where('modele', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('contract.generator', function ($query) use ($search) {
                            $query->where('modele', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                        });
                })
                ->getStateUsing(function (Intervention $record) {
                    return $record->contract
                        ? optional($record->contract->generator)->name
                        : $record->generator;
                })
                ->description(fn(Intervention $record) => static::generatorColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(50),

            TextColumn::make('type')
                ->label('Type')
                ->getStateUsing(fn($record) => InterventionType::from($record->type)->label())
                ->searchable()
                ->sortable(),
        ];
    }
}

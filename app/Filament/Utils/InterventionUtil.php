<?php

namespace App\Filament\Utils;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Models\Intervention;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use App\Utils\NumberUtils;

class InterventionUtil
{
    public static function infoInterne()
    {
        $section = Section::make('Infos internes')
            ->columns(1)
            ->schema([
                DateTimePicker::make('start_date')
                    ->label('Date de début')
                    ->reactive(),

                DateTimePicker::make('end_date')
                    ->label('Date de fin'),

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
       
        $reference = $record->generator?->reference ?? $record->reference;
        $powr = $record->generator?->power ?? $record->power;
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='font-normal'>{$reference}</span>
                    <span class='font-normal'>{$powr}kVA</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function table(string $nameContrat = "Contrat"): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Créé le')
                ->dateTime("d/m/Y à H:i")
                ->sortable(),

            TextColumn::make('numero')
                ->label('Numéro')
                ->searchable()
                ->sortable()
                ->copyable()
                ->limit(50)
                ->extraAttributes(['class' => 'font-bold'])
                ->copyable(),

            TextColumn::make('identifiant')
                ->label('N° Bon de travaux')
                ->searchable()
                ->sortable()
                ->limit(50),

            TextColumn::make('status')
                ->label('Statut')
                ->getStateUsing(function($record){
                    $stat = InterventionStatus::from($record->status)->label();
                    return BadgetWidget::interventionStatusBadget($stat);
                    })
                ->html(),

            TextColumn::make('client') // Nom arbitraire, car on utilise getStateUsing
                ->label('Client')
                ->searchable(true, function($search) {
                    return fn($query, $search) => $query
                        ->whereHas('devis.customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('contract.customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ;

                })
                ->getStateUsing(function (Intervention $record) {
                    return  optional($record->contract?->customer)->name ??
                         optional($record->devis?->customer)->name
                         ?? optional($record->customer)->name;
                })
                ->description(fn(Intervention $record) => static::customerColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(8),

            TextColumn::make('cd') // Nom arbitraire, car on utilise getStateUsing
                ->label($nameContrat)
                ->searchable(true, function($search) {
                    return fn($query, $search) => $query
                        ->whereHas('devis', function ($query) use ($search) {
                            $query->where('number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('contract', function ($query) use ($search) {
                            $query->where('number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                })
                ->getStateUsing(function (Intervention $record) {
                    return  optional($record->contract)->number ??
                         optional($record->devis)->number
                       
                         ?? "Hors contrat";
                })
                ->copyable()
                ->extraAttributes(['class' => 'font-bold']),

            TextColumn::make('generator_name')
                ->label('Groupe Électrogène')
                ->searchable(false, function($search) {
                    return fn($query, $search) => $query
                        ->whereHas('generator', function ($query) use ($search) {
                            $query->where('reference', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                        });
                })
                ->getStateUsing(fn(Intervention $record) => $record->generator?->name ?? $record->generator_name)
                ->description(fn(Intervention $record) => static::generatorColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(8),

            TextColumn::make('type')
                ->label('Type')
                ->getStateUsing(fn($record) => InterventionType::from($record->type)->label())
                ->searchable()
                ->sortable(),

            TextColumn::make('montant')
                ->label('Montant')
                ->getStateUsing(fn($record) => NumberUtils::format($record->montant). " FCFA")
                ->searchable()
                ->sortable(),
        ];
    }
}

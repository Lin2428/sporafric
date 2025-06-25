<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicalVisitsResource\Pages;
use App\Filament\Resources\TechnicalVisitsResource\RelationManagers;
use App\Filament\Utils\WidgetUtils;
use App\Models\TechnicalVisits;
use ArielMejiaDev\FilamentPrintable\Actions\PrintBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class TechnicalVisitsResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = TechnicalVisits::class;
     
    protected static ?string $label           = "Visites techniques";

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Ronde';

    protected static ?string $navigationLabel = 'Visites techniques';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informations sur le client')
                ->columns(2)
                ->schema([
                    Select::make('type_service')
                        ->label('Location ou Maintenance ?')
                        ->options(['1' => 'Maintenance', '0' => 'Location'])
                        ->reactive(),

                    DateTimePicker::make('date'),

                    WidgetUtils::contractSelectWidget()->visible(fn(callable $get) => $get('type_service') == '1'),

                    WidgetUtils::contractSelectWidget('devis_id')->label('Devis')->visible(fn(callable $get) => $get('type_service') == '0'),

                    WidgetUtils::generatorSelectWidget(type: null)->visible(fn(callable $get) => $get('type_service') !== null),
                ])
                ->columnSpanFull(),

            Section::make('Visite')->schema([
                CheckboxList::make('checklist_1')
                    ->label('Action préable')
                    ->bulkToggleable()
                    ->options([
                        'control_1' => 'Niveau d\'huile moteur',
                        'control_2' => 'Niveau du liquide de refroidissement',
                        'control_3' => 'Niveau de l\'electrolute Batterie',
                    ])
                    ->columns(3)
                    ->afterStateHydrated(function ($component, $record) {
                        $data = [];
                        if ($record?->control_1 == true) {
                            $data[] = 'control_1';
                        }
                        if ($record?->control_2 == true) {
                            $data[] = 'control_2';
                        }
                        if ($record?->control_3 == true) {
                            $data[] = 'control_3';
                        }

                        $component->state($data);
                    }),

                CheckboxList::make('checklist_2')
                    ->label('Contrôle technique')
                    ->bulkToggleable()
                    ->options([
                        'control_4' => 'Filtre à huile',
                        'control_5' => 'Filtre à air',
                        'control_6' => 'Filtre à carburant',
                        'control_7' => 'Circuit carburant',
                        'control_8' => 'Circuit de refroidissement',
                        'control_9' => 'etat de la présence des courroies',
                        'control_10' => 'Chargeur batterie',
                        'control_11' => 'Resistance chauffante',
                    ])
                    ->afterStateHydrated(function ($component, $record) {
                        $data = [];
                        
                        if ($record?->control_4 == true) {
                            $data[] = 'control_4';
                        }
                        if ($record?->control_5 == true) {
                            $data[] = 'control_5';
                        }
                        if ($record?->control_6 == true) {
                            $data[] = 'control_6';
                        }
                        if ($record?->control_7 == true) {
                            $data[] = 'control_7';
                        }
                        if ($record?->control_8 == true) {
                            $data[] = 'control_8';
                        }
                        if ($record?->control_9 == true) {
                            $data[] = 'control_9';
                        }
                        if ($record?->control_10 == true) {
                            $data[] = 'control_10';
                        }
                        if ($record?->control_11 == true) {
                            $data[] = 'control_11';
                        }

                        $component->state($data);
                    })
                    ->columns(3),

                Radio::make('control_battery')
                    ->label('Contrôle de la batterie et de la densité(3ans)')
                    ->options([
                        '1' => '1 ,26 à 1 ,28',
                        '2' => '1 ,22 à 1 ,26',
                        '3' => '< 1 ,22',
                    ])
                    ->columns(3),
            ]),

            Section::make('Après visite')
                ->columns(2)
                ->schema([
                    CheckboxList::make('checklist_3')
                        ->bulkToggleable()
                        ->label('')
                        ->options([
                            'control_12' => 'Démarrage du GE',
                            'control_13' => 'Fonctionement du démarreur',
                            'control_14' => 'Etat du GE et du local',
                        ])
                        ->afterStateHydrated(function ($component, $record) {
                        $data = [];
                        if ($record?->control_12 == true) {
                            $data[] = 'control_12';
                        }
                        if ($record?->control_13 == true) {
                            $data[] = 'control_13';
                        }
                        if ($record?->control_14 == true) {
                            $data[] = 'control_14';
                        }

                        $component->state($data);
                    })
                        ->columns(3)
                        ->columnSpanFull(),

                    TextInput::make('control_circuit')->numeric()->label('Circuit de charge moteur (V)'),
                    TextInput::make('control_frequence')->numeric()->label('Frequences (Hz)'),

                    Section::make('Tension de sortie(230V)')
                        ->columns(3)
                        ->schema([
                            TextInput::make('control_tension.v1')
                            ->label('V1n'),

                            TextInput::make('control_tension.v2')
                            ->label('V2n'),

                            TextInput::make('control_tension.v3')
                            ->label('V3n'),
                            ])
                        ->columnSpanFull(),

                    Section::make('Tension de sortie(400V)')
                        ->columns(3)
                        ->schema([
                            TextInput::make('control_tension_2.u1')
                            ->label('U12'),

                            TextInput::make('control_tension_2.u2')
                            ->label('U13'),

                             TextInput::make('control_tension_2.u3')
                             ->label('U23')])
                        ->columnSpanFull(),
                    Section::make('Intensité par phase')
                        ->columns(3)
                        ->schema([
                            TextInput::make('control_intensite.i1')
                        ->label('I1'), 

                        TextInput::make('control_intensite.i2')
                        ->label('I2'), 

                        TextInput::make('control_intensite.i3')
                        ->label('I3')])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function customerColumn(TechnicalVisits $record): HtmlString
    {
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='text-[13px]'>{$record->contract?->customer->contact_c_phone}{$record->customer?->contact_c_phone}</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function generatorColumn($record): HtmlString
    {
       
        $reference = $record->generator?->reference ?? $record->reference;
        $powr = $record->generator?->power ?? $record->power;
        $html = "
                <div class='flex flex-col text-xs' style='line-height: 1.2;'>
                    <span class='font-normal'>{$reference}</span>
                    <span class='font-normal'>{$powr}KVA</span>
                </div>
            ";

        return new HtmlString($html);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->columns([
            TextColumn::make('created_at')
                ->label('Créé le')
                ->dateTime("d/m/Y à H:i")
                ->sortable(),

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
                        });

                })
                ->getStateUsing(function ($record) {
                    return  optional($record->contract?->customer)->name ??
                         optional($record->devis?->customer)->name;
                })
                ->description(fn($record) => static::customerColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(8),

            TextColumn::make('cd') // Nom arbitraire, car on utilise getStateUsing
                ->label("Contrat/Devis")
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
                ->getStateUsing(function ($record) {
                    return  optional($record->contract)->number ??
                         optional($record->devis)->number;
                })
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
                ->getStateUsing(fn($record) => $record->generator?->name ?? $record->generator_name)
                ->description(fn($record) => static::generatorColumn($record))
                ->extraAttributes(['class' => 'font-bold'])
                ->limit(8),
            ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), PrintBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechnicalVisits::route('/'),
            'create' => Pages\CreateTechnicalVisits::route('/create'),
            'edit' => Pages\EditTechnicalVisits::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'cancell',
            'delete_any',
            'delete',
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages;
use App\Filament\Utils\BadgetWidget;
use App\Livewire\CheckList;
use App\Livewire\InterventionHistory;
use App\Livewire\LocationHistory;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Models\Intervention;
use App\Models\ReportLocation;
use App\Utils\DateUtils;
use App\Utils\NumberUtils;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Doctrine\DBAL\Schema\View;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneratorResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Generator::class;

    protected static ?string $label           = "GE Location";
    protected static ?string $navigationIcon  = 'icon-generator';
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $navigationLabel = 'Groupes Electrogènes';
    protected static ?int $navigationSort     = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = Generator::where('type', 1)->count();
        return $count;
    }

   public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Infos générales')
                            ->columns()
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Identification GE & SN')
                                    ->columnSpanFull(),

                                // TextInput::make('reference')
                                //     ->label('referencee')
                                //     ->columnSpanFull(),

                                // TextInput::make('serial_number')
                                //     ->label('Numéro de série')
                                //     ->columnSpanFull(),

                                FileUpload::make('image')
                                    // ->acceptedFileTypes([
                                    //     'jpg',
                                    //     'png',
                                    //     'jpeg',
                                    // ])
                                    // ->imageCropAspectRatio('1:1')
                                    // ->imageResizeTargetWidth('800')
                                    // ->imageResizeTargetWidth('800')
                                    // ->imageResizeMode('contain')
                                    // ->imagePreviewHeight('250')
                                    ->openable()
                                    ->reorderable()
                                    ->label('Image')
                                    ->columnSpanFull(),

                                TextInput::make('power')
                                    ->numeric()
                                    ->label('Puissance (kVA)'),

                                TextInput::make('voltage')
                                    ->numeric()
                                    ->label('Tension (V)'),

                                TextInput::make('frequency')
                                    ->label('Fréquence (Hz)')
                                    ->numeric()
                                    ->columnSpanFull(),

                                // TextInput::make('fuel_type')
                                //     ->label('Type de carburant'),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Infos techniques')
                            ->schema([
                                DatePicker::make('start-up')
                                    ->label('Mise en service')
                                    ->default(now()),

                                Select::make('status')
                                    ->options( collect(GeneratorStatus::cases())
                                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                        ->toArray())
                                    ->searchable()
                                    ->label('Statut')
                                    ->preload()
,

                                TextInput::make('houres')
                                    ->label('Heures de fonctionnement'),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange (h)')
                                    ->numeric(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                     Section::make('Localisation sur la carte')
                            ->schema([
                                \Filament\Forms\Components\View::make('filament.forms.components.map-picker')
                                    ->label(''),
                            ])->columnSpan(['lg' => 2]),

                    Section::make('Données de la carte')
                            ->columns(2)
                            ->schema([
                            //    Textarea::make('adresse')
                            //         ->label('Adresse')
                            //         ->rows(2)  
                            //         ->columnSpanFull(),

                                TextInput::make('lat')
                                    ->label('Latitude')
                                    ->reactive()
                                    ->columnSpanFull(),

                                TextInput::make('lng')
                                    ->label('Longitude')
                                    ->reactive()
                                    ->columnSpanFull(),
                            ])->columnSpan(['lg' => 1]),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(static::$model::query()->where('type', 1))
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->default('generateur.png')
                    ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

                TextColumn::make('name')
                    ->label('Marque')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->getStateUsing(function (Generator $record) {
                        $status = GeneratorStatus::from($record->status)->label();
                        return BadgetWidget::generatorStatusBadget($status);
                    })
                    ->html(),

                TextColumn::make('houres')
                    ->label('Nb H')
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('next_vidange')
                    ->label('P vidange')
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('start-up')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable(),

            ])
            ->filters([
                Filter::make('status')
                ->form([
                    CheckboxList::make('status')
                    ->options( 
                        collect(GeneratorStatus::cases())
                             ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                             ->toArray()
                        )
                        
                ])
                ->query(
                    fn (Builder $query, array $data): Builder => $query
                        ->when($data['status'], function (Builder $query, array $status) {
                            return $query->whereIn('status', $status);
                        })
                ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index'  => Pages\ListGenerators::route('/'),
            'create' => Pages\CreateGenerator::route('/create'),
            'edit'   => Pages\EditGenerator::route('/{record}/edit'),
            'view'   => Pages\ViewGenerator::route('/{record}'),
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

    public static function buildInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Contrat / SItuation')
                            ->icon('heroicon-o-clipboard-document')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(4)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Informations du groupe électrogène')
                                            ->columns(3)
                                            ->schema([
                                                TextEntry::make('status')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        $status = GeneratorStatus::from($record->status)->label();
                                                        return BadgetWidget::generatorStatusBadget($status);
                                                    })
                                                    ->html()
                                                    ->columnSpanFull(),

                                                ImageEntry::make('image')
                                                    ->label('')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                                                    ->default('generateur.png'),

                                                TextEntry::make('name')
                                                    ->label('GE')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                // TextEntry::make('reference')
                                                //     ->label('referencee')
                                                //     ->extraAttributes(['class' => 'font-bold text-danger']),


                                                TextEntry::make('power')
                                                    ->label('Puissance')
                                                   ->extraAttributes([ 'class' => 'font-bold']),


                                                TextEntry::make('houres')
                                                    ->label('Heures de fonc.')
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('next_vidange')
                                                    ->getStateUsing(fn($record) => NumberUtils::format($record->next_vidange) . ' h')
                                                    ->label('Prochaine vidange')
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('start-up')
                                                    ->label('Mise en service')
                                                    ->date('d/m/Y')
                                                    ->inlineLabel()
                                                    ->columnSpanFull()
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(3)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make(function(Generator $record){
                                            if($record->devisGenerator){
                                                return 'Contrat en cours';
                                            }

                                            if($record->devisGenerator){
                                                return 'Devis en cours';
                                            }

                                            return 'Pas de devis';
                                        })
                                            ->columns(2)
                                            ->schema([
                                                TextEntry::make('is_active')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        $state = null;
                                                        if($record->contractGenerator){
                                                            $state =  $record->contractGenerator->contract->is_active;
                                                            return BadgetWidget::boleanToBadget($state, 'En cours', 'Terminé');
                                                        }
                                                        if($record->devisGenerator){
                                                            $state =  $record->devisGenerator->devis->is_active;
                                                            return BadgetWidget::boleanToBadget($state, 'En cours', 'Terminé');
                                                        }
                                                        return '';
                                                    })->html(),

                                                ImageEntry::make('logo')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->customer->logo ?? "customer.png";
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->customer->logo ?? "customer.png";
                                                        }
                                                        return null;
                                                    })
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                                TextEntry::make('number')
                                                    ->label(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return 'N° Contrat';
                                                        }
                                                        if($record->devisGenerator){
                                                            return 'N° Devis';
                                                        }
                                                        return null;
                                                    })
                                                     ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->number;
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->number;
                                                        }
                                                        return null;
                                                    })
                                                    ->url(fn(Generator $record) => $record->devisGenerator != null ? url('/admin/devis/' . $record->devisGenerator?->devis?->id ) : null)
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('customer-name')
                                                    ->label('Client')
                                                     ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->customer->name;
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->customer->name;
                                                        }
                                                        return null;
                                                    })
                                                    ->limit(10)
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('forfait')
                                                    ->label(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return 'Forfait mensuel';
                                                        }
                                                        if($record->devisGenerator){
                                                            return 'Coût total du devis';
                                                        }
                                                    })
                                                      ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->forfait;
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->forfait;
                                                        }
                                                        return null;
                                                    })
                                                    ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                                                    ->extraAttributes(['class' => 'font-bold'])
                                                    ->columnSpanFull(),

                                               
                                                TextEntry::make('start_date')
                                                    ->date('d/m/Y')
                                                    ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->start_date;
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->start_date;
                                                        }
                                                        return null;
                                                    })
                                                    ->label('A debuter le')
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                                TextEntry::make('end_date')
                                                    ->date('d/m/Y')
                                                    ->getStateUsing(function (Generator $record) {
                                                        if($record->contractGenerator){
                                                            return $record->contractGenerator->contract->end_date;
                                                        }
                                                        if($record->devisGenerator){
                                                            return $record->devisGenerator->devis->end_date;
                                                        }
                                                        return null;
                                                    })
                                                    ->label('Se termine le')
                                                    ->color('danger'),

                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(7)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make(function (Generator $record) {
                                            if($record->contractGenerator){
                                                return 'Details';
                                            }
                                            if($record->devisGenerator){
                                                return 'Details';
                                            }
                                            return "Aucun Details";
                                        })
                                            ->columns(2)
                                            ->schema([

                                                \Filament\Infolists\Components\Section::make('Contact commercial')
                                                    ->columnSpan(['lg' => 1])
                                                    ->columns(2)
                                                    ->schema([
                                                        TextEntry::make('contact_c_name')
                                                            ->label('Nom')
                                                            ->limit(20)
                                                            ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->devis->customer->contact_c_name;
                                                            }
                                                            return null;
                                                        })
                                                        ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contact_c_phone')
                                                            ->label('Téléphone')
                                                             ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->devis->customer->contact_c_phone;
                                                            }
                                                            return null;
                                                        })
                                                           ->extraAttributes([ 'class' => 'font-bold']),

                                                           TextEntry::make('contact_c_email')
                                                            ->label('Email')
                                                             ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->devis->customer->contact_c_email;
                                                            }
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->devis->customer->contact_c_email;
                                                            }
                                                            return null;
                                                        })
                                                           ->extraAttributes([ 'class' => 'font-bold'])
                                                           ->columnSpanFull(),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact sur site')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contact_c_name')
                                                            ->label('Nom')
                                                            ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->contact_name;
                                                            }
                                                            return null;
                                                        })
                                                        ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contact_c_phone')
                                                            ->label('Téléphone')
                                                             ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->contact_phone;
                                                            }
                                                            return null;
                                                        })
                                                           ->extraAttributes([ 'class' => 'font-bold']),

                                                           TextEntry::make('contact_c_email')
                                                            ->label('Email')
                                                             ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->contact_email;
                                                            }
                                                            return null;
                                                        })
                                                           ->extraAttributes([ 'class' => 'font-bold'])
                                                           ->columnSpanFull(),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Localisation du site')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                       TextEntry::make('site')
                                                            ->label('Site')
                                                            ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->site;
                                                            }
                                                            return null;
                                                        })
                                                        ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('code_site')
                                                            ->label('Code')
                                                             ->getStateUsing(function (Generator $record) {
                                                            if($record->devisGenerator){
                                                                return $record->devisGenerator->code_site;
                                                            }
                                                            return null;
                                                        })
                                                           ->extraAttributes([ 'class' => 'font-bold']),

                                                           TextEntry::make('adresse')
                                                            ->label(label: 'Adresse')
                                                           ->extraAttributes([ 'class' => 'font-bold'])
                                                           ->columnSpanFull(),
                                                    ]),

                                                

                                                TextEntry::make('vu')
                                                    ->label('Vue sur la carte')
                                                    ->inlineLabel()
                                                    ->columnSpanFull(),

                                                \Filament\Infolists\Components\View::make('filament.infolist.components.map-pointer')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        if($record->devisGenerator){
                                                            return [
                                                            'lat' => $record->devisGenerator->devis->lat,
                                                            'lng' => $record->devisGenerator->devis->lng,
                                                            ];
                                                        }
                                                        return [
                                                            'lat' => $record->devisGenerator->devis->lat,
                                                            'lng' => $record->devisGenerator->devis->lng,
                                                        ];
                                                    })
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                                                    ->columnSpanFull(),

                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Interventions planifiées / en cours')

                            ->icon('heroicon-o-wrench-screwdriver')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make("devisGenerator.devis.interventions")
                                    ->label('')
                                   ->contained(false)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make(fn(Intervention $record) => InterventionType::from($record->type)->label(). " du " . DateUtils::format($record->date_planifiee))
                                        ->collapsible()
                                        ->collapsed(function (Intervention $record) {
                                            if($record->status == InterventionStatus::EN_COURS->value || $record->status == InterventionStatus::PLANIFIEE->value){
                                                return false;
                                            }
                                            return true;
                                        })
                                        ->extraAlpineAttributes(['class' => 'border-b border-gray-200'])
                                        ->schema([
                                            
                                        \Filament\Infolists\Components\Group::make()
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('status')
                                            ->label('')
                                            ->getStateUsing(function($record){ 
                                                 $stats = InterventionStatus::from($record->status)->label();

                                                 return BadgetWidget::interventionStatusBadget($stats);
                                                })
                                            ->html(),

                                            \Filament\Infolists\Components\Actions::make([
                                                \Filament\Infolists\Components\Actions\Action::make('view')
                                                    ->label('Détail')
                                                    ->url(fn($record) => url('admin/intervention-devis/'.$record->id))
                                                    ->icon('heroicon-o-eye')
                                                    ->color('gray')
                                                    ->size(ActionSize::Small),
                                            ])->alignRight(),
                                        ]),
                                        
                                        TextEntry::make('created_at')
                                            ->label('Créer le')
                                            ->inlineLabel()
                                            ->date('d/m/y à H:i'),

                                            \Filament\Infolists\Components\Group::make()
                                            ->columns(2)
                                            ->schema([
                                                TextEntry::make('identifiant')
                                                ->label('Numéro de Bon d\'intervention')
                                                ->extraAttributes(['class' => 'font-bold']),
    
                                            TextEntry::make('type')
                                                ->label('Type')
                                                ->getStateUsing(fn($record) => InterventionType::from($record->type)->label())
                                                ->extraAttributes(['class' => 'font-bold']),
                                            ]),

                                        TextEntry::make('description_panne')
                                            ->label('Description de la panne')
                                            ->color('secondary')
                                            ->columnSpanFull(),

                                        \Filament\Infolists\Components\Group::make()
                                            ->columns(4)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextEntry::make('date_prise_appel')
                                                    ->label('Date de prise d’appel')
                                                    ->date('d/m/Y')
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('date_planifiee')
                                                    ->label('Date planifiée')
                                                    ->date('d/m/Y')
                                                    ->color('danger'),

                                                TextEntry::make('start_date')
                                                    ->label('Date début')
                                                    ->date('d/m/Y')
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('end_date')
                                                    ->label('Date limite')
                                                    ->date('d/m/Y')
                                                    ->color('danger'),
                                            ]),
                                         ])
                                    ]),
                            ]),
                        // Tabs\Tab::make('Pièces de rechange')

                        //     ->icon('heroicon-o-cog-8-tooth')
                        //     ->iconPosition(IconPosition::After)
                        //     ->schema([
                        //         RepeatableEntry::make('pieces')
                        //             ->label('Pièces de rechange')
                        //             ->schema([
                        //                 \Filament\Infolists\Components\Group::make()
                        //                     ->columns(3)
                        //                     ->schema([
                        //                         ImageEntry::make('piece.image')
                        //                         ->label('')
                        //                         ->columnSpanFull()
                        //                         ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                        //                         TextEntry::make('piece.reference')
                        //                             ->label('Pièce')
                        //                             ->extraAttributes(['class' => 'font-bold']),
                                                    
                        //                         TextEntry::make('piece.designation')
                        //                             ->label('Désignation')
                        //                             ->extraAttributes(['class' => 'font-bold']),
                                                
                        //                             TextEntry::make('qty')
                        //                             ->label('Quantité')
                        //                             ->extraAttributes(['class' => 'font-bold']),

                                                
                        //                             TextEntry::make(name: 'intervention.identifiant')
                        //                             ->label('Intervention')
                        //                             ->color('success')
                        //                             ->url(fn($record) => url('admin/interventions/'.$record->intervention->id)),

                        //                         TextEntry::make('price')
                        //                             ->label('Prix unitaire')
                        //                             ->inlineLabel()
                        //                             ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                        //                             ->extraAttributes(['class' => 'font-bold'])
                        //                             ->columnSpan(['lg' => 2]),
                        //                     ]),
                        //             ])
                        //             ->grid(2)
                        //             ->columnSpanFull(),
                        //     ]),

                        Tabs\Tab::make('Etat avant/après')
                            ->icon('heroicon-o-arrow-path')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                               \Filament\Infolists\Components\View::make('filament.infolist.components.checklist-render')
                                    ->label('')
                                    ->viewData(['record'])
                            ]),
                            Tabs\Tab::make('Historique des interventions')
                            ->icon('heroicon-o-arrow-path')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                 Livewire::make(InterventionHistory::class)
                                    ->data([
                                        'generatorId' => $infolist->getRecord()->id,
                                        'model' => ReportLocation::class,
                                    ]),
                            ]),

                            Tabs\Tab::make('Historique des locations')
                            ->icon('heroicon-o-inbox-stack')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                 Livewire::make(LocationHistory::class)
                                    ->data([
                                        'generatorId' => $infolist->getRecord()->id,
                                        'model' => DevisGenerator::class,
                                    ]),
                            ]),
                    ]),

                    
            ]);
    }
}

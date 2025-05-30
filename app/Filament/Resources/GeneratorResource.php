<?php

namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages;
use App\Livewire\CheckList;
use App\Livewire\InterventionHistory;
use App\Models\ContractFacture;
use App\Models\Generator;
use App\Models\Intervention;
use App\Utils\DateUtils;
use App\Utils\NumberUtils;
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

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $navigationIcon  = 'icon-generator';
    protected static ?string $navigationGroup = 'Global';
    protected static ?string $navigationLabel = 'Groupes Electrogènes';
    protected static ?int $navigationSort     = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = Generator::count();
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
                                    ->label('Marque')
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('modele')
                                    ->label('Modèle')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('serial_number')
                                    ->label('Numéro de série')
                                    ->columnSpanFull(),

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
                                    ->label('Puissance (KVA)'),

                                TextInput::make('voltage')
                                    ->numeric()
                                    ->label('Tension (V)'),

                                TextInput::make('frequency')
                                    ->label('Fréquence (Hz)')
                                    ->numeric(),

                                TextInput::make('fuel_type')
                                    ->label('Type de carburant'),
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

                                    ->required(),

                                TextInput::make('houres')
                                    ->label('Heures de fonctionnement'),

                                TextInput::make('next_vidange')
                                    ->label('Prochaine vidange (h)')
                                    ->numeric(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

                TextColumn::make('name')
                    ->label('Marque')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('modele')
                    ->label('Modèle')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(function (Generator $record) {
                        return GeneratorStatus::from($record->status)->label();
                    })
                    ->colors([
                        'success' => 'Disponible',
                        'warning' => 'En maintenance',
                        'info'    => 'En location',
                        'danger'  => 'Indisponible',
                    ]),

                TextColumn::make('serial_number')
                    ->label('Numéro de série')
                    ->searchable(),

                TextColumn::make('power')
                    ->label('Puissance (KVA)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('voltage')
                    ->label('Tension (V)'),

                TextColumn::make('frequency')
                    ->label('Fréquence (Hz)'),

                TextColumn::make('fuel_type')
                    ->label('Type de carburant')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('houres')
                    ->label('Heures de fonc.')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('next_vidange')
                    ->label('Prochaine vidange')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start-up')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

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
                                                    ->badge()
                                                    ->getStateUsing(function (Generator $record) {
                                                        return GeneratorStatus::from($record->status)->label();
                                                    })
                                                    ->colors([
                                                        'success' => 'Disponible',
                                                        'warning' => ['Pas de GE assigné', 'En maintenance'],
                                                        'info'    => 'En location',
                                                        'danger'  => 'Indisponible',
                                                    ])
                                                    ->columnSpanFull(),

                                                ImageEntry::make('image')
                                                    ->label('')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                                TextEntry::make('name')
                                                    ->label('GE')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('modele')
                                                    ->label('Modèle')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('serial_number')
                                                    ->label('Numéro de série')
                                                    ->extraAttributes(['class' => 'font-bold text-danger']),

                                                TextEntry::make('power')
                                                    ->label('Puissance')
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('fuel_type')
                                                    ->label('Type de carburant')
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
                                                   ->extraAttributes([ 'class' => 'font-bold']),

                                                TextEntry::make('created_at')
                                                    ->label('Ajouté le')
                                                    ->date('d/m/Y')
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(3)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Contrat en cours')
                                            ->columns(2)
                                            ->schema([
                                                TextEntry::make('is_active')
                                                    ->label('')
                                                    ->badge()
                                                    ->getStateUsing(function (Generator $record) {
                                                        return $record->contractGenerator ? $record->contractGenerator->contract->is_active ? 'En cours' : 'Terminé' : 'Pas de contrat';
                                                    })
                                                    ->colors([
                                                        'success' => 'En cours',
                                                        'danger'  => 'Terminé',
                                                    ]),

                                                ImageEntry::make('contractGenerator.contract.customer.logo')
                                                    ->label('')
                                                    ->columnSpanFull()
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                                TextEntry::make('contractGenerator.contract.number')
                                                    ->label('N° contrat')
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('contractGenerator.contract.customer.name')
                                                    ->label('Client')
                                                    ->extraAttributes(['class' => 'font-bold']),

                                                TextEntry::make('contractGenerator.contract.forfait')
                                                    ->label('Forfait de maintenance mensuel')
                                                    ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                                                    ->extraAttributes(['class' => 'font-bold'])
                                                    ->columnSpanFull(),

                                                TextEntry::make('contractGenerator.contract.site')
                                                    ->label('Site')
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                                TextEntry::make('contractGenerator.contract.code_site')
                                                    ->label('Code')
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                                TextEntry::make('contractGenerator.contract.start_date')
                                                    ->date('d/m/Y')
                                                    ->label('A debuter le')
                                                   ->extraAttributes([ 'class' => 'font-bold']),
                                                TextEntry::make('contractGenerator.contract.end_date')
                                                    ->date('d/m/Y')
                                                    ->label('Se termine le')
                                                    ->color('danger'),

                                            ]),
                                    ]),

                                \Filament\Infolists\Components\Group::make()
                                    ->columnSpan(7)
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('Detail du contrat')
                                            ->columns(2)
                                            ->schema([
                                            TextEntry::make('contractGenerator.contract.adress')
                                                        ->label('Adresse')
                                                        ->columnSpanFull(),

                                                \Filament\Infolists\Components\Section::make('Contact commercial')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.customer.contact_c_name')
                                                            ->label('Nom')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.customer.contact_c_email')
                                                            ->label('Email')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.customer.contact_c_phone')
                                                            ->label('Téléphone')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact logistique')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.customer.contact_l_name')
                                                            ->label('Nom')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.customer.contact_l_email')
                                                            ->label('Email')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.customer.contact_l_phone')
                                                            ->label('Téléphone')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                    ]),

                                                \Filament\Infolists\Components\Section::make('Contact sur site')
                                                    ->columns(2)
                                                    ->columnSpan(['lg' => 1])
                                                    ->schema([
                                                        TextEntry::make('contractGenerator.contract.contact_name')
                                                            ->label('Nom')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.contact_email')
                                                            ->label('Email')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                        TextEntry::make('contractGenerator.contract.contact_phone')
                                                            ->label('Téléphone')
                                                           ->extraAttributes([ 'class' => 'font-bold']),
                                                    ]),

                                                TextEntry::make('vu')
                                                    ->label('Vue sur la carte')
                                                    ->inlineLabel()
                                                    ->columnSpanFull(),

                                                \Filament\Infolists\Components\View::make('filament.infolist.components.map-pointer')
                                                    ->label('')
                                                    ->getStateUsing(function (Generator $record) {
                                                        return [
                                                            'lat' => $record->contractGenerator->contract->lat,
                                                            'lng' => $record->contractGenerator->contract->lng,
                                                        ];
                                                    })
                                                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                                                    ->columnSpanFull(),

                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Interventions du contrat en cours')

                            ->icon('heroicon-o-wrench-screwdriver')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make("contractGenerator.contract.interventions")
                                    ->label('')
                                   ->contained(false)
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make(fn(Intervention $record) => InterventionType::from($record->type)->label(). " du " . DateUtils::format($record->date_planifiee))
                                        ->collapsible()
                                        ->collapsed(function (Intervention $record) {
                                            if($record->status == InterventionStatus::EN_COURS->value || $record->status == InterventionStatus::NON_COMMENCE->value){
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
                                            ->badge()
                                            ->getStateUsing(fn($record) => InterventionStatus::from($record->status)->label())
                                            ->colors([
                                                'warning' => "En cours",
                                                'info'  => "Non commencée",
                                                'danger' => "Annulée",
                                                'success' => "Terminée",
                                            ]),

                                            \Filament\Infolists\Components\Actions::make([
                                                \Filament\Infolists\Components\Actions\Action::make('view')
                                                    ->label('Détail')
                                                    ->url(fn($record) => url('admin/interventions/'.$record->id))
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

                                        // \Filament\Infolists\Components\View::make('filament.infolist.components.technicien-card')
                                        //         ->label('')
                                        //         ->viewData([
                                        //             'record' => fn ($record) => $record->interventionTechniciens,
                                        //         ])
                                        //         ->columnSpanFull(),

                                        // \Filament\Infolists\Components\Group::make()
                                        //     ->columnSpanFull()
                                        //     ->schema([
                                        //         RepeatableEntry::make('interventionTechniciens')
                                        //             ->alignCenter()
                                        //             ->extraAttributes(['class' => 'border-0 shadow-none p-0 bg-transparent'])
                                        //             ->grid(2)
                                        //             ->schema([
                                        //                 Grid::make()
                                        //                     ->columns(2)
                                        //                     ->schema([
                                        //                         ImageEntry::make("photo")
                                        //                             ->hiddenLabel()
                                        //                             ->inlineLabel()
                                        //                             ->circular()
                                        //                             ->size(100)
                                        //                             ->height(100),
                                        //                         \Filament\Infolists\Components\Group::make()
                                        //                             ->columnSpan(['lg' => 1])
                                        //                             ->schema([
                                        //                                 TextEntry::make('name')
                                        //                                     ->hiddenLabel()
                                        //                                     ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                        //                                 TextEntry::make('email')
                                        //                                     ->hiddenLabel()
                                        //                                     ->inlineLabel()
                                        //                                     ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                        //                                 TextEntry::make('phone')
                                        //                                     ->hiddenLabel()
                                        //                                     ->inlineLabel()
                                        //                                     ->extraAttributes(['class' => 'mb-0 gap-y-0 p-0']),
                                        //                             ]),
                                        //                     ]),
                                        //             ])
                                        //             ->label('Liste des techniciens'),
                                        //     ]),
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
                                Livewire::make(CheckList::class),
                            ]),
                            Tabs\Tab::make('Historique des interventions')
                            ->icon('heroicon-o-arrow-path')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                 Livewire::make(InterventionHistory::class)
                                    ->data([
                                        'generatorId' => $infolist->getRecord()->id,
                                    ]),
                            ]),
                    ]),

                    
            ]);
    }
}

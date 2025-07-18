<?php

namespace App\Filament\Resources;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Utils\BadgetWidget;
use App\Filament\Utils\WidgetUtils;
use App\Livewire\ShowGeneratorsTable;
use App\Models\Contract;
use App\Models\Generator;
use App\Utils\DateUtils;
use App\Utils\NumberUtils;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Contract::class;

    protected static ?string $label           = "Contrat";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Contrats';
    //protected static ?int $navigationSort     = 0;

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Infos générales')
                            ->columns(2)
                            ->schema([
                                WidgetUtils::customerSelectWidget()
                                    ->columnSpanFull(),

                                TextInput::make('number')
                                    ->label('Numéro de contrat')
                                    ->default("CTR-".NumberUtils::generate(6))
                                    ->required()
                                    ->unique(Contract::class, 'number', ignoreRecord: true)
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        // Section::make('Contact sur place')
                        //     ->columns(2)
                        //     ->schema([
                        //         TextInput::make('contact_name')
                        //             ->label('Nom')
                        //             ->required()
                        //             ->columnSpanFull(),
                        //         TextInput::make('contact_email')
                        //             ->label('Email')
                        //             ->required()
                        //             ->columnSpanFull(),
                        //         TextInput::make('contact_phone')
                        //             ->label('Téléphone')
                        //             ->tel()
                        //             ->required()
                        //             ->columnSpanFull(),
                        //     ])->columnSpan(['lg' => 1]),

                        Section::make('Infos contractuelles')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required()
                                    ->default(now())
                                    ->columnSpanFull(),

                                DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Statut')
                                    ->default(true)
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->required(),
                            ])->columnSpan(['lg' => 1]),

                    ])->columnSpan(['lg' => 1]),

                Repeater::make('generators')
                    ->formatStateUsing(function ($record) {
                        if(empty($record->generators)) return [];
                        return $record->generators?->map(function ($generator) {
                            return [
                                'generator_id' => $generator->id,
                                'forfait' => $generator->pivot->forfait,
                                'site' => $generator->pivot->site,
                                'code_site' => $generator->pivot->code_site,
                                'contact_name' => $generator->pivot->contact_name,
                                'contact_phone' => $generator->pivot->contact_phone,
                                'contact_email' => $generator->pivot->contact_email,
                            ];
                        })->toArray();
                    })
                    ->label('Groupes électrogènes')
                    ->createItemButtonLabel('Ajouter un GE')
                    ->deleteAction(fn(\Filament\Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
                    ->schema([
                        WidgetUtils::generatorSelectWidget(type: 2, isDispo: false)
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('forfait')
                                    ->label('Forfait de maintenance mensuel')
                                    ->numeric()
                                    ->columnSpanFull(),

                        TextInput::make('site')
                            ->label('Site'),

                        TextInput::make('code_site')
                            ->label('Code'),

                        Section::make('Contact sur place')
                            ->columns(2)
                            ->schema([
                                TextInput::make('contact_name')
                                    ->label('Nom'),
                                TextInput::make('contact_phone')
                                    ->label('Téléphone')
                                    ->tel(),
                                TextInput::make('contact_email')
                                    ->label('Email')
                                    ->columnSpanFull(),
                            ])->columnSpanFull(),

                    ])->columns(2)
                    ->grid(2)
                    ->columnSpan(['lg' => 3]),

            ])->columns(3);
    }



    public static function infolist(Infolist $infolist): Infolist
    {
        return static::buildInfolist($infolist);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('number')
                    ->label('N° contrat')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                TextColumn::make('is_active')
                    ->label('Statut')
                    ->getStateUsing(function(Contract $record){
                        return BadgetWidget::boleanToBadget($record->is_active, 'En cours', 'Terminé');
                    })
                    ->html(),

                TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Contract $record) => $record->customer->name)
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(10),

                // ImageColumn::make('customer.logo')
                //     ->label('Logo')
                //     ->circular()
                //     ->rounded()
                //     ->size(50)
                //     ->default('https://ui-avatars.com/api/?name=Logo&background=random'),

                TextColumn::make('generator')
                    ->getStateUsing(fn(Contract $record): string => (string) $record->generators->count())
                    ->label('Nombre deGE')
                    ->extraAttributes(['style' => 'font-weight: bold;'])
                    ->limit(50),

                TextColumn::make('customer.contact_c_phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Statut')
                    ->options([
                        '1' => 'En cours',
                        '0' => 'Terminé',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
            'view' => Pages\ViewContract::route('/{record}'),
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
            ->columns(7)
            ->schema([
                \Filament\Infolists\Components\Group::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\Section::make('Informations du contrat')
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                                TextEntry::make('is_active')
                                    ->label('')
                                    ->getStateUsing(function (Contract $record) {
                                        return BadgetWidget::boleanToBadget($record->is_active, 'En cours', 'Terminé');
                                    })
                                    ->html(),


                                ImageEntry::make('customer.logo')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'w-full d-flex justify-center']),

                                TextEntry::make('number')
                                    ->label('N° contrat')
                                    ->extraAttributes(['class' => 'font-bold']),

                                TextEntry::make('customer.name')
                                    ->label('Client')
                                    ->extraAttributes(['class' => 'font-bold']),

                                TextEntry::make('customer.contact_c_phone')
                                    ->label('Téléphone')
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                                TextEntry::make('customer.contact_c_email')
                                    ->label('Email')
                                    ->lineClamp(2)
                                    ->extraAttributes(['class' => 'font-bold text-danger']),


                                TextEntry::make('start_date')
                                    ->hiddenLabel()
                                    ->getStateUsing(function (Contract $record) {
                                        return $record->start_date ? "A debuter le " . DateUtils::format($record->start_date) : '';
                                    })
                                    ->extraAttributes(['class' => 'font-bold text-danger']),
                                TextEntry::make('end_date')
                                    ->hiddenLabel()
                                    ->getStateUsing(function (Contract $record) {
                                        return $record->end_date ? "Se termine le " . DateUtils::format($record->end_date) : '';
                                    })
                                    ->color('danger'),

                            ])
                    ]),


                \Filament\Infolists\Components\View::make('filament.infolist.components.generator-show-tab')
                    ->label('Groupe électrogènes')
                    ->extraAttributes(['class' => 'w-full d-flex justify-center'])
                    ->columnSpanFull(),

            ]);
    }
}

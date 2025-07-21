<?php

namespace App\Filament\Pages;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Filament\Resources\GeneratorResource\Pages\ViewIntervention;
use App\Filament\Resources\InterventionResource\Pages\EditIntervention;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Intervention;
use App\Utils\NumberUtils;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class RetiredPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    protected static ?string $model = Intervention::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $title = 'Retrait';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.retired-page';

            public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_RetiredPage');
    }
      public static function getNavigationBadge(): ?string
    {
        $count = Intervention::where('type',  InterventionType::RETRAIT->value)->count();
        return $count;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau retrait')
                ->modalHeading('Nouveau retrait')
                ->model(Intervention::class)
                ->form([
                    Grid::make()
                        ->columns(2)
                        ->schema([
                            Section::make('Informations sur le retrait')
                                ->columns(2)
                                ->schema([
                                TextInput::make('numero')
                                    ->label('Numéro')
                                    ->default(NumberUtils::intevention_numero('INT-LOC'))
                                    ->disabled()
                                    ->columnSpanFull(),

                                     TextInput::make('identifiant')
                                    ->label('Numéro de Bon de travaux')
                                     ->columnSpanFull(),

                                   WidgetUtils::contractSelectWidget('devis_id')
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->label("Devis"),

                                 WidgetUtils::generatorSelectWidget(isDispo:false)
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->visible(fn(callable $get) => $get('devis_id') != null),
                                    DatePicker::make('date_prise_appel')
                                        ->label('Date de prise d’appel')
                                        ->default(now())
                                        ->required(),

                                    DatePicker::make('date_planifiee')
                                        ->label('Date planifiée'),

                                    RichEditor::make('description_panne')
                                        ->label('Note')
                                        ->columnSpanFull(),
                                ])->columnSpan(['lg' => 1]),
                            Section::make('Infos internes')
            ->columns(1)
            ->schema([
                DatePicker::make('start_date')
                    ->label('Date de début'),

                DatePicker::make('end_date')
                    ->label('Date limite'),

                Select::make('status')
                    ->label('Statut')
                    ->options(collect(InterventionStatus::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                    ->required(),
                Select::make('technicien_id')
                    ->options(fn() => \App\Models\Technicien::all()->pluck('name', 'id'))
                    ->label('Techniciens assignés')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder('Sélectionner un technicien'),
            ])->columnSpan(['lg' => 1]),
                        ])
                ])->action(function (array $data) {
                    $data['numero'] = NumberUtils::intevention_numero('INT-LOC');
                    $devis = Devis::find($data['devis_id']);
                    $data['type_service'] = '0';
                    $data['type'] = InterventionType::RETRAIT->value;
                    //$data['identifiant'] = NumberUtils::generate();
                
                    $intervention = $devis->interventions()->create($data);

                    $technicians = $data['technicien_id'] ?? [];

                    $intervention->interventionTechniciens()->sync($technicians);

                    DevisGenerator::where('devis_id', $devis->id)
                        ->where('generator_id', $data['generator_id'])
                        ->update(['is_retired' => true]);

                    
                    Notification::make()
                        ->title('Retrait enregistré')
                        ->success()
                        ->send();
                })->visible(auth()->user()->hasPermissionTo('create_intervention::devis'))
        ];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(static::$model::query()
            ->where('type', InterventionType::RETRAIT)
            ->where('type_service', '=', 0)
            )
            ->columns(InterventionUtil::table("Devis"))
            ->recordUrl(fn($record) => url('admin/intervention-devis/'.$record->id))
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                    ->url(fn($record) => url('admin/intervention-devis/'.$record->id)),
                    EditAction::make()
                    ->url(fn($record) => url('admin/intervention-devis/'.$record->id.'/edit')),
                    // Action::make('cancel')
                    // ->label("Annuler")
                    // ->color('danger')
                    // ->icon('heroicon-o-x-circle')
                    // ->requiresConfirmation(),
                ]), 
            ])
            ->bulkActions([
                // ...
            ]);
    }

    
}

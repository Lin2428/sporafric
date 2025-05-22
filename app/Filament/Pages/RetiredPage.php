<?php

namespace App\Filament\Pages;

use App\Enum\InterventionType;
use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Contract;
use App\Models\Intervention;
use App\Utils\NumberUtils;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
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
                                    WidgetUtils::contractSelectWidget()
                                        ->columnSpanFull(),
                                    DatePicker::make('date_prise_appel')
                                        ->label('Date de prise d’appel')
                                        ->required(),

                                    DatePicker::make('date_planifiee')
                                        ->label('Date planifiée')
                                        ->required(),

                                    Textarea::make('description_panne')
                                        ->label('Note')
                                        ->required()
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])->columnSpan(['lg' => 1]),
                            InterventionUtil::infoInterne()->columnSpan(['lg' => 1]),
                        ])
                ])->action(function (array $data) {
                    $contract = Contract::find($data['contract_id']);
                    $contract->update(['is_retired' => true]);
                    $data['type_location'] = 2;
                    $data['type'] = InterventionType::RETRAIT;
                    $data['identifiant'] = NumberUtils::generate();
                    $contract->interventions()->create($data);
                    
                    Notification::make()
                        ->title('Retrait enregistré')
                        ->success()
                        ->send();
                })
        ];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(Intervention::query()->where('type', InterventionType::RETRAIT))
            ->columns(InterventionUtil::table())
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('cancel')
                    ->label("Annuler")
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation(),
                ]), 
            ])
            ->bulkActions([
                // ...
            ]);
    }
}

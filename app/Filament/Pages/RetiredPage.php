<?php

namespace App\Filament\Pages;

use App\Filament\Utils\InterventionUtil;
use App\Filament\Utils\WidgetUtils;
use App\Models\Contract;
use App\Models\Intervention;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
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
    protected static ?string $navigationGroup = 'Ronde';
    protected static ?string $title = 'Retrait';

    protected static string $view = 'filament.pages.retired-page';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau retrait')
                ->modalHeading('Nouveau retrait')
                ->form([
                    Group::make()
                        ->schema([
                            Section::make('Informations sur l’intervention')
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
                                ]),
                            InterventionUtil::infoInterne()
                        ])
                ])
        ];
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(Contract::query()->where('is_retired', true))
            ->columns([
                TextColumn::make('customer.name'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
}

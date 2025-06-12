<?php

// namespace App\Filament\Pages;

// use App\Enum\InterventionStatus;
// use App\Enum\InterventionType;
// use App\Filament\Resources\GeneratorResource\Pages\ViewIntervention;
// use App\Filament\Resources\InterventionResource\Pages\CreateIntervention;
// use App\Filament\Resources\InterventionResource\Pages\EditIntervention;
// use App\Filament\Utils\InterventionUtil;
// use App\Models\Intervention;
// use Filament\Forms\Components\CheckboxList;
// use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\Select;
// use Filament\Pages\Actions\CreateAction;
// use Filament\Pages\Page;
// use Filament\Tables\Actions\Action;
// use Filament\Tables\Actions\ActionGroup;
// use Filament\Tables\Actions\BulkActionGroup;
// use Filament\Tables\Actions\DeleteBulkAction;
// use Filament\Tables\Actions\EditAction;
// use Filament\Tables\Actions\ViewAction;
// use Filament\Tables\Concerns\InteractsWithTable;
// use Filament\Tables\Contracts\HasTable;
// use Filament\Tables\Filters\Filter;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\Relations\Relation;

// class DevisIntervention extends Page implements HasTable

// {
//     use InteractsWithTable;

//     protected static ?string $model = Intervention::class;
//     protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
//     protected static ?string $navigationGroup = 'Location';
//     protected static ?string $navigationLabel = 'Loc Interventions';
//     protected static ?string $title = 'Interventions';
//     protected static ?int $navigationSort = 1;

//     public static function getNavigationBadge(): ?string
//     {
//         $count = Intervention::where('type_location',  0)->count();
//         return $count;
//     }

//     protected function getHeaderActions(): array
//     {
//         return [
//             CreateAction::make()
//                 ->label('Nouvelle intervention')
//                 ->url(fn($record) => url('admin/interventions/create')),
//         ];
//     }

//     protected static function getBaseQuery(): Builder|Relation
//     {

//         return Intervention::query()
//          ->where('type_location', '==', 0);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->query(static::getBaseQuery())
//             ->defaultSort('created_at', 'desc')
//             ->columns(InterventionUtil::table())
//             ->recordUrl(fn($record) => url('admin/interventions/'.$record->id))
//             ->filters([
//                  Filter::make('status')
//                 ->form([
//                     CheckboxList::make('status')
//                     ->options(collect(InterventionStatus::cases())
//                         ->mapWithKeys(fn($status) => [$status->value => $status->label()])
//                         ->toArray()),

//                     DatePicker::make('date_planifiee')
//                         ->label('Date planifiée'),

//                     DatePicker::make('date_prise_appel')
//                         ->label('Date de prise d\'appel'),

//                     Select::make('type')
//                         ->label('Type')
//                         ->options(collect(InterventionType::cases())
//                             ->mapWithKeys(fn($status) => [$status->value => $status->label()])
//                             ->toArray()),
//                 ])
//             ])
//             ->actions([
//                 ActionGroup::make([
//                    ViewAction::make()
//                    ->url(fn($record) => url('admin/interventions/'.$record->id)),
//                     EditAction::make()
//                     ->url(fn($record) => url('admin/interventions/'.$record->id.'/edit')),
//               Action::make('cancel')
//                     ->label("Annuler")
//                     ->color('danger')
//                     ->icon('heroicon-o-x-circle')
//                     ->requiresConfirmation(),
//                 ]),
//             ])
//             ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'create' => CreateIntervention::route('/create'),
//             'edit' => EditIntervention::route('/{record}/edit'),
//             'view' => ViewIntervention::route('/{record}'),
//         ];
//     }

//     protected static string $view = 'filament.pages.devis-intervention';
// }

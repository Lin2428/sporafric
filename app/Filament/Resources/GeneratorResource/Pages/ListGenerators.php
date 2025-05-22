<?php

namespace App\Filament\Resources\GeneratorResource\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource;
use App\Models\Generator;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListGenerators extends ListRecords
{
    protected static string $resource = GeneratorResource::class;

    protected static ?string $title = 'Groupes électrogènes';
    public ?string $category = null;
    public array $products;

    public function updatedCategory($state)
    {
        $this->products = Generator::select('*')->get()->toArray();
    }
    public function getTabs(): array
    {
        return  [
            Tab::make("Tout"),

            Tab::make("Actifs")->query(
                fn($query) =>
                $query->where('status', '=', GeneratorStatus::EN_LOCATION->value)
            ),

            Tab::make("Inactifs")->query(
                fn($query) =>
                $query->where('status', '<>', GeneratorStatus::EN_LOCATION->value)
            ),



        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            /*Actions\CreateAction::make()
                ->label('Ajouter')
                ->icon('heroicon-o-plus'),*/
            Actions\Action::make('synchronuis')
                ->label('Synchroniser')
                ->icon('heroicon-o-arrow-path')
                ->modalHeading("Synchroniser les produits")
                ->modal()
                ->form([
                    Select::make('category')
                        ->label('Sélectionnez la catégorie')
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->options([
                            '1' => 'Categorie 1',
                            '2' => 'Categorie 2',
                            '3' => 'Categorie 3',
                            '4' => 'Categorie 4'
                        ])
                        ->multiple()
                        ->afterStateUpdated(function ($state) {
                            $this->updatedCategory($state);
                        }),

                    $this->getProductViewField(),
                ])
                ->modalSubmitActionLabel('Synchroniser')
        ];
    }

    public function getProductViewField(): ViewField
    {
        return ViewField::make('product_table')
            ->view('filament.generators.product-table');
    }
}

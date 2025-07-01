<?php

namespace App\Filament\Pages;

use App\Filament\Utils\WidgetUtils;
use App\Models\Generator;
use App\Models\Technicien;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class PrintGeneratorList extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Ronde';
    protected static ?string $navigationLabel = 'Impression de fiche';
    protected static ?string $title = 'Fiche de visite technique';

    public $data = [];

    public $customer_id = null;

     public function mount(): void
    {
        $this->refresh();
    }
    protected function refresh(): void
    {
        if ($this->customer_id == null) {
            return;
        }

        $this->data = Generator::whereHas('contractGenerator', function (Builder $query) {
            $query->whereHas('contract', function (Builder $query) {
                $query->where('customer_id', $this->customer_id);
            });
        })
        ->orWhereHas('devisGenerator', function (Builder $query) {
            $query->whereHas('devis', function (Builder $query) {
                $query->where('customer_id', $this->customer_id);
            });
        })
        ->get();


    }


    public function getHeaderActions(): array
{
    return [
        PrintAction::make()
        ->label('Imprimer'),
    ];
}

    public function form(Form $form): Form
    {
        $techniciens = Technicien::all()->pluck('name', 'id');
        return $form
            ->schema([

                WidgetUtils::customerSelectWidget()
                    ->label('Client')
                    ->afterStateUpdated(function ($state) {
                        $this->customer_id  = $state;
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(),

                Select::make('technicien_id')
                    ->label('Technicien')
                    ->options($techniciens),
            ])
            ->columns(2);
    }

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }

    
    protected static string $view = 'filament.pages.print-generator-list';
}

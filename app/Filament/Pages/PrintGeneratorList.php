<?php

namespace App\Filament\Pages;

use App\Filament\Utils\WidgetUtils;
use App\Models\Customer;
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

        public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_PrintGeneratorList');
    }
    public $data;

    public $groupes;

    public $clients = null;
    public $selectTechniciens;

     public function mount(): void
    {
        $this->refresh();
    }
    protected function refresh(): void
    {
        if ($this->clients == null) {
            return;
        }
        

        $this->data = Generator::with(['contractGenerator.contract', 'devisGenerator.devis'])
        ->where(function ($query) {
            $query->whereHas('contractGenerator.contract', function ($q) {
                $q->whereIn('customer_id', array_values($this->clients));
            })->orWhereHas('devisGenerator.devis', function ($q) {
                $q->whereIn('customer_id', array_values($this->clients));
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

                Select::make('clients')
                    ->label('Client')
                    ->options(Customer::all()->pluck('name','id'))
                    ->afterStateUpdated(function ($state) {
                        $this->refresh();
                    })
                    ->searchable()
                    ->multiple()
                    ->live(true)
                    ->required(),

                Select::make('selectTechniciens')
                    ->label('Technicien')
                     ->options($techniciens)
                     ->default($this->selectTechniciens)
                    ->afterStateUpdated(function ($state)use($techniciens) {
                        $this->selectTechniciens = $techniciens[$state];
                        $this->refresh();
                    })
                    ->live(true)
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

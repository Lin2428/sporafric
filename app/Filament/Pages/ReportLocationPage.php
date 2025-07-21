<?php
namespace App\Filament\Pages;

use App\Filament\Admin\Pages\DailyReportPage;
use App\Filament\Utils\WidgetUtils;
use App\Models\Devis;
use App\Models\Generator;
use App\Models\ReportLocation;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ReportLocationPage extends DailyReportPage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title           = 'Rapports de location';
  

    private $data;
    public $type;
    public $devis;
    public $generator;

         public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_ReportLocationPage');
    }

    protected function refresh(): void
    {
        if (empty($this->devisId) && empty($this->generatorId)) {
            $this->data = collect();
            return;
        }


        $query = ReportLocation::query();

        if (! empty($this->devisId)) {
            $query->where('devis_id', $this->devisId);
            $this->devis = Devis::find($this->devisId);
        }

        if (! empty($this->generatorId)) {
            $query->orWhere('generator_id', $this->generatorId);
            $this->generator = Generator::find($this->generatorId);
        }

        if (! empty($this->selectDateRange)) {
            $query->whereBetween('intervention_at', [$this->startDate,$this->endDate]);
        }

        $this->data = $query->get();

         

    }

        // public function getHeaderActions(): array
        // {
        //     return [
        //         PrintAction::make('print')
        //         ->label('Imprimer'),
        //     ];
        // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                   WidgetUtils::contractSelectWidget("devisId")
                    ->label('Devis')
                    ->afterStateUpdated(function ($state) {
                        $this->generatorId = null;
                         $this->generator = null;
                        $this->devisId  = $state;
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false)
                    ->visible(fn(callable $get) => $get('type') == '0'),

                WidgetUtils::generatorSelectWidget(name: "generatorId", isDispo: false)
                    ->afterStateUpdated(function ($state) {
                        $this->devisId  = null;
                         $this->devis = null;
                        $this->generatorId = $state;
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false)
                    ->visible(fn(callable $get) => $get('type') == '1'),

                 DateRangePicker::make("selectDateRange")
                    ->label('Période')
                    ->separator(' au ')
                    ->afterStateUpdated(function ($state) {
                     
                        [$start, $end] = explode(' au ', $state);

                        $this->selectDateRange = $state;
                        $this->startDate = Carbon::createFromFormat('d/m/Y', trim($start))->format('Y-m-d');
                        $this->endDate = Carbon::createFromFormat('d/m/Y', trim($end))->addDay()->format('Y-m-d');
                  
                        $this->refresh();
                    })
                    ->maxDate(Carbon::now())
                    ->live(true)
                    ->required(false),
                    
                Select::make('type')
                    ->label('Type de rapport')
                    ->options([
                        '0' => 'Devis',
                        '1' => 'Groupe électrogène',
                    ])
                    ->reactive()
                    ->extraAttributes(['class' => 'no-print']),
            ])
            ->columns(3);
    }

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }

      protected static string $view             = 'filament.pages.report-location';
}

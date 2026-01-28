<?php

namespace App\Filament\Pages;

use App\Enum\InterventionType;
use App\Filament\Admin\Pages\DailyReportPage;
use App\Filament\Utils\WidgetUtils;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\ReportMaintenance;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ReportMaintenancePage extends DailyReportPage implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title = 'Rapports de maintenance';

    protected static string $view = 'filament.pages.report-maintenance';

    private $data;
    public $type;
    public $customer;
    public $contract;
    public $generator;
    public $interventionType;
    public array $printableFields = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_ReportMaintenancePage');
    }

    protected function refresh(): void
    {
        if (empty($this->contractId) && empty($this->generatorId) && empty($this->customerId)) {
            $this->data = collect();
            return;
        }


        $query = ReportMaintenance::query();
        if (!empty($this->customerId)) {
            $query->where('customer_id', $this->customerId);
            $this->customer = Customer::findOrFail($this->customerId);
        }
        if (!empty($this->contractId)) {
            $query->where('contract_id', $this->contractId);
            $this->contract = Contract::findOrFail($this->contractId);
        }

        if (!empty($this->generatorId)) {
            $query->where('generator_id', $this->generatorId);
            $this->generator = Generator::findOrFail($this->generatorId);
        }

        if (! empty($this->selectDateRange)) {
            $query->whereBetween('intervention_at', [$this->startDate, $this->endDate]);
        }

        if ($this->interventionTypeId !== null) {
            $query->where('type_intervention', $this->interventionTypeId);
            $this->interventionType = $this->interventionTypeId;
        }

        $this->data = $query->get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                WidgetUtils::customerSelectWidget(name: "customerId")
                    ->afterStateUpdated(function ($state) {
                        $this->customerId = $state;
                        if ($state == null) {
                            $this->customer = null;
                        }
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false),

                WidgetUtils::contractSelectWidget(
                    "contractId",
                    placeholder: "Recherchez par numéro ou par client"
                )
                    ->afterStateUpdated(function ($state) {
                        $this->contractId = $state;
                        if ($state == null) {
                            $this->contract = null;
                        }
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false),

                WidgetUtils::generatorSelectWidget(type: 2, name: "generatorId", isDispo: false)
                    ->afterStateUpdated(function ($state) {
                        $this->generatorId = $state;
                        if ($state == null) {
                            $this->generator = null;
                        }
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false),


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

                Select::make('interventionTypeId')
                    ->afterStateUpdated(function ($state) {
                        $this->interventionTypeId = $state;
                        if ($state == null) {
                            $this->interventionType = null;
                        }
                        $this->refresh();
                    })
                    ->label("Type d'intervention")
                    ->options(collect(InterventionType::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                    ->reactive(),

                CheckboxList::make('printable_fields')
                    ->label('Champs imprimables')
                    ->reactive()
                    ->columns(10)
                    ->gridDirection('row')
                    ->options([
                        'start_at' => 'Date de début',
                        'end_at' => 'Date de fin',
                        'site' => 'Site',
                        'technicien' => 'Techniciens',
                        'generator' => 'GE',
                        'piece' => 'Pièces',
                        'qty_pice' => 'Qt pièces',
                        'amount_pieces' => 'M. pièces',
                        'amount_intervention' => 'M. intervention',
                        'total_mount' => 'M. total',
                    ])->afterStateUpdated(function ($state) {
                        $this->printableFields = $state;
                    })
                    ->columnSpanFull()
                    ->live(true),
            ])
            ->columns(5);
    }



    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}

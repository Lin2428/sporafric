<?php

namespace App\Filament\Pages;

use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use Carbon\Carbon;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class ReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?string $title = 'Rapports';
    protected static string $view = 'filament.pages.report-page';
    public $results, $start_date, $end_date, $contract_id, $generator_id, $customer_id, $month;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Rapport hebdomadaire')
                        ->icon('heroicon-s-calendar-days')
                        ->schema([
                            Group::make()
                                ->columns(3)
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label('A partir du'),

                                    DatePicker::make('end_date')
                                        ->label('Jusqu\'au'),
                                    Actions::make([
                                        Action::make('resetStars')
                                            ->hiddenLabel()
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(fn($state) => $this->submitTab1())
                                    ])->alignRight(),
                                ]),
                        ]),
                    Tabs\Tab::make('Rapport mensuel')
                        ->icon('heroicon-s-calendar-date-range')
                        ->schema([
                            Group::make()
                                ->columns(2)
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('month')
                                        ->type('month')
                                        ->label('Mois'),
                                    Actions::make([
                                        Action::make('submit1')
                                            ->hiddenLabel()
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(fn($state) => $this->submitTab2())
                                    ])->alignRight(),
                                ]),
                        ]),
                    Tabs\Tab::make('Location')
                        ->icon('heroicon-s-home-modern')
                        ->schema([
                            Group::make()
                                ->columns(2)
                                ->columnSpanFull()
                                ->schema([

                                    Actions::make([
                                        Action::make('submit2')
                                            ->hiddenLabel()
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(fn($state) => null)
                                    ])->alignRight(),
                                ]),
                        ]),
                    Tabs\Tab::make('Maintenance')
                        ->icon('heroicon-s-wrench-screwdriver')
                        ->schema([
                            Group::make()
                                ->columns(3)
                                ->columnSpanFull()
                                ->schema([
                                    WidgetUtils::generatorSelectWidget(isDispo: false),
                                    WidgetUtils::contractSelectWidget(),
                                    Actions::make([
                                        Action::make('submit3')
                                            ->hiddenLabel()
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(fn($state) => $this->submitTab4())
                                    ])->alignRight(),
                                ]),
                        ]),
                ]),
        ];
    }

    public function submitTab1()
    {
        $this->results = [];
        $interventions = Intervention::query();

        if ($this->start_date && $this->end_date) {
            $interventions->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }

        if (($this->start_date && $this->end_date)  !== null) {
            $this->results = $interventions->get();
        }
    }

    public function submitTab2()
    {
        $this->results = [];
        $month = Carbon::parse($this->month)->month;
        $year = Carbon::parse($this->month)->year;

        $interventions = Intervention::query();
        if ($this->month) {
            $interventions->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
        }
        if ($this->month !== null) {
            $this->results = $interventions->get();
        }
    }
    public function submitTab4()
    {
        $this->results = [];
        $interventions = Intervention::query();
        $interventions->orWhereHas('contract', function ($query) {
            if ($this->generator_id) {
                $query->where('generator_id', $this->generator_id);
            }
        });

        if ($this->contract_id) {
            $interventions->where('contract_id', $this->contract_id);
        }


        if (($this->contract_id || $this->generator_id)  !== null) {
            $this->results = $interventions
            ->groupBy('contract_id')
            //->selectRaw('count(*) as total_interventions')
            ->get();
        }

        dd($this->results);
      
    }
}

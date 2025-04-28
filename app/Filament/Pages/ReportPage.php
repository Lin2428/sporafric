<?php

namespace App\Filament\Pages;

use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use Carbon\Carbon;
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
    public $results, $start_date, $end_date, $generator_id, $customer_id, $month;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Rapport journalier')
                        ->icon('heroicon-s-calendar-days')
                        ->schema([
                            Group::make()
                                ->columns(4)
                                ->columnSpanFull()
                                ->schema([

                                    WidgetUtils::generatorSelectWidget(fn($state) => $this->submit())
                                        ->reactive()
                                        ->debounce(3000),
                                    WidgetUtils::customerSelectWidget()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state) => $this->submit())->reactive()
                                        ->debounce(3000),
                                    DatePicker::make('start_date')
                                        ->label('A partir du')
                                        ->reactive()
                                        ->debounce(3000)
                                        ->afterStateUpdated(fn($state) => $this->submit()),
                                    DatePicker::make('end_date')
                                        ->label('Jusqu\'au')
                                        ->reactive()
                                        ->reactive()
                                        ->debounce(3000)
                                        ->afterStateUpdated(fn($state) => $this->submit())
                                ]),
                        ]),
                    Tabs\Tab::make('Rapport mensuel')
                        ->icon('heroicon-s-calendar-date-range')
                        ->schema([
                            Group::make()
                                ->columns(3)
                                ->columnSpanFull()
                                ->schema([
                                    WidgetUtils::generatorSelectWidget(fn($state) => $this->submit())
                                        ->reactive()
                                        ->debounce(3000),
                                    WidgetUtils::customerSelectWidget()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state) => $this->submit())->reactive()
                                        ->debounce(3000),
                                    TextInput::make('month')
                                        ->type('month')
                                        ->label('Mois')
                                        ->reactive()
                                        ->debounce(3000)
                                        ->afterStateUpdated(fn($state) => $this->submit()),
                                ]),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $this->results = [];
        $month = Carbon::parse($this->month)->month;
        $year = Carbon::parse($this->month)->year;
        $interventions = Intervention::whereHas('contract', function ($query) {
            if ($this->generator_id) {
                $query->where('generator_id', $this->generator_id);
            }

            if ($this->customer_id) {
                $query->where('customer_id', $this->customer_id);
            }
        });

        if ($this->start_date && $this->end_date) {
            $interventions->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }

        if ($this->month) {
            $interventions->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();
        }

        $this->results = $interventions->get();
    }
}

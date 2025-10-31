<?php

namespace App\Filament\Pages;

use App\Enum\InterventionStatus;
use App\Enum\InterventionTypeService;
use App\Filament\Widgets\GeneratorStats;
use App\Filament\Widgets\TextWidget;
use App\Models\Devis;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Models\Intervention;
use Filament\Pages\Page;

class DashboardPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Tableau de bord';

    protected static ?string $title = 'Location';
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard-page';

    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_DashboardPage');
    }
    public $interventionsDuJour = [];

    public $ExpiredLocation = [];

    public $alerts = [];

    public function mount()
    {
        $this->interventionsDuJour = Intervention::whereBetween('date_planifiee', [now()->subWeek(), now()->addDays(1)->endOfDay()])
            ->where('status', '!=', InterventionStatus::TERMINEE->value)
            ->where('status', '!=', InterventionStatus::EN_COURS->value)
            ->with(['interventionTechniciens', 'pieces', 'contract', 'customer'])
            ->orderBy('date_planifiee', 'asc')
            ->get();

        $this->ExpiredLocation = DevisGenerator::whereHas('devis', function ($query) {
            $query->whereBetween('end_date', [
                now()->subDay(),
                now()
            ])
                ->orWhere('end_date', '<=', now())
                ->where('is_active', true);
        })
            ->where('is_retired', false)
            ->limit(5)
            ->with(['devis', 'generator'])
            ->get();



        foreach ($this->interventionsDuJour as $intervention) {
            $date = \Carbon\Carbon::parse($intervention->date_planifiee)->locale('fr');
            $today = now()->startOfDay();

            if ($date->lt($today)) {
                $bg = 'red';
                $icon = 'heroicon-o-x-mark';
                $title = 'EN RETARD';
            } elseif ($date->isToday()) {
                $bg = 'yellow';
                $icon = 'heroicon-o-exclamation-triangle';
                $title = 'AUJOURD\'HUI';
            } elseif ($date->isTomorrow()) {
                $bg = 'blue';
                $icon = 'heroicon-o-exclamation-circle';
                $title = 'DEMAIN';
            }

            $url = '/admin/interventions/' . $intervention->id;
            if ($intervention->type_service == InterventionTypeService::LOCATION->value) {
                $url = '/admin/intervention-devis/' . $intervention->id;
            }

            $label = \App\Enum\InterventionType::from($intervention->type)->label();
            $this->alerts[] = [
                'title' => $title,
                'label' => $label,
                'date' => $date,
                'icon' => $icon,
                'color' => $bg,
                'url' => $url,
                'hour' => null,
            ];
        }
        foreach ($this->ExpiredLocation as $location) {
            $date = \Carbon\Carbon::parse($location->devis->end_date)->locale('fr');
            $this->alerts[] = [
                'title' => "Location terminée",
                'label' => $location->devis->number,
                'date' => $date,
                'icon' => 'heroicon-o-clipboard-document-check',
                'color' => 'red',
                'url' => '/admin/generators/' . $location->generator_id,
                'hour' => null,
            ];
        }


        $vidandeCount = Generator::where('vidange', '=', false)
            ->get();

        foreach ($vidandeCount as $generator) {
            $colors = match (true) {
                $generator->next_vidange <= 5 => 'red',
                $generator->next_vidange <= 20 => 'yellow',
                default => 'green'
            };
            $url = "/admin/generators/$generator->id";
            if ($generator->type == 2) {
                $url = "/admin/contract-generators/$generator->id";
            }
            if ($generator) {
                $this->alerts[] = [
                    'title' => "Vidange en attente",
                    'label' => "GE " . $generator->name,
                    'date' => null,
                    'hour' => $generator->next_vidange,
                    'icon' => 'heroicon-o-arrow-path-rounded-square',
                    'color' => $colors,
                    'url' => $url
                ];
            }
        }
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use App\Models\Generator;
use App\Models\Intervention;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InterventionMaintenanceStats extends BaseWidget
{
    protected static ?string $model = Intervention::class;

    protected function getStats(): array
    {
        $generators = Generator::where('type', 1)
            ->count();

        $contract = Contract::count();

        $data = $this->getEloquentQuery()->first();
        $txEnCours = $data->en_cours == 0 ? 0 : ($data->en_cours / $data->total) * 100;
        $txRetard = $data->en_retard == 0 ? 0 : ($data->en_retard / $data->total) * 100;
        $txTermine = $data->ttrermine == 0 ? 0 : ($data->trermine / $data->total) * 100;
        return [
            Stat::make('GE total', $generators)
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('Contrat total', $contract)
                ->icon('heroicon-o-cube'),

            Stat::make('Int. total', $data->total)
                ->description('cette semaine')
                ->icon('heroicon-o-cube'),

            Stat::make('Int. en cours', $data->en_cours)
                ->description($txEnCours != 0 ? number_format($txEnCours, 2) . ' %' : "")
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('Int. en retard', $data->en_retard)
                ->description($txRetard != 0 ? number_format($txRetard, 2) . ' %' : "")
                ->icon('heroicon-o-cube')
                ->color('danger'),

            Stat::make('Int. cloturée', $data->trermine)
                ->description($txTermine != 0 ? number_format($txTermine, 2) . ' %' : "")
                ->icon('heroicon-o-cube')
                ->color('success'),
        ];
    }

    protected function getEloquentQuery()
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Lundi
        $endOfWeek = Carbon::now()->endOfWeek();     // Dimanche

        return static::$model::whereBetween('date_planifiee', [$startOfWeek, $endOfWeek])
            ->selectRaw('
            COUNT(*) AS total,
            COUNT(CASE WHEN status = 1 THEN 1 END) AS en_cours,
            COUNT(CASE WHEN status = 2 THEN 1 END) AS trermine,
            COUNT(CASE WHEN date_planifiee < now() AND status = 0 THEN 1 END) AS en_retard
        ')
        
            ->where('type_location', '1');
    }
}

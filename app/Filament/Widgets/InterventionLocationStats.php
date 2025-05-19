<?php

namespace App\Filament\Widgets;

use App\Models\Intervention;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InterventionLocationStats extends BaseWidget
{
    protected static ?string $model = Intervention::class;

    protected function getStats(): array
    {
        $data = $this->getEloquentQuery()->first();
        $txEnCours = ($data->en_cours / $data->total) * 100;
        $txAnnulee = ($data->annulee / $data->total) * 100;
        return [
            Stat::make('Total', $data->total)
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('En cours', $data->en_cours)
                ->description(number_format($txEnCours, 2) . ' %')
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('Annulée', $data->annulee)
                ->description(number_format($txAnnulee, 2) . ' %')
                ->icon('heroicon-o-cube')
                ->color('danger'),
        ];
    }
    protected function getEloquentQuery()
    {
        return static::$model::selectRaw('
            COUNT(*) AS total,
            COUNT(CASE WHEN status = 1 THEN 1 END) AS en_cours,
            COUNT(CASE WHEN status = 3 THEN 1 END) AS annulee
        ')
        ->where('type_location', '0');
    }
}

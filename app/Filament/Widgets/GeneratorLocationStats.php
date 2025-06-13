<?php

namespace App\Filament\Widgets;

use App\Models\Devis;
use App\Models\Generator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Utils\NumberUtils;

class GeneratorlocationStats extends BaseWidget
{
    protected static ?string $model = Generator::class;

    protected function getStats(): array
    {
        $data = $this->getEloquentQuery()->first();
        $locationFinish = Devis::where('end_date', '<=', now())
        ->count();

        $txLocation = $data->total_location == 0 ? 0 : ($data->total_location/$data->total) * 100;
        $txIndisponible = $data->indisponible == 0 ? 0 : ($data->indisponible/$data->total) * 100;

        return [
            Stat::make('GE total', $data->total)
                ->icon('icon-generator')
                ->color('success'),

            Stat::make('GE en location', $data->total_location)
                ->description($txLocation != 0 ? NumberUtils::format($txLocation, 2).' %' : "")
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('GE en attente', $data->indisponible)
                ->description($txIndisponible != 0 ? NumberUtils::format($txIndisponible, 2).' %': "")
                ->icon('heroicon-o-cube')
                ->color('danger'),

            Stat::make('GE en panne', $data->indisponible)
                ->description($txIndisponible != 0 ? NumberUtils::format($txIndisponible, 2).' %': "")
                ->icon('heroicon-o-cube')
                ->color('danger'),

            Stat::make('Loc cloturée', $locationFinish)
                ->icon('heroicon-o-cube'),

                
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getEloquentQuery()
    {
        return static::$model::where('type', 1)
        ->selectRaw('
        COUNT(*) AS total,
        COUNT(CASE WHEN status = 2 THEN 1 END) AS total_location,
        COUNT(CASE WHEN status = 3 THEN 1 END) AS indisponible,
        COUNT(CASE WHEN status = 4 THEN 1 END) AS attente
    ');
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Generator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Utils\NumberUtils;

class GeneratorStats extends BaseWidget
{
    protected static ?string $model = Generator::class;

    protected function getStats(): array
    {
        $data = $this->getEloquentQuery()->first();

        $txLocation = ($data->total_location/$data->total) * 100;
        $txIndisponible = ($data->indisponible/$data->total) * 100;

        return [
            Stat::make('Total', $data->total)
                ->icon('icon-generator')
                ->color('success'),

            Stat::make('En location', $data->total_location)
                ->description($txLocation != 0 ? NumberUtils::format($txLocation, 2).' %' : "")
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('En panne', $data->indisponible)
                ->description($txIndisponible != 0 ? NumberUtils::format($txIndisponible, 2).' %': "")
                ->icon('heroicon-o-cube')
                ->color('danger'),
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getEloquentQuery()
    {
        return static::$model::selectRaw('
        COUNT(*) AS total,
        COUNT(CASE WHEN status = 2 THEN 1 END) AS total_location,
        COUNT(CASE WHEN status = 3 THEN 1 END) AS indisponible
    ');
    }
}

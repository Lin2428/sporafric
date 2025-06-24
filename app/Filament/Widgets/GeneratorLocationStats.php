<?php

namespace App\Filament\Widgets;

use App\Enum\GeneratorStatus;
use App\Models\Devis;
use App\Models\Generator;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Utils\NumberUtils;
use Illuminate\Support\HtmlString;

class GeneratorlocationStats extends BaseWidget
{
    protected static ?string $model = Generator::class;

    protected function getStats(): array
    {
        $data = $this->getEloquentQuery()->first();
        $locationFinish = Devis::where('end_date', '<=', now())
        ->count();

        $vidangeCount = Generator::where('vidange', false)->count();

        $txLocation = $data->total_location == 0 ? 0 : ($data->total_location/$data->total) * 100;
        $txIndisponible = $data->indisponible == 0 ? 0 : ($data->indisponible/$data->total) * 100;
        $txAttente = $data->attente == 0 ? 0 : ($data->attente/$data->total) * 100;

        return [
            Stat::make('GE total', $data->total)
                ->icon('icon-generator')
                ->color('success')
                ->url(url('admin/generators')),

            Stat::make('GE en location', $data->total_location)
                ->description($txLocation != 0 ? NumberUtils::format($txLocation, 2).' %' : "")
                ->icon('heroicon-o-cube')
                ->color('success')
                ->url(url("admin/generators?tableFilters[status][status][0]=".GeneratorStatus::EN_LOCATION->value)),

            Stat::make('GE en attente', $data->attente)
                ->description($txAttente != 0 ? NumberUtils::format($txAttente, 2).' %': "")
                ->icon('heroicon-o-cube')
                ->color('primary')
                ->url(url("admin/generators?tableFilters[status][status][0]=".GeneratorStatus::EN_REVU->value)),

            Stat::make('GE en panne', $data->indisponible)
                ->description($txIndisponible != 0 ? NumberUtils::format($txIndisponible, 2).' %': "")
                ->icon('heroicon-o-cube')
                ->color('danger')
                ->url(url("admin/generators?tableFilters[status][status][0]=".GeneratorStatus::INDISPONIBLE->value)),

            Stat::make('Loc cloturée', $locationFinish)
                ->icon('heroicon-o-cube')
                ->url(url('admin/devis?tableFilters[is_active][value]=0')),

            Stat::make('Vidange en attente', $vidangeCount)
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->value(new HtmlString('<span class="text-red-500">'.$vidangeCount.'</span>'))
                ->url(url('/admin/generator-hours')),
                
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
        COUNT(CASE WHEN status = 1 THEN 1 END) AS attente
    ');
    }
}

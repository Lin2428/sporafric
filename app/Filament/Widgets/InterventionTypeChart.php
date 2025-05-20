<?php

namespace App\Filament\Widgets;

use App\Enum\InterventionType;
use App\Models\Intervention;
use Filament\Widgets\ChartWidget;

class InterventionTypeChart extends ChartWidget
{
    protected static ?string $model = Intervention::class;

    protected static ?string $heading = 'Intervention par type les 90 derniers jours';

    protected function getData(): array
    {
        $data = $this->getEloquentQuery()->get();
       
        $labels = [];
        $values = [];
        foreach ($data as $item) {
            $labels[] = InterventionType::from($item->type)->label();
            $values[] = $item->total;
        }
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $values,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    
                'borderColor'=> [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)'
                      ],
                ],
            ],
        ];
    }

    protected function getEloquentQuery()
    {
        return static::$model::selectRaw('
            COUNT(*) AS total,
            type
        ')
        ->whereMonth('created_at', [now()->subMonths(2), now()])
        ->groupBy('type');
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

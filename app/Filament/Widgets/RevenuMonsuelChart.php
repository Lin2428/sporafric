<?php

namespace App\Filament\Widgets;
use App\Models\RevenuMonsuel;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenuMonsuelChart extends ChartWidget
{
    protected static ?string $heading = 'Revenu mensuel ( FCFA )';
    protected static ?string $model = RevenuMonsuel::class;

    protected function getData(): array
    {
        $data = static::$model::get();
        $labels = [];
        $totalPiece = [];
        $totalPaye = [];
        $totalDevis = [];
        $totalIntervention = [];
        $totalContract = [];
        foreach ($data as $item) {
            $labels[] = Carbon::parse($item->mois)->translatedFormat('F');
            $totalPiece[] = (float)$item->revenu_pieces;
            $totalIntervention[] = (float)$item->revenu_intervention;
            $totalContract[] = (float)$item->revenu_contract;
            $totalDevis[] = (float)$item->revenu_devis;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Total Contrat',
                    'data' => $totalContract,
                    'backgroundColor' => '#2196F3',
                    'borderColor' => '#2196F3',
                ],
                [
                    'label' => 'Total Devis',
                    'data' => $totalDevis,
                    'backgroundColor' => '#FF9800',
                    'borderColor' => '#FF9800',
                ],
                [
                    'label' => 'Total Intervention',
                    'data' => $totalIntervention,
                    'backgroundColor' => '#f70743',
                    'borderColor' => '#f70743',
                ],
                [
                    'label' => 'Total Piece',
                    'data' => $totalPiece,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getEloquentQuery()
    {
        return static::$model::selectRaw("
            DATE_FORMAT(intervention_at, '%Y-%m') as mois,
            SUM(DISTINCT montant_piece) as total_piece,
            SUM(DISTINCT montant_paye) as total_paye,
            SUM(DISTINCT devis_montant) as total_devis

        ") ->groupBy(DB::raw("DATE_FORMAT(intervention_at, '%Y-%m')"))
        ->orderBy('mois');
    }

    protected function getType(): string
    {
        return 'line';
    }
}

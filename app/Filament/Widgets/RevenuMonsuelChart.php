<?php

namespace App\Filament\Widgets;

use App\Models\ReportMaintenance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenuMonsuelChart extends ChartWidget
{
    protected static ?string $heading = 'Revenu mensuel ( FCFA )';
    protected static ?string $model = ReportMaintenance::class;

    protected function getData(): array
    {
        $data = $this->getEloquentQuery()->get();
        $labels = [];
        $totalPiece = [];
        $totalPaye = [];
        $totalDevis = [];
        foreach ($data as $item) {
            $labels[] = Carbon::parse($item->mois)->translatedFormat('F');
            $totalPiece[] = (float)$item->total_piece;
            $totalPaye[] = (float)$item->total_paye;
            $totalDevis[] = (float)$item->total_devis;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Total Contrat',
                    'data' => $totalPaye,
                    'backgroundColor' => '#2196F3',
                    'borderColor' => '#2196F3',
                ],
                [
                    'label' => 'Total Piece',
                    'data' => $totalPiece,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                ],
                [
                    'label' => 'Total Devis',
                    'data' => $totalDevis,
                    'backgroundColor' => '#FF9800',
                    'borderColor' => '#FF9800',
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

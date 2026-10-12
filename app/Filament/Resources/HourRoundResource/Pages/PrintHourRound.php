<?php

namespace App\Filament\Resources\HourRoundResource\Pages;

use App\Filament\Resources\HourRoundResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

/**
 * Fiche papier de la tournée (même format que l'ancienne « Impression de fiche »),
 * HF pré-remplies quand le relevé est déjà remonté.
 */
class PrintHourRound extends Page
{
    use InteractsWithRecord;

    protected static string $resource = HourRoundResource::class;

    protected static string $view = 'filament.pages.print-hour-round';

    protected static ?string $title = 'Fiche de relevés';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }

    protected function getViewData(): array
    {
        $this->record->load([
            'technicien:id,name',
            'readings.customer:id,name',
            'readings.generator' => fn ($q) => $q->without(['contractGenerator', 'etat', 'files']),
        ]);

        return [
            'groupes' => $this->record->readings->groupBy(fn ($r) => $r->customer?->name ?? '—'),
        ];
    }
}

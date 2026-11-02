<?php

namespace App\Filament\Pages;

use App\Enum\RentalUsage;
use App\Services\RentalReportService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Rapport de location : amortissement, rentabilité et disponibilité des GE sélectionnés
 * sur une période, avec planning jour par jour (L / P / M / D) et export xlsx.
 */
class RentalReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $navigationLabel = 'Rapport de location';
    protected static ?string $title = 'Rapport de location';
    protected static string $view = 'filament.pages.rental-report-page';

    public ?array $data = [];

    /** Paramètres du dernier rapport généré (null = pas encore généré). */
    public ?array $query = null;

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_RentalReportPage');
    }

    /**
     * Colonnes du tableau (écran + export). type : text | int | dec | pct | money | date.
     * `help` = formule affichée au survol du « ? ».
     */
    public static function columns(): array
    {
        return [
            'Données GE' => [
                ['reference', 'ID GE', 'text'],
                ['name', 'Désignation', 'text'],
                ['kva', 'kVA', 'dec'],
                ['status', 'Statut', 'text'],
                ['hours', 'Compteur h', 'int'],
                ['days_location', 'Jours location', 'int'],
                ['days_pret', 'Jours prêt', 'int', 'Jours pendant lesquels le GE est prêté : GE de remplacement posé chez un client par une intervention « Remplacement » (jusqu\'au retour du GE d\'origine, au retrait ou à la fin de la location), + commandes au client « PRÊT GE ». Pendant ce temps le GE remplacé compte en maintenance.'],
                ['days_maintenance', 'Jours maintenance', 'int', 'Jours d\'immobilisation pour maintenance ou réparation : commandes au client « MAINTENANCE GE » + jours d\'intervention + consommations internes (1 jour à la date de la commande), hors jours de location / prêt.'],
                ['days_dotation', 'Jours dotation', 'int'],
                ['occupation', 'Taux d\'occupation %', 'pct', 'Taux d\'occupation (%) = (Jours loués + jours prêtés) ÷ Jours calendaires × 100'],
                ['availability', 'Taux de disponibilité %', 'pct', 'Taux de disponibilité (%) = (Jours calendaires − Jours d\'immobilisation : panne / maintenance / intervention) ÷ Jours calendaires × 100'],
                ['booking', 'Taux de réservation %', 'pct', 'Taux de réservation (%) = (Jours loués + jours prêtés) ÷ (Jours calendaires − Jours d\'immobilisation) × 100'],
            ],
            'Client' => [
                ['customer', 'Client / Affec. actuel', 'text'],
                ['site', 'Site actuel', 'text'],
                ['commissioning', 'Mise en service', 'date'],
                ['next_vidange', 'Prochaine vidange h', 'int'],
                ['rental_status', 'Location en cours', 'text'],
            ],
            'Financier' => [
                ['pr', 'Prix de revient HT', 'money', 'Prix de revient HT du GE, saisi dans la fiche GE (non repris d\'Odoo).'],
                ['threshold', 'Seuil d\'amortissement', 'money', 'Seuil d\'amortissement = Prix de revient HT × coefficient de frais généraux et de siège (Paramètres location).'],
                ['revenue_range', 'Locations HT période', 'money', 'Montant HT des locations au prorata des jours compris dans la période.'],
                ['revenue', 'Locations HT cumulées', 'money', 'Montant HT de toutes les locations confirmées depuis l\'origine.'],
                ['costs', 'Coûts cumulés', 'money', 'Coûts cumulés = montant des interventions + pièces consommées (quantité × prix) + consommations internes Odoo (HT).'],
                ['profitability', 'Rentabilité %', 'pct', 'Rentabilité (%) = (Locations HT cumulées − Coûts cumulés) ÷ Prix de revient × 100'],
                ['remaining', 'Reste à amortir', 'money', 'Reste à amortir = Seuil d\'amortissement − (Locations HT cumulées − Coûts cumulés), minimum 0.'],
                ['amortized_hours', 'Amorti à (h)', 'int', 'Compteur d\'heures auquel le GE est amorti, au rythme actuel : Seuil d\'amortissement ÷ ((Locations HT cumulées − Coûts cumulés) ÷ Compteur h). À comparer au compteur h.'],
                ['financial_counter', 'Compteur financier', 'money', 'Compteur financier = (Locations HT cumulées − Coûts cumulés) − Seuil d\'amortissement ; à zéro tant que le GE n\'est pas amorti.'],
            ],
            'Récapitulatif' => [
                ['orders', 'N° commandes / BL (factures) sur la période', 'text'],
            ],
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'generator_ids' => [],
            'from'          => now()->subYear()->addDay()->toDateString(),
            'to'            => now()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('from')->label('Date début')->required()->native(false)->displayFormat('d/m/Y'),
                    DatePicker::make('to')->label('Date fin')->required()->native(false)->displayFormat('d/m/Y')->afterOrEqual('from'),
                ]),
                CheckboxList::make('generator_ids')
                    ->label('Groupes électrogènes')
                    ->options(fn () => RentalReportService::selectableGenerators()
                        ->mapWithKeys(fn ($g) => [$g->id => trim(($g->reference ? "{$g->reference} — " : '') . $g->name . ($g->power ? " ({$g->power} kVA)" : ''))])
                        ->all())
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3)
                    ->gridDirection('row')
                    ->required(),
            ]);
    }

    public function generate(): void
    {
        $data = $this->form->getState();
        $this->query = [
            'generator_ids' => array_map('intval', $data['generator_ids']),
            'from'          => $data['from'],
            'to'            => $data['to'],
        ];
        unset($this->report);
    }

    /** Recalculé à chaque rendu (rien de volumineux dans l'état Livewire). */
    #[Computed]
    public function report(): ?array
    {
        if (! $this->query) {
            return null;
        }

        return (new RentalReportService())->build(
            $this->query['generator_ids'],
            Carbon::parse($this->query['from']),
            Carbon::parse($this->query['to']),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exporter (.xlsx)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->disabled(fn () => ! $this->query)
                ->action(fn () => $this->export()),
            Action::make('settings')
                ->label('Paramètres')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('gray')
                ->url(RentalSettingsPage::getUrl())
                ->visible(fn () => RentalSettingsPage::canAccess()),
        ];
    }

    public function export(): ?StreamedResponse
    {
        $report = $this->report;
        if (! $report) {
            Notification::make()->title('Générez d\'abord le rapport')->warning()->send();
            return null;
        }

        $filename = 'rapport-location-' . $report['from']->format('Ymd') . '-' . $report['to']->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($report) {
            $writer = new Writer();
            $writer->openToFile('php://output');

            $bold = (new Style())->setFontBold();
            $columns = collect(static::columns())->flatten(1);
            $writer->addRow(Row::fromValues([
                ...$columns->map(fn ($c) => $c[1])->all(),
                ...collect($report['columns'])->map(fn ($c) => $report['granularity'] === 'day'
                    ? Carbon::parse($c['start'])->format('d/m/Y')
                    : $c['label'] . ' (' . Carbon::parse($c['start'])->format('d/m') . ')')->all(),
            ], $bold));

            $usageStyles = collect(RentalUsage::cases())->mapWithKeys(fn (RentalUsage $u) => [$u->value => (new Style())
                ->setBackgroundColor(ltrim($u->colors()[0], '#'))
                ->setFontColor(ltrim($u->colors()[1], '#'))
                ->setFontBold()]);

            foreach ($report['rows'] as $row) {
                $cells = $columns->map(function ($c) use ($row) {
                    $value = $row[$c[0]] ?? null;
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('d/m/Y');
                    }
                    if ($c[0] === 'status' && ! empty($row['status_colors'])) {
                        return Cell::fromValue($value, (new Style())
                            ->setBackgroundColor(ltrim($row['status_colors'][0], '#'))
                            ->setFontColor(ltrim($row['status_colors'][1], '#')));
                    }

                    return Cell::fromValue($value); // nombres exportés en nombres
                })->all();

                foreach ($row['cells'] as $cell) {
                    $cells[] = $cell
                        ? Cell::fromValue($cell['code'], $usageStyles[$cell['code']])
                        : Cell::fromValue('');
                }
                $writer->addRow(new Row($cells));
            }

            $writer->addRow(Row::fromValues([]));
            foreach (RentalUsage::cases() as $usage) {
                $writer->addRow(new Row([Cell::fromValue($usage->value, $usageStyles[$usage->value]), Cell::fromValue($usage->label())]));
            }

            $writer->close();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}

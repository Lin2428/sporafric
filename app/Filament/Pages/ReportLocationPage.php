<?php
namespace App\Filament\Pages;

use App\Filament\Admin\Pages\DailyReportPage;
use App\Filament\Utils\WidgetUtils;
use App\Models\ReportLocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class ReportLocationPage extends DailyReportPage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rapport';
    protected static ?string $title           = 'Rapports de location';
    protected static string $view             = 'filament.pages.report-location';

    private $data;
    public $type;

         public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('page_ReportLocationPage');
    }

    protected function refresh(): void
    {
        if (empty($this->devisId) && empty($this->generatorId)) {
            $this->data = collect();
            return;
        }

        $query = ReportLocation::query();

        if (! empty($this->devisId)) {
            $query->where('devis_id', $this->devisId);
        }

        if (! empty($this->generatorId)) {
            $query->orWhere('generator_id', $this->generatorId);
        }

        $this->data = $query->get();

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                WidgetUtils::contractSelectWidget("devisId")
                    ->label('Devis')
                    ->afterStateUpdated(function ($state) {
                        $this->generatorId = null;
                        $this->devisId  = $state;
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false)
                    ->visible(fn(callable $get) => $get('type') == '0'),

                WidgetUtils::generatorSelectWidget(name: "generatorId", isDispo: false)
                    ->afterStateUpdated(function ($state) {
                        $this->devisId  = null;
                        $this->generatorId = $state;
                        $this->refresh();
                    })
                    ->live(true)
                    ->required(false)
                    ->visible(fn(callable $get) => $get('type') == '1'),

                Select::make('type')
                    ->label('Type de rapport')
                    ->options([
                        '0' => 'Devis',
                        '1' => 'Groupe électrogène',
                    ])
                    ->reactive()
                    ->extraAttributes(['class' => 'no-print']),
            ])
            ->columns(2);
    }

    protected function viewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}

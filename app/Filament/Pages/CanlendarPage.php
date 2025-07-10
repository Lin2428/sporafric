<?php
namespace App\Filament\Pages;

use App\Enum\InterventionStatus;
use App\Filament\Utils\WidgetUtils;
use App\Filament\Widgets\CalendarView;
use App\Models\Technicien;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class CanlendarPage extends Page implements HasForms
{
    use InteractsWithForms;
    

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $title           = 'Planning des interventions';
    protected static ?string $navigationGroup = 'Tableau de bord';
    protected static ?int $navigationSort     = 2;

    protected static string $view = 'filament.pages.canlendar-page';

    public $technicien;
    public $status;
    public $customer_id;
    public $type;

    public function getFooterWidgets(): array
    {
        return [
            CalendarView::make([
                'technicien' => $this->technicien,
                'status'     => $this->status,
                'customer_id' => $this->customer_id,
                'type' => $this->type
            ]),
        ];
    }

    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        $technicians = Technicien::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return $form
            ->schema([
                Group::make([
                    WidgetUtils::customerSelectWidget()
                        ->columnSpanFull()
                        ->label('Filtrer par client')
                        ->default( session('customer_id'))
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            session(['customer_id' => $state]);
                            return redirect(request()->header('Referer'));
                        }),
                    Select::make('type')
                        ->label('Filtrer par type')
                        ->default( session('type'))
                        ->options([
                            '0' => 'Location',
                            '1' => 'Maintenance',
                        ])
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            session(['type' => $state]);
                            return redirect(request()->header('Referer'));
                        })
                        ->placeholder('Sélectionnez un type'),
                    Select::make('technicien')
                        ->label('Filtrer par technicien')
                        ->options($technicians)
                        ->default( session('technicien'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            session(['technicien' => $state]);
                            return redirect(request()->header('Referer'));
                        })
                        ->placeholder('Sélectionnez un technicien'),

                    Select::make('status')
                        ->label('Filtrer par statut')
                        ->options(
                            collect(InterventionStatus::cases())
                                ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                                ->toArray()
                        )
                        ->default( session('status'))
                        ->reactive()
                        ->searchable()
                        ->afterStateUpdated(function ($state) {
                            session(['status' => $state]);
                            return redirect(request()->header('Referer'));
                        })
                        ->placeholder('Sélectionnez un statut'),

                    
                ])->columns(3),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Enum\InterventionTypeService;
use App\Filament\Utils\WidgetUtils;
use App\Models\Intervention;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CalendarView extends CalendarWidget
{
    //protected string $calendarView = 'resourceTimeGridWeek';
    protected string|\Closure|HtmlString|null $heading = 'Planning des interventions';

    protected bool $dateClickEnabled           = true;
    protected bool $dateSelectEnabled          = true;
    protected bool $noEventsClickEnabled       = true;
    protected bool $eventClickEnabled          = true;
    protected ?string $defaultEventClickAction = 'edit';
    protected bool $allDay                     = true;
    protected bool $eventDragEnabled           = true;
    protected bool $eventResizeEnabled         = true;

    public $technicien;
    public $status;
    public $customer_id;
    public $type;

    protected $listeners = ['reloadCalendar' => '$refresh'];

    /**
     * Get the options for the calendar.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'nowIndicator'  => true,
            'slotDuration'  => '00:15:00',
            'allDay'        => false,
            // Vue Mois uniquement : la version de vkurko embarquée n'a pas de vraie vue « dayGridWeek »
            // (et une vue semaine horaire serait vide, les interventions sont « toute la journée »).
            'headerToolbar' => [
                'start'  => 'today prev,next',
                'center' => 'title',
                'end'    => '',
            ],
            'buttonText' => [
                'today' => 'Aujourd\'hui',
            ],
            // Hauteur automatique : avec une hauteur fixe + « + N autres », vkurko mesure mal les
            // cartes personnalisées et elles débordent sur la semaine suivante.
            'dayMaxEvents' => false,
        ];
    }

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        $start = $fetchInfo['start'] ?? now()->startOfMonth();
        $end   = $fetchInfo['end'] ?? now()->endOfMonth();

        return Intervention::whereBetween('date_planifiee', [$start, $end])
            ->when($this->customer_id != null, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('customer', function ($q) {
                        $q->where('id', $this->customer_id);
                    })
                        ->orWhereHas('contract', function ($q) {
                            $q->where('customer_id', $this->customer_id);
                        })
                        ->orWhereHas('devis', function ($q) {
                            $q->where('customer_id', $this->customer_id);
                        });
                });
            })
            ->when($this->type !== null, function ($q) {
                $q->where('type_service', $this->type);
            })
            ->when($this->technicien, function ($q) {
                $q->whereHas('interventionTechniciens', function ($q) {
                    $q->where('technicien_id', $this->technicien);
                });
            })
            ->when($this->status != null, function ($q) {
                $q->where('status', $this->status);
            })
            ->get()
            ->map(fn(Intervention $intervention) => $intervention->toCalendarEvent());
    }

    public function editAction(): Action
    {
        $intervention = $this->getEventRecord();

        return Action::make('edit')
            ->slideOver()
            ->label('Modifier l’intervention')
            ->modalWidth('lg')
            ->modalAlignment(Alignment::Right)
            ->form([
                Select::make('type_service')
                    ->label('Location ou Maintenance ?')
                    ->options(collect(InterventionTypeService::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                    ->default($this->getRecord()->type_service)
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(collect(InterventionType::cases())->map(fn(InterventionType $type) => $type->label()))
                    ->required()
                    ->label('Type')
                    ->default(InterventionType::from($intervention->type)->value),

                Select::make('status')
                    ->options(collect(InterventionStatus::cases())->map(fn(InterventionStatus $status) => $status->label()))
                    ->required()
                    ->label('Status')
                    ->default(InterventionStatus::from($intervention->status)->value),

                WidgetUtils::contractSelectWidget()
                    ->default($intervention->contract_id)
                    ->visible(function () {
                        return optional($this->getRecord())->contract_id !== null;
                    })
                    ->disabled(),

                WidgetUtils::contractSelectWidget('devis_id')
                    ->label('Devis')
                    ->default(function () {
                        return $this->getRecord()->devis_id != null ? $this->getRecord()->devis_id : null;
                    })
                    ->visible(fn() => $this->getRecord()->devis_id != null)
                    ->disabled(),

                Section::make('Information sur le client')
                    ->columns(2)
                    ->schema([WidgetUtils::customerSelectWidget()->default($intervention->customer_id)->columnSpanFull(), TextInput::make('generator_name')->label('Marque du GE')->default($intervention->generator_name), TextInput::make('power')->label('Puissance (kVA)')->numeric()->default($intervention->power), TextInput::make('serial_number')->label('Numéro de série')->default($intervention->power)->columnSpanFull()])
                    ->visible(fn() => $this->getRecord()->type_activite == '0'),

                WidgetUtils::generatorSelectWidget(type: null, isDispo: false)
                    ->required()
                    ->default($this->getRecord()->generator_id)
                    ->visible(fn() => $this->getRecord()->generator_id != null),

                Group::make()
                    ->columns(2)
                    ->schema([DateTimePicker::make('start_date')->label('Date de début')->default($intervention->start_date), DateTimePicker::make('end_date')->label('Date de fin')->default($intervention->end_date)]),

                RichEditor::make('description_panne')
                    ->label('Constat')
                    ->default($this->getRecord()->description_panne),

                RichEditor::make('travaux')
                    ->label('Travaux effectués')
                    ->default($this->getRecord()->travaux)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data) use ($intervention) {
                Intervention::where('id', '=', $this->getRecord()->id)->update($data);

                Notification::make()->success()->body("L'intervention mis à jour avec succès !")->send();

                $this->dispatch('reloadCalendar');
            })
            ->extraModalFooterActions([
                Action::make('view')
                    ->action(function () {
                       
                        if ($this->getRecord()->type_service == InterventionTypeService::MAINTENANCE->value) {
                            redirect()->route('admin.interventions', ['id' => $this->getRecord()->id]);
                        } else {
                            redirect()->route('admin.intervention.devis', ['id' => $this->getRecord()->id]);
                        }
                    })
                    ->color('info')
                    ->label('Details'),
            ]);
    }

    /**
     * Handle the date click event.
     *
     * @param array $info
     * @return void
     */
    public function onDateClick(array $info = []): void {}

    /**
     * Handle the date select event.
     *
     * @param array $info
     * @return void
     */
    public function onDateSelect(array $info = []): void {}

    /**
     * Étirement d'une intervention sur plusieurs cases : la fin est décalée du nombre de jours
     * ajoutés / retirés (heure conservée).
     */
    public function onEventResize(array $info = []): bool
    {
        parent::onEventResize($info);

        $record = $this->getEventRecord();
        $days   = $this->dayShift($info, 'end');
        if ($days === 0) {
            return true;
        }

        $currentEnd = $record->end_date ?? $record->start_date ?? $record->date_planifiee;
        $record->update(['end_date' => Carbon::parse($currentEnd)->addDays($days)]);

        $this->dispatch('reloadCalendar');
        return true;
    }

    /**
     * Glisser-déposer sur une autre case : la date planifiée ET les dates de début / fin sont
     * décalées du même nombre de jours (heures conservées). La date planifiée sert au chargement
     * du planning et aux interventions « du jour » de l'app mobile.
     */
    public function onEventDrop(array $info = []): bool
    {
        // Don't forget to call the parent method to resolve the event record
        parent::onEventDrop($info);
        $record = $this->getEventRecord();

        $days = $this->dayShift($info, 'start');
        if ($days === 0) {
            return true;
        }

        $shift = fn ($date) => $date ? Carbon::parse($date)->addDays($days) : null;
        $record->update([
            'date_planifiee' => $shift($record->date_planifiee),
            'start_date'     => $shift($record->start_date),
            'end_date'       => $shift($record->end_date),
        ]);

        Notification::make()
            ->success()
            ->body('Intervention déplacée au ' . Carbon::parse($record->date_planifiee)->format('d/m/Y'))
            ->send();

        $this->dispatch('reloadCalendar');
        return true;
    }

    /**
     * Nombre de jours de décalage entre l'ancien et le nouvel événement. Les deux dates arrivent
     * sérialisées de la même façon par le navigateur (UTC), la différence est donc exacte quel que
     * soit le fuseau ; à défaut, on lit le `delta` de vkurko/calendar.
     */
    private function dayShift(array $info, string $edge): int
    {
        $old = data_get($info, "oldEvent.{$edge}");
        $new = data_get($info, "event.{$edge}");
        if ($old && $new) {
            return (int) round(Carbon::parse($old)->diffInDays(Carbon::parse($new), false));
        }

        $delta = (array) data_get($info, $edge === 'start' ? 'delta' : 'endDelta', []);

        return (int) (($delta['years'] ?? 0) * 365 + ($delta['months'] ?? 0) * 30 + ($delta['days'] ?? 0)
            + intdiv((int) ($delta['seconds'] ?? 0), 86400));
    }

    public function getEventContent(): null | string | array
    {
        // return a blade view
        return view('components.calendar-event');

        // return a HtmlString
        //dd($intervention);
        //return new HtmlString('<div>My event</div>');
    }
    public function authorize($ability, $arguments = [])
    {

        return true;
    }
}

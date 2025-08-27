<?php
namespace App\Filament\Widgets;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
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
            'title'        => 'Planing',
            'nowIndicator' => '',
            'slotDuration' => '00:15:00',
            'allDay'       => false,
        ];
    }

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        $start = $fetchInfo['start'] ?? now()->startOfMonth();
        $end   = $fetchInfo['end'] ?? now()->endOfMonth();

        return Intervention::whereBetween('start_date', [$start, $end])
            ->whereBetween('date_planifiee', [$start, $end])
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
                    ->options(['1' => 'Maintenance', '0' => 'Location'])
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
                    ->schema([DateTimePicker::make('start_date')->required()->label('Date de début')->default($intervention->start_date), DateTimePicker::make('end_date')->required()->label('Date de fin')->default($intervention->end_date)]),

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
                        if ($this->getRecord()->type_service === 1) {
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
    public function onDateClick(array $info = []): void
    {}

    /**
     * Handle the date select event.
     *
     * @param array $info
     * @return void
     */
    public function onDateSelect(array $info = []): void
    {}

    public function onEventResize(array $info = []): bool
    {
        parent::onEventResize($info);

        $record = $this->getEventRecord();

        $eventEndDate = Carbon::make($info['event']['end'])->format('Y-m-d');
        $eventEndTime = $record->end_date
        ? Carbon::make($record->end_date)->format('H:i')
        : '12:00';

        $end = $eventEndDate . '-' . $eventEndTime;

        $this->getEventRecord()->update([
            'end_date' => $end,
        ]);
        $this->dispatch('reloadCalendar');
        return true;
    }

    public function onEventDrop(array $info = []): bool
    {
        // Don't forget to call the parent method to resolve the event record
        parent::onEventDrop($info);
        $record = $this->getEventRecord();

        $eventStartDate = Carbon::make($info['event']['start'])->addDay()->format('Y-m-d');
        $eventStartTime = $record->start_date
        ? Carbon::make($record->start_date)->format('H:i')
        : '11:00';

        $eventEndDate = Carbon::make($info['event']['end'])->format('Y-m-d');
        $eventEndTime = $record->end_date
        ? Carbon::make($record->end_date)->format('H:i')
        : '12:00';

        $start = $eventStartDate . '-' . $eventStartTime;
        $end   = $eventEndDate . '-' . $eventEndTime;
        $this->getEventRecord()->update([
            'start_date' => $start,
            'end_date'   => $end,
        ]);

        $this->dispatch('reloadCalendar');
        return true;
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

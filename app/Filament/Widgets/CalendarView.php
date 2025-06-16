<?php

namespace App\Filament\Widgets;

use App\Models\Intervention;
use Filament\Forms\Components\Select;
use \Guava\Calendar\Widgets\CalendarWidget;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CalendarView extends CalendarWidget
{
   //protected string $calendarView = 'resourceTimeGridWeek';
    protected string|\Closure|HtmlString|null $heading = 'Planning des interventions';
    
  protected bool $dateClickEnabled = true;
  protected bool $dateSelectEnabled = true;
  protected bool $noEventsClickEnabled = true;
  protected bool $eventClickEnabled = true;
  protected bool $allDay = true;
  protected ?string $defaultEventClickAction = 'view'; 
    
  public $technicien;
  public $status;

  protected $listeners = ['reloadCalendar' => '$refresh'];

    
    /**
     * Get the options for the calendar.
     *
     * @return array
     */
   public function getOptions(): array
    {
        return [
            'title' => 'Planing',
            'nowIndicator' => '',
            'slotDuration' => '00:15:00',
            'allDay' => false
        ];
    }

    

    public function getEvents(array $fetchInfo = []): Collection | array
    {
           $start = $fetchInfo['start'] ?? now()->startOfMonth();
    $end = $fetchInfo['end'] ?? now()->endOfMonth();

    return Intervention::whereBetween('start_date', [$start, $end])
        ->when($this->technicien, fn($q) => $q->where('user_id', $this->technicien))
        ->when($this->status, fn($q) => $q->where('status', $this->status))
        ->get()
        ->map(fn(Intervention $intervention) => $intervention->toCalendarEvent());
    }

     public static function refresh()
    {
        // Provide an empty array as the default argument
        return (new self())->getEvents([]);
    }



    /**
     * Handle the event click action.
     *
     * @param array $info
     * @param string|null $action
     * @return void
     */
        public function onEventClick(array $info = [], ?string $action = null): void
        {
           $id = $info['event']['extendedProps']['key'];
           redirect()->route('admin.interventions', ['id' => $id,
        'data-target' => '_blank',]);
        }

    /**
     * Handle the date click event.
     *
     * @param array $info
     * @return void
     */
    public function onDateClick(array $info = []): void
    {
        
    }

    /**
     * Handle the date select event.
     *
     * @param array $info
     * @return void
     */
    public function onDateSelect(array $info = []): void
    {
        dd('kok'.$info);
    }

    /**
     * Handle the no events click event.
     *
     * @param array $info
     * @return void
     */
    public function onNoEventsClick($info): void
    {
        dd('top'.$info);
    }

    public function getEventContent(): null|string|array
    {
        // return a blade view
        return view('components.calendar-event');
    
        // return a HtmlString
        //dd($intervention);
        //return new HtmlString('<div>My event</div>');
    }

}

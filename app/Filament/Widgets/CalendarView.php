<?php

namespace App\Filament\Widgets;

use App\Models\Intervention;
use \Guava\Calendar\Widgets\CalendarWidget;

use Illuminate\Support\Collection;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Support\HtmlString;

class CalendarView extends CalendarWidget
{
   //protected string $calendarView = 'resourceTimeGridWeek';
   //protected string $heading = 'My Calendar';
    
  protected bool $dateClickEnabled = true;
  protected bool $dateSelectEnabled = true;
  protected bool $noEventsClickEnabled = true;
  protected bool $eventClickEnabled = true;
  protected ?string $defaultEventClickAction = 'view'; // 'view' or 'edit'
    
     /**
      * Get the options for the calendar.
      */

   public function getOptions(): array
    {
        return [
            'nowIndicator' => true,
            'slotDuration' => '00:15:00'
        ];
    }

    public function getEvents(array $fetchInfo = []): Collection | array
    {
        return Intervention::whereMonth('date_planifiee', now()->month)->get()->map(fn(Intervention $intervention) => $intervention->toCalendarEvent());
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
        dd($info);
    }

    /**
     * Handle the date select event.
     *
     * @param array $info
     * @return void
     */
    public function onDateSelect(array $info = []): void
    {
        dd($info);
    }

    /**
     * Handle the no events click event.
     *
     * @param array $info
     * @return void
     */
    public function onNoEventsClick($info): void
    {
        dd($info);
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

<?php

namespace App\Filament\Pages;

use App\Enum\InterventionStatus;
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

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
     protected static ?string $title = 'Planning des interventions';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.canlendar-page';

    public function getFooterWidgets(): array
    {
        return [
            CalendarView::class,
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
                    Select::make('technician_id')
                        ->label('Filtrer par technicien')
                        ->options($technicians)
                        ->searchable()
                        ->placeholder('Sélectionnez un technicien'),

                    Select::make('status')
                        ->label('Filtrer par statut')
                        ->options(collect(InterventionStatus::cases())
                        ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                        ->toArray())
                        ->searchable()
                        ->placeholder('Sélectionnez un statut'),
                ])->columns(2),

            ]);
    }
}

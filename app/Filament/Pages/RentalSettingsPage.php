<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Models\Customer;
use App\Services\RentalReportService;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class RentalSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $navigationLabel = 'Paramètres location';
    protected static ?string $title = 'Paramètres du rapport de location';
    protected static string $view = 'filament.pages.rental-settings-page';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_RentalSettingsPage');
    }

    public function mount(): void
    {
        $customers = (array) AppSetting::get(RentalReportService::SETTING_CUSTOMERS, []);
        $detected  = array_flip(array_map(fn ($u) => $u->value, RentalReportService::usageCustomers()));

        $this->form->fill([
            'coefficient' => RentalReportService::coefficient(),
            'customer_P'  => $customers['P'] ?? $detected['P'] ?? null,
            'customer_M'  => $customers['M'] ?? $detected['M'] ?? null,
            'customer_D'  => $customers['D'] ?? $detected['D'] ?? null,
        ]);
    }

    public function form(Form $form): Form
    {
        $customerSelect = fn (string $name, string $label, string $help) => Select::make($name)
            ->label($label)
            ->helperText($help)
            ->searchable()
            ->getSearchResultsUsing(fn (string $search) => Customer::query()->where('name', 'like', "%{$search}%")->limit(30)->pluck('name', 'id'))
            ->getOptionLabelUsing(fn ($value) => Customer::query()->find($value)?->name);

        return $form
            ->statePath('data')
            ->schema([
                Section::make('Amortissement')
                    ->schema([
                        TextInput::make('coefficient')
                            ->label('Coefficient frais généraux et frais de siège')
                            ->helperText('Seuil d\'amortissement = prix de revient HT × coefficient (ex. 1,50).')
                            ->numeric()
                            ->minValue(1)
                            ->step(0.01)
                            ->required(),
                    ]),

                Section::make('Clients dédiés (créés dans Odoo)')
                    ->description('Les commandes de location passées à ces clients sont classées Prêt / Maintenance / Dotation dans le planning ; toutes les autres sont des locations.')
                    ->columns(3)
                    ->schema([
                        $customerSelect('customer_P', 'Prêt (P)', 'Ex. « PRÊT GE »'),
                        $customerSelect('customer_M', 'Maintenance (M)', 'Ex. « MAINTENANCE GE »'),
                        $customerSelect('customer_D', 'Dotations – RP (D)', 'Ex. « DOTATIONS - RP GE »'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::put(RentalReportService::SETTING_COEFFICIENT, (float) $data['coefficient']);
        AppSetting::put(RentalReportService::SETTING_CUSTOMERS, array_filter([
            'P' => $data['customer_P'] ?? null,
            'M' => $data['customer_M'] ?? null,
            'D' => $data['customer_D'] ?? null,
        ]));

        Notification::make()->title('Paramètres enregistrés')->success()->send();
    }
}

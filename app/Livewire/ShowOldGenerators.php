<?php

namespace App\Livewire;

use App\Models\ContractGenerator;
use App\Models\DevisGenerator;
use App\Models\Generator;
use App\Models\Shop\Product;
use App\Utils\NumberUtils;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ShowOldGenerators extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public  $contractId;

    public function mount($record): void
    {
        $this->contractId = $record;
    }

    public function table(Table $table): Table
    {
        $model = ContractGenerator::where('contract_id', $this->contractId)
        ->where('old_generator_id','<>', null)
        ->with('oldGenerator');

        if(str_contains(request()->url(), 'devis')) {
            $model = DevisGenerator::where('devis_id', $this->contractId)
             ->where('old_generator_id','<>', null)
             ->with('oldGenerator');
        }
        return $table
            ->heading('Groupes électrogènes remplacés')
            ->query($model)
            ->columns([
                TextColumn::make('oldGenerator.name')
                    ->label('GE')
                    ->sortable()
                       ->copyable()
                    ->searchable(),
                TextColumn::make('oldGenerator.power')
                    ->label('Puissance ')
                    ->getStateUsing(fn($record) => NumberUtils::format($record->generator?->power) . " kVA")
                    ->searchable(),

                TextColumn::make('site')
                    ->label('Site ')
                    ->searchable(),

                TextColumn::make('forfait')
                    ->label('Forfait de maintenance')
                    ->formatStateUsing(fn($state) => NumberUtils::format($state) . ' FCFA')
                    ->columnSpanFull()
                    ->visible(fn() => str_contains(request()->url(), 'contract')),
            ])
            ->recordUrl(function ($record) {
                if (str_contains(request()->url(), 'devis')) {
                    return url('/admin/generators/'. $record->old_generator_id);
                }
                return url('/admin/contract-generators/'. $record->old_generator_id);
            })
            ->filters([
                // ...
            ]);
    }
    public function render()
    {
        return view('livewire.show-old-generators');
    }
}

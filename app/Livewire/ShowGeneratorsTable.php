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

class ShowGeneratorsTable extends Component implements HasForms, HasTable
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
        $model = ContractGenerator::where('contract_id', $this->contractId)->with('generator');

        if(str_contains(request()->url(), 'devis')) {
            $model = DevisGenerator::where('devis_id', $this->contractId)->with('generator');
        }
        return $table
            ->heading('Groupes électrogènes')
            ->query($model)
            ->columns([
                TextColumn::make('generator.name')
                    ->label('GE')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('generator.power')
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
                    return url('/admin/generators/'. $record->generator_id);
                }
                return url('/admin/contract-generators/'. $record->generator_id);
            })
            ->filters([
                // ...
            ]);
    }

    public function render(): View
    {
        return view('livewire.show-generators-table');
    }
}
// namespace App\Livewire;

// use Collator;
// use Filament\Tables\Actions\Contracts\HasTable;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Concerns\InteractsWithTable;
// use Filament\Tables\Table;
// use Livewire\Component;

// class ShowGeneratorsTable extends Component implements HasTable
// {
//    use InteractsWithTable;
  

//     public  $data;

//     public function mount($record): void
//     {
//         $this->data = $record;
//         dd($this->data);
//     }
//    protected function getTableRecords()
//     {
//         return $this->data;
//     }

// public function table(Table $table): Table
//     {
//         return $table
//             ->query(Product::query())
//             ->columns([
//                 TextColumn::make('name'),
//             ])
//             ->filters([
//                 // ...
//             ])
//             ->actions([
//                 // ...
//             ])
//             ->bulkActions([
//                 // ...
//             ]);
//     }
    
//     public function render()
//     {
//        
//     }
// }

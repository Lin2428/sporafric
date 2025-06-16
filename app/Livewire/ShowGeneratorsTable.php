<?php

namespace App\Livewire;

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
use Livewire\Component;

class ShowGeneratorsTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    
    public array  $data;

    public function mount($record): void
    {
        $this->data = $record->pluck('id')->toArray();
    }
    
    public function table(Table $table): Table
    {
        return $table
        ->heading('Groupes électrogènes')
            ->query(Generator::query()->whereIn('id', $this->data))
            ->columns([
                TextColumn::make('name')
                    ->label('Générateur')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('reference')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->label('Numéro de série')
                    ->searchable(),
                TextColumn::make('power')
                    ->label('Puissance ')
                    ->getStateUsing(fn($record) => NumberUtils::format($record->power) . " KVA")
                    ->searchable(),
            ])
            ->recordUrl(function ($record){
                if(str_contains(request()->url(), 'devis')){
                    return url('/admin/generators', $record->id);
                }
                 return url('/admin/contract-generators', $record->id);
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

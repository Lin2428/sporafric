<?php

namespace App\Filament\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Utils\BadgetWidget;
use App\Models\Generator;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class GeneratorInRevision extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = ' ';
     protected static ?string $model = Generator::class;

     public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(static::$model::query()
            ->where('status', '=', GeneratorStatus::EN_REVU->value)
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->default('generateur.png')
                    ->extraAttributes(['style' => 'width: 100px, height: 100px;']),

                TextColumn::make('name')
                    ->label('Identification du GE')
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->getStateUsing(function (Generator $record) {
                        $status = GeneratorStatus::from($record->status)->label();
                        return BadgetWidget::generatorStatusBadget($status);
                    })
                    ->html(),

                TextColumn::make('houres')
                    ->label('Nb H')
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('next_vidange')
                    ->label('P vidange')
                    ->sortable()
                    ->extraAttributes(['style' => 'font-weight: bold; '])
                    ->searchable(),

                TextColumn::make('start-up')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable(),

            ])
            ->recordUrl(fn($record) => url('admin/generators/'.$record->id))
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                    ->url(fn($record) => url('admin/generators/'.$record->id)),
                    EditAction::make()
                    ->url(fn($record) => url('admin/generators/'.$record->id.'/edit')),
                ]), 
            ])
            ->bulkActions([
                // ...
            ]);
    }

    protected static string $view = 'filament.pages.generator-in-revision';
}

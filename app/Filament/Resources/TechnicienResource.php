<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicienResource\Pages;
use App\Filament\Resources\TechnicienResource\RelationManagers;
use App\Models\Technicien;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TechnicienResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Technicien::class;

    protected static ?string $label           = "Techniciens";
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Global';
    protected static ?string $navigationLabel = 'Techniciens';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom & Prénom')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                FileUpload::make('photo')
                    ->label('Photo')
                    ->directory('techniciens')
                    ->visibility('public')
                    ->enableOpen()
                    ->enableDownload()
                    ->preserveFilenames()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->rounded()
                    ->size(50)
                    ->default('technicien.png'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom & Prénom')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('job')
                    ->label('Fonction')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\ViewAction::make()
                //     ->modalWidth('md'),
                //     Tables\Actions\EditAction::make()
                //     ->modalWidth('md'),
                //     Tables\Actions\DeleteAction::make(),
                // ])
                Tables\Actions\EditAction::make()
                    ->modalWidth('md'),
                Tables\Actions\Action::make('create-user')
                    ->label("Créer  l'utilisateur")
                    ->color("info")
                    ->visible(fn(Technicien $record): bool => ! $record->user()->exists())
                    ->url(fn(Technicien $record): string => UserResource::getUrl('create', [
                        'technicien_id' => $record->id,
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechniciens::route('/'),
            // 'create' => Pages\CreateTechnicien::route('/create'),
            // 'edit' => Pages\EditTechnicien::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'cancell',
            'delete_any',
            'delete',
        ];
    }
}

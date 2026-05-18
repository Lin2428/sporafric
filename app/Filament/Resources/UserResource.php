<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Technicien;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Rawilk\FilamentPasswordInput\Password;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = "Paramètres";
    protected static ?string $navigationLabel = "Utilisateurs";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columns(3)
                    ->schema([
                        Section::make("")
                            ->schema([
                                Hidden::make('technicien_id')
                                    ->default(fn(): ?int => self::getTechnicienFromRequest()?->id),

                                TextInput::make("name")
                                    ->label("Nom")
                                    ->columnSpanFull()
                                    ->default(fn(): ?string => self::getTechnicienFromRequest()?->name)
                                    ->required(),

                                TextInput::make("email")
                                    ->label("Email")
                                    ->unique(ignoreRecord: true)
                                    ->default(fn(): ?string => self::getTechnicienFromRequest()?->email)
                                    ->required(),

                                TextInput::make("phone")
                                    ->label("Téléphone")
                                    ->numeric()
                                    ->default(fn(): ?string => self::getTechnicienFromRequest()?->phone)
                                    ->unique(ignoreRecord: true)
                                    ->helperText("Necessaire pour la connexion sur tablette"),

                                Select::make("roles")
                                    ->relationship("roles", "name")
                                    ->required()
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->columnSpanFull(),

                                Password::make('password')
                                    ->label('Mot de passe')
                                    ->copyMessage('Copied in clipboard')
                                    ->regeneratePassword()
                                    ->copyable()
                                    ->required(fn(Page $livewire): bool => $livewire instanceof Pages\CreateUser)
                                    ->minLength(8)
                                    ->columnSpanFull()
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state)),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("created_at")
                    ->label("Création")
                    ->dateTime("d/m/Y H:i"),

                TextColumn::make("name")
                    ->label("Nom")
                    ->searchable(),

                TextColumn::make("email")
                    ->label("Email")
                    ->searchable(),

                TextColumn::make("phone")
                    ->label("Phone")
                    ->searchable(),

                BadgeColumn::make("roles.name")
                    ->label("Roles")
                    ->searchable(),

                // BadgeColumn::make("status")
                // ->label("Statut")
                // ->getStateUsing(function (User $record) {
                //     return $record->status ? 'Actif' : 'Inactif';
                // })
                // ->colors([
                //     'success' => 'Actif',
                //     'Danger' => 'Inactif',
                // ]),

                ToggleColumn::make("status")
                    ->label("Statut"),

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Impersonate::make(),
                    Tables\Actions\DeleteAction::make()->visible(fn(User $user) =>  $user->isSuperAdmin()),
                ]),

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

    private static function getTechnicienFromRequest(): ?Technicien
    {
        $technicienId = request()->integer('technicien_id');

        if (! $technicienId) {
            return null;
        }

        return Technicien::query()
            ->whereDoesntHave('user')
            ->find($technicienId);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete_any',
            'cancell',
        ];
    }
}

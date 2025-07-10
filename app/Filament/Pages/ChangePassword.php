<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Pages\Page;
use Rawilk\FilamentPasswordInput\Password;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static string $view = 'filament.pages.change-password';
    protected static ?string $navigationGroup = "Paramètres";
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Changer mon mot de passe';

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
           Group::make()
                ->schema([
                   Section::make('Changer mon mot de passe')
                        ->schema([
                            TextInput::make('current_password')
                                ->label('Mot de passe actuel')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Password::make('new_password')
                                    ->label('Password')
                                    ->copyMessage('Copied in clipboard')
                                    ->regeneratePassword()
                                    ->copyable()
                                    ->required(fn(Page $livewire): bool => $livewire instanceof Pages\CreateUser)
                                    ->maxLength(8)
                                    ->columnSpanFull()
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state)),

                            TextInput::make('new_password_confirmation')
                                ->label('Confirmer le nouveau mot de passe')
                                ->password()
                                ->required()
                                ->same('new_password')
                                ->minLength(8)
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])->columnSpan(4),
                ])
                ->columns(7),
        ];
    }

    public function submit()
    {
        $user = auth()->user();

        if (!$user || !is_string($user->password) || !Hash::check($this->current_password, (string) $user->password)) {
            Notification::make()
                ->title('Mot de passe actuel incorrect')
                ->danger()
                ->send();

            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        Notification::make()
            ->title('Mot de passe changé avec succès')
            ->success()
            ->send();


        $this->form->fill([]);
    }

    protected function getFormActions(): array
    {
        return [
        Action::make('submit')
                ->label('Mettre à jour')
                ->submit('submit'),
        ];
    }
}

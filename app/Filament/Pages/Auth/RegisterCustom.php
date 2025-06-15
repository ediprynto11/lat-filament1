<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class RegisterCustom extends Register
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'name'),

                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->unique(User::class, 'email'),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->revealable()
                            ->password()
                            ->required()
                            ->confirmed()
                            ->minLength(8),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->revealable()
                            ->password()
                            ->required(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function throwValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Email ini sudah digunakan.',
            'data.name' => 'Nama pengguna ini sudah terdaftar.',
        ]);
    }
}

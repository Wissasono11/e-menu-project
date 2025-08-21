<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('logo')
                    ->label('Logo Toko')
                    ->image()
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Toko')
                    ->required(),
                TextInput::make('username')
                    ->label('Username')
                    ->hint('Minimal 5 karakter, dan tidak ada spasi')
                    ->minLength(5)
                    ->unique(ignoreRecord: true)
                    ->required(),
                    TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Admin',
                        'store' => 'Toko',
                    ])
                    ->required(),
            ]);
    }
}

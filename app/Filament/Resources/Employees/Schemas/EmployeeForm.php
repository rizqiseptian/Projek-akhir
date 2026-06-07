<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->placeholder('e.g. John Doe')
                    ->maxLength(255)
                    ->helperText('Enter the employee\'s full name'),

                TextInput::make('rfid_uid')
                    ->label('RFID Card ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autofocus()
                    ->helperText('Tap the ID card on the reader to populate this field')
                    ->extraInputAttributes(['autocomplete' => 'off'])
                    ->prefixIcon('heroicon-o-credit-card'),

                ViewField::make('face_descriptor')
                    ->label('Face Capture')
                    ->required()
                    ->view('filament.forms.components.face-capture')
                    ->columnSpanFull(),

                Hidden::make('is_admin')
                    ->default(false),
            ])
            ->columns(2);
    }
}
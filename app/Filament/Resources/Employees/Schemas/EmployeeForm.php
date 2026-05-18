<?php

namespace App\Filament\Resources\Employees\Schemas;

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
                    ->label('Employee Name')
                    ->required()
                    ->placeholder('e.g. John Doe')
                    ->maxLength(255),
                
                TextInput::make('rfid_uid')
                    ->label('Scan RFID Card')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autofocus() // Focuses the cursor here automatically
                    ->helperText('Please tap the ID card on the reader to populate this field.')
                    ->extraInputAttributes(['autocomplete' => 'off']), // Stops browser suggestions
                    
                ViewField::make('face_descriptor')
                    ->label('Facial Registration')
                    ->required()
                    ->view('filament.forms.components.face-capture')
                    ->columnSpanFull(),
            ]);
    }
}
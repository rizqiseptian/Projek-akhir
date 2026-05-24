<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Admin Name')
                    ->required()
                    ->placeholder('e.g. Jane Doe')
                    ->maxLength(255),
                
                TextInput::make('rfid_uid')
                    ->label('Scan RFID Card')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autofocus()
                    ->helperText('Please tap the ID card on the reader to populate this field.')
                    ->extraInputAttributes(['autocomplete' => 'off']),
                    
                ViewField::make('face_descriptor')
                    ->label('Facial Registration')
                    ->required()
                    ->view('filament.forms.components.face-capture')
                    ->columnSpanFull(),

                Hidden::make('is_admin')
                    ->default(true),
            ]);
    }
}

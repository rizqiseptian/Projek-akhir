<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->placeholder('e.g. Jane Doe')
                    ->maxLength(255)
                    ->helperText('Enter the administrator\'s full name'),

                TextInput::make('rfid_uid')
                    ->label('RFID Card ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autofocus()
                    ->helperText('Tap the ID card on the reader to populate this field')
                    ->extraInputAttributes(['autocomplete' => 'off'])
                    ->prefixIcon('heroicon-o-credit-card'),

                TextInput::make('whatsapp_number')
                    ->label('WhatsApp Number')
                    ->required()
                    ->tel()
                    ->placeholder('+6281234567890')
                    ->maxLength(20)
                    ->helperText('Required for emergency OTP delivery via WhatsApp')
                    ->prefixIcon('heroicon-o-device-phone-mobile'),

                ViewField::make('face_descriptor')
                    ->label('Face Capture')
                    ->required()
                    ->view('filament.forms.components.face-capture')
                    ->columnSpanFull(),

                Hidden::make('is_admin')
                    ->default(true),
            ])
            ->columns(2);
    }
}

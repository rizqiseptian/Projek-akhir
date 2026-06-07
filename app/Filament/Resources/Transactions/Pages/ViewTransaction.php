<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Transaction Details')
                    ->schema([
                        Text::make(fn (Transaction $record) => 'Employee: ' . ($record->employee?->name ?? '-'))
                            ->weight('medium'),

                        Text::make(fn (Transaction $record) => 'Total Amount: $' . number_format($record->total_amount ?? 0, 2))
                            ->weight('bold')
                            ->color('primary'),

                        Text::make(fn (Transaction $record) => 'Cash Paid: $' . number_format($record->cash_paid ?? 0, 2)),

                        Text::make(fn (Transaction $record) => 'Change Returned: $' . number_format($record->change_returned ?? 0, 2))
                            ->color('success'),

                        Text::make(fn (Transaction $record) => 'Transaction Date: ' . ($record->created_at?->format('M d, Y H:i:s') ?? '-')),

                        Text::make(fn (Transaction $record) => 'Last Updated: ' . ($record->updated_at?->format('M d, Y H:i:s') ?? '-')),
                    ])
                    ->columns(2),

                Section::make('Transaction Items')
                    ->schema([
                        Text::make(fn (Transaction $record) => $record->items->map(fn ($item) => "{$item->description} - $" . number_format($item->price, 2))->join("\n"))->size('sm')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}

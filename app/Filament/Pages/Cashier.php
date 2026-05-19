<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Cashier extends Page
{
    protected string $view = 'filament.pages.cashier';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    public $cart = [];
    public $newItemDescription = '';
    public $newItemPrice = '';
    public $cashPaid = '';

    public function addItem()
    {
        if (empty($this->newItemDescription) || !is_numeric($this->newItemPrice) || $this->newItemPrice <= 0) {
            Notification::make()
                ->title('Invalid Input')
                ->body('Please enter a valid item name and price.')
                ->danger()
                ->send();
            return;
        }

        $this->cart[] = [
            'description' => $this->newItemDescription,
            'price' => (float) $this->newItemPrice,
        ];

        $this->reset(['newItemDescription', 'newItemPrice']);
    }

    public function removeItem($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Re-index array
        }
    }

    public function getTotalProperty()
    {
        return array_sum(array_column($this->cart, 'price'));
    }

    public function getChangeProperty()
    {
        $cash = (float) ($this->cashPaid ?: 0);
        return max(0, $cash - $this->total);
    }

    public function completeTransaction()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Empty Cart')
                ->body('Cannot complete an empty transaction.')
                ->warning()
                ->send();
            return;
        }

        $cash = (float) ($this->cashPaid ?: 0);
        if ($cash < $this->total) {
            Notification::make()
                ->title('Insufficient Cash')
                ->body('The cash paid is less than the total amount.')
                ->danger()
                ->send();
            return;
        }

        $transaction = Transaction::create([
            'employee_id' => Auth::id(),
            'total_amount' => $this->total,
            'cash_paid' => $cash,
            'change_returned' => $this->change,
        ]);

        foreach ($this->cart as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'description' => $item['description'],
                'price' => $item['price'],
            ]);
        }

        Notification::make()
            ->title('Transaction Complete')
            ->body('Transaction saved successfully. Change: $' . number_format($this->change, 2))
            ->success()
            ->send();

        $this->reset(['cart', 'newItemDescription', 'newItemPrice', 'cashPaid']);
    }
}

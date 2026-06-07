<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;

class Cashier extends Component
{
    public $cart = [];
    public $newItemDescription = '';
    public $newItemPrice = '';
    public $cashPaid = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $transactions = [];

    public function mount()
    {
        $this->loadTransactions();
    }

    private function loadTransactions()
    {
        $this->transactions = Transaction::where('employee_id', Auth::id() ?? 1)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function addItem()
    {
        $this->resetMessages();

        if (empty($this->newItemDescription) || !is_numeric($this->newItemPrice) || $this->newItemPrice <= 0) {
            $this->errorMessage = 'Please enter a valid item name and price.';
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
        $this->resetMessages();
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

    private function resetMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function completeTransaction()
    {
        $this->resetMessages();

        if (empty($this->cart)) {
            $this->errorMessage = 'Cannot complete an empty transaction.';
            return;
        }

        $cash = (float) ($this->cashPaid ?: 0);
        if ($cash < $this->total) {
            $this->errorMessage = 'The cash paid is less than the total amount.';
            return;
        }

        $transaction = Transaction::create([
            'employee_id' => Auth::id() ?? 1, // Fallback if no auth for standalone route
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

        $this->successMessage = 'Transaction saved successfully. Change: $' . number_format($this->change, 2);
        $this->reset(['cart', 'newItemDescription', 'newItemPrice', 'cashPaid']);
        $this->loadTransactions();
    }

    public function render()
    {
        return view('livewire.cashier')->layout('layouts.app');
    }
}

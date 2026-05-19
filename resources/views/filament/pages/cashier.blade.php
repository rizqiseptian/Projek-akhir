<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column: Cart Items -->
        <div class="md:col-span-2 space-y-4">
            <x-filament::section>
                <x-slot name="heading">
                    Current Transaction
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Item Description</th>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-right">Price</th>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($cart as $index => $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item['description'] }}</td>
                                    <td class="px-4 py-3 text-right font-mono">${{ number_format($item['price'], 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <x-filament::icon-button
                                            icon="heroicon-o-trash"
                                            color="danger"
                                            wire:click="removeItem({{ $index }})"
                                            tooltip="Remove Item"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                        No items added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($cart) > 0)
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td class="px-4 py-3 font-bold text-right">TOTAL</td>
                                    <td class="px-4 py-3 font-bold text-right font-mono text-lg text-primary-600 dark:text-primary-400">${{ number_format($this->total, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </x-filament::section>
        </div>

        <!-- Right Column: Add Item & Checkout -->
        <div class="space-y-6">
            <!-- Add Item Form -->
            <x-filament::section>
                <x-slot name="heading">
                    Add Item
                </x-slot>

                <form wire:submit="addItem" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="description">Item Name</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model="newItemDescription"
                                id="description"
                                placeholder="e.g. Coffee"
                                required
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="price">Price</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                step="0.01"
                                wire:model="newItemPrice"
                                id="price"
                                placeholder="0.00"
                                required
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <x-filament::button type="submit" class="w-full">
                        Add to Order
                    </x-filament::button>
                </form>
            </x-filament::section>

            <!-- Checkout / Payment -->
            <x-filament::section>
                <x-slot name="heading">
                    Checkout
                </x-slot>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="cash">Cash Paid</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                step="0.01"
                                wire:model.live="cashPaid"
                                id="cash"
                                placeholder="0.00"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Due:</span>
                            <span class="text-lg font-bold font-mono">${{ number_format($this->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Change:</span>
                            <span class="text-2xl font-black font-mono @if($this->change > 0) text-success-600 dark:text-success-400 @else text-gray-400 @endif">
                                ${{ number_format($this->change, 2) }}
                            </span>
                        </div>
                    </div>

                    <x-filament::button 
                        color="success" 
                        class="w-full" 
                        size="lg"
                        wire:click="completeTransaction"
                        :disabled="empty($cart) || (float)$cashPaid < $this->total"
                    >
                        Complete Transaction
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>

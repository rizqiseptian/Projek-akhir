<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900">Checkout Terminal</h1>
        <p class="mt-2 text-sm text-gray-600">Scan items or input manually to calculate totals and process transactions.</p>
    </div>

    @if ($successMessage)
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Cart Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Keranjang belanja</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ count($cart) }} barang
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail barang</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($cart as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $item['description'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-mono">${{ number_format($item['price'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 focus:outline-none transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500 bg-gray-50 italic">
                                        Belum ada barang yang ditambahkan. Gunakan form di sebelah kanan untuk menambahkan item ke keranjang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($cart) > 0)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 uppercase">Total</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold text-blue-600 font-mono">${{ number_format($this->total, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Add Item & Checkout -->
        <div class="space-y-6">
            
            <!-- Add Item Form -->
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Barang</h3>
                </div>
                <div class="px-6 py-5">
                    <form wire:submit.prevent="addItem" class="space-y-5">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <div class="mt-1">
                                <input type="text" wire:model="newItemDescription" id="description" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="e.g. Cafe Latte" required>
                            </div>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Harga ($)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" step="0.01" wire:model="newItemPrice" id="price" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="0.00" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Add to Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- Checkout / Payment -->
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment</h3>
                </div>
                <div class="px-6 py-5 space-y-6">
                    <div>
                        <label for="cash" class="block text-sm font-medium text-gray-700">Cash Received ($)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" wire:model.live="cashPaid" id="cash" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-xl font-bold border-gray-300 rounded-md py-3 px-3 border" placeholder="0.00">
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-5 text-white shadow-inner">
                        <div class="flex justify-between items-center mb-3 border-b border-gray-700 pb-3">
                            <span class="text-sm font-medium text-gray-400">Total Due</span>
                            <span class="text-2xl font-bold font-mono">${{ number_format($this->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-400">Change Due</span>
                            <span class="text-3xl font-black font-mono @if($this->change > 0) text-green-400 @else text-gray-500 @endif">
                                ${{ number_format($this->change, 2) }}
                            </span>
                        </div>
                    </div>

                    <button 
                        wire:click="completeTransaction"
                        @if(empty($cart) || (float)$cashPaid < $this->total) disabled @endif
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-lg font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                    >
                        Complete Transaction
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
